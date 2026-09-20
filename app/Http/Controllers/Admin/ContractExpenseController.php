<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractExpense;
use App\Models\ExpenseAuditLog;
use App\Models\PropertyHouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContractExpenseController extends Controller
{
    public function store(Request $request, PropertyHouse $house): RedirectResponse
    {
        $data = $request->validate($this->rules(), $this->messages());

        $expense = $house->expenses()->create($data);

        ExpenseAuditLog::create([
            'contract_expense_id' => $expense->id,
            'user_id' => $request->user()->id,
            'action' => 'created',
            'new_values' => $data,
        ]);

        return redirect()
            ->route('admin.houses.show', $house)
            ->with('status', 'تم تسجيل المصروف بنجاح بمبلغ '.number_format($data['amount'], 2).' د.ب');
    }

    public function update(Request $request, PropertyHouse $house, ContractExpense $expense): RedirectResponse
    {
        $this->ensureBelongsToHouse($house, $expense);

        $data = $request->validate($this->rules(), $this->messages());

        $oldValues = $expense->only(array_keys($data));

        $expense->update($data);

        ExpenseAuditLog::create([
            'contract_expense_id' => $expense->id,
            'user_id' => $request->user()->id,
            'action' => 'updated',
            'old_values' => $oldValues,
            'new_values' => $data,
        ]);

        return redirect()
            ->route('admin.houses.show', $house)
            ->with('status', 'تم تحديث المصروف بنجاح.');
    }

    public function destroy(Request $request, PropertyHouse $house, ContractExpense $expense): RedirectResponse
    {
        $this->ensureBelongsToHouse($house, $expense);

        ExpenseAuditLog::create([
            'contract_expense_id' => $expense->id,
            'user_id' => $request->user()->id,
            'action' => 'deleted',
            'old_values' => $expense->toArray(),
        ]);

        $expense->delete();

        return redirect()
            ->route('admin.houses.show', $house)
            ->with('status', 'تم حذف المصروف.');
    }

    private function ensureBelongsToHouse(PropertyHouse $house, ContractExpense $expense): void
    {
        abort_unless((int) $expense->property_house_id === (int) $house->id, 404);
    }

    private function rules(): array
    {
        return [
            'expense_type' => ['required', 'string', Rule::in(array_keys(ContractExpense::expenseTypes()))],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'in:cash,benefit,bank_transfer,other'],
            'payee_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function messages(): array
    {
        return [
            'expense_type.required' => 'يرجى اختيار نوع المصروف.',
            'expense_type.in' => 'نوع المصروف المختار غير صالح.',
            'amount.required' => 'حقل مبلغ المصروف مطلوب.',
            'amount.numeric' => 'يجب أن يكون المبلغ رقماً صحيحاً.',
            'amount.min' => 'يجب أن يكون المبلغ أكبر من صفر (0.01 على الأقل).',
            'expense_date.required' => 'تاريخ المصروف مطلوب.',
            'expense_date.date' => 'صيغة تاريخ المصروف غير صحيحة.',
            'payment_method.required' => 'يرجى اختيار طريقة الدفع.',
            'payment_method.in' => 'طريقة الدفع المختارة غير صالحة.',
        ];
    }
}
