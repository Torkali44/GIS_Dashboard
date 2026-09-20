<?php

namespace Tests\Feature;

use App\Models\ContractExpense;
use App\Models\ContractPayment;
use App\Models\PropertyHouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionHardeningTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_payment_from_another_contract_cannot_be_updated_or_deleted(): void
    {
        $houseA = PropertyHouse::create([
            'user_id' => $this->admin->id,
            'title' => 'عقد أ',
            'price' => 100,
        ]);
        $houseB = PropertyHouse::create([
            'user_id' => $this->admin->id,
            'title' => 'عقد ب',
            'price' => 200,
        ]);
        $payment = ContractPayment::create([
            'property_house_id' => $houseB->id,
            'amount' => 50,
            'payment_date' => '2026-09-18',
            'payment_method' => 'cash',
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.houses.payments.update', [$houseA, $payment]), [
                'amount' => 99,
                'payment_date' => '2026-09-19',
                'payment_method' => 'cash',
            ])
            ->assertNotFound();

        $this->actingAs($this->admin)
            ->delete(route('admin.houses.payments.destroy', [$houseA, $payment]))
            ->assertNotFound();

        $this->assertDatabaseHas('contract_payments', [
            'id' => $payment->id,
            'property_house_id' => $houseB->id,
            'amount' => 50,
        ]);
    }

    public function test_expense_from_another_contract_cannot_be_updated_or_deleted(): void
    {
        $houseA = PropertyHouse::create([
            'user_id' => $this->admin->id,
            'title' => 'عقد أ',
        ]);
        $houseB = PropertyHouse::create([
            'user_id' => $this->admin->id,
            'title' => 'عقد ب',
        ]);
        $expense = ContractExpense::create([
            'property_house_id' => $houseB->id,
            'expense_type' => 'salary',
            'amount' => 20,
            'expense_date' => '2026-09-18',
            'payment_method' => 'cash',
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.houses.expenses.update', [$houseA, $expense]), [
                'expense_type' => 'gas',
                'amount' => 99,
                'expense_date' => '2026-09-19',
                'payment_method' => 'cash',
            ])
            ->assertNotFound();

        $this->actingAs($this->admin)
            ->delete(route('admin.houses.expenses.destroy', [$houseA, $expense]))
            ->assertNotFound();

        $this->assertDatabaseHas('contract_expenses', [
            'id' => $expense->id,
            'property_house_id' => $houseB->id,
            'amount' => 20,
        ]);
    }

    public function test_duplicate_contract_number_is_rejected(): void
    {
        PropertyHouse::create([
            'user_id' => $this->admin->id,
            'title' => 'عقد أول',
            'contract_number' => 'GIS-2026-0100',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.houses.store'), [
                'title' => 'عقد مكرر',
                'contract_number' => 'GIS-2026-0100',
            ])
            ->assertSessionHasErrors('contract_number');
    }

    public function test_invalid_report_month_is_clamped_and_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reports.monthly', ['month' => 99, 'year' => 1990]))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('admin.reports.annual', ['year' => 0]))
            ->assertOk();
    }

    public function test_dashboard_and_excel_exports_load(): void
    {
        $house = PropertyHouse::create([
            'user_id' => $this->admin->id,
            'title' => 'عقد لوحة',
            'price' => 150,
        ]);
        ContractPayment::create([
            'property_house_id' => $house->id,
            'amount' => 50,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cash',
        ]);

        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.houses.index'))->assertOk();

        $this->actingAs($this->admin)
            ->get(route('admin.reports.monthly.excel'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->actingAs($this->admin)
            ->get(route('admin.reports.annual.excel'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_contract_notes_with_quotes_do_not_break_show_page(): void
    {
        $house = PropertyHouse::create([
            'user_id' => $this->admin->id,
            'title' => 'عقد XSS',
            'contract_number' => "GIS-2026-O'Brien",
            'price' => 100,
        ]);
        ContractPayment::create([
            'property_house_id' => $house->id,
            'amount' => 10,
            'payment_date' => '2026-09-18',
            'payment_method' => 'cash',
            'notes' => "دفعة 'تجريبية' </script><script>alert(1)</script>",
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.houses.show', $house))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_unknown_expense_type_is_rejected(): void
    {
        $house = PropertyHouse::create([
            'user_id' => $this->admin->id,
            'title' => 'عقد مصروف',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.houses.expenses.store', $house), [
                'expense_type' => 'inspector_fee',
                'amount' => 30,
                'expense_date' => '2026-09-18',
                'payment_method' => 'cash',
            ])
            ->assertSessionHasErrors('expense_type');
    }
}
