<?php

namespace Tests\Feature;

use App\Models\ContractExpense;
use App\Models\ContractPayment;
use App\Models\ExpenseAuditLog;
use App\Models\PropertyHouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractFinancialValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected PropertyHouse $house;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'admin@gis.bh',
            'is_admin' => true,
        ]);

        $this->house = PropertyHouse::create([
            'user_id' => $this->user->id,
            'title' => 'فيلا تجريبية الرفاع',
            'contract_number' => 'GIS-2026-TEST',
            'client_name' => 'محمد أحمد',
            'buyer_name' => 'محمد أحمد',
            'phone' => '33445566',
            'price' => 200.00,
            'contract_status' => 'active',
        ]);
    }

    public function test_payment_validation_fails_on_missing_or_invalid_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.houses.payments.store', $this->house), [
                'amount' => '',
                'payment_date' => 'invalid-date',
                'payment_method' => 'unsupported_method',
            ]);

        $response->assertSessionHasErrors([
            'amount' => 'حقل مبلغ الدفعة مطلوب.',
            'payment_date' => 'صيغة تاريخ الدفعة غير صحيحة.',
            'payment_method' => 'طريقة الدفع المختارة غير صالحة.',
        ]);
    }

    public function test_payment_validation_fails_on_negative_or_zero_amount(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.houses.payments.store', $this->house), [
                'amount' => 0,
                'payment_date' => '2026-09-18',
                'payment_method' => 'benefit',
            ]);

        $response->assertSessionHasErrors([
            'amount' => 'يجب أن يكون المبلغ أكبر من صفر (0.01 على الأقل).',
        ]);
    }

    public function test_valid_payment_can_be_stored_and_updates_financials(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.houses.payments.store', $this->house), [
                'amount' => 100.00,
                'payment_date' => '2026-09-18',
                'payment_method' => 'benefit',
                'notes' => 'الدفعة الأولى كاش / بنفت',
            ]);

        $response->assertRedirect(route('admin.houses.show', $this->house));
        $this->assertDatabaseHas('contract_payments', [
            'property_house_id' => $this->house->id,
            'amount' => 100.00,
            'payment_method' => 'benefit',
        ]);

        $this->house->refresh();
        $this->assertEquals(100.00, $this->house->total_paid);
        $this->assertEquals(100.00, $this->house->remaining_amount);
        $this->assertEquals('partial', $this->house->payment_status);
    }

    public function test_payment_can_be_updated_and_deleted(): void
    {
        $payment = ContractPayment::create([
            'property_house_id' => $this->house->id,
            'amount' => 50.00,
            'payment_date' => '2026-09-15',
            'payment_method' => 'cash',
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('admin.houses.payments.update', [$this->house, $payment]), [
                'amount' => 150.00,
                'payment_date' => '2026-09-16',
                'payment_method' => 'benefit',
                'notes' => 'تم التعديل',
            ]);

        $response->assertRedirect(route('admin.houses.show', $this->house));
        $this->assertDatabaseHas('contract_payments', [
            'id' => $payment->id,
            'amount' => 150.00,
            'payment_method' => 'benefit',
        ]);

        // Delete test
        $delResponse = $this->actingAs($this->user)
            ->delete(route('admin.houses.payments.destroy', [$this->house, $payment]));

        $delResponse->assertRedirect(route('admin.houses.show', $this->house));
        $this->assertDatabaseMissing('contract_payments', ['id' => $payment->id]);
    }

    public function test_expense_validation_fails_on_missing_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.houses.expenses.store', $this->house), [
                'expense_type' => '',
                'amount' => -10,
                'expense_date' => 'bad-date',
                'payment_method' => 'unknown',
            ]);

        $response->assertSessionHasErrors([
            'expense_type' => 'يرجى اختيار نوع المصروف.',
            'amount' => 'يجب أن يكون المبلغ أكبر من صفر (0.01 على الأقل).',
            'expense_date' => 'صيغة تاريخ المصروف غير صحيحة.',
            'payment_method' => 'طريقة الدفع المختارة غير صالحة.',
        ]);
    }

    public function test_valid_expense_stores_and_creates_audit_log(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.houses.expenses.store', $this->house), [
                'expense_type' => 'inspector_fee',
                'amount' => 30.00,
                'expense_date' => '2026-09-18',
                'payment_method' => 'benefit',
                'payee_name' => 'المهندس الفاحص',
                'notes' => 'أتعاب فحص ميداني',
            ]);

        $response->assertRedirect(route('admin.houses.show', $this->house));
        $this->assertDatabaseHas('contract_expenses', [
            'property_house_id' => $this->house->id,
            'expense_type' => 'inspector_fee',
            'amount' => 30.00,
            'payee_name' => 'المهندس الفاحص',
        ]);

        $this->assertDatabaseHas('expense_audit_logs', [
            'user_id' => $this->user->id,
            'action' => 'created',
        ]);

        $this->house->refresh();
        $this->assertEquals(30.00, $this->house->total_expenses);
    }

    public function test_financial_calculations_complete_cycle(): void
    {
        // House price is 200.00
        // Add payment 1: 100.00
        ContractPayment::create([
            'property_house_id' => $this->house->id,
            'amount' => 100.00,
            'payment_date' => '2026-09-10',
            'payment_method' => 'cash',
        ]);

        // Add payment 2: 100.00 (Total paid = 200.00)
        ContractPayment::create([
            'property_house_id' => $this->house->id,
            'amount' => 100.00,
            'payment_date' => '2026-09-15',
            'payment_method' => 'benefit',
        ]);

        // Add expense 1: 30.00
        ContractExpense::create([
            'property_house_id' => $this->house->id,
            'expense_type' => 'inspector_fee',
            'amount' => 30.00,
            'expense_date' => '2026-09-12',
            'payment_method' => 'benefit',
        ]);

        // Add expense 2: 20.00 (Total expenses = 50.00)
        ContractExpense::create([
            'property_house_id' => $this->house->id,
            'expense_type' => 'fuel_transport',
            'amount' => 20.00,
            'expense_date' => '2026-09-12',
            'payment_method' => 'cash',
        ]);

        $this->house->refresh();

        $this->assertEquals(200.00, $this->house->total_paid);
        $this->assertEquals(0.00, $this->house->remaining_amount);
        $this->assertEquals(50.00, $this->house->total_expenses);
        $this->assertEquals(150.00, $this->house->net_profit); // 200 paid - 50 expenses = 150
        $this->assertEquals('paid', $this->house->payment_status);
    }

    public function test_house_validation_rules_and_arabic_messages(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.houses.store'), [
                'title' => '',
                'price' => -50,
                'inspection_date' => 'not-a-date',
            ]);

        $response->assertSessionHasErrors([
            'title' => 'حقل عنوان أو اسم العقار مطلوب.',
            'price' => 'يجب ألا تقل قيمة العقد عن صفر.',
            'inspection_date' => 'صيغة تاريخ الفحص غير صحيحة.',
        ]);
    }
}
