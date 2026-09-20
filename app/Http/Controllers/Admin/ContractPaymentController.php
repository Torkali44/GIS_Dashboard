<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractPayment;
use App\Models\PropertyHouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContractPaymentController extends Controller
{
    public function store(Request $request, PropertyHouse $house): RedirectResponse
    {
        $data = $request->validate($this->rules(), $this->messages());

        $house->payments()->create($data);

        return redirect()
            ->route('admin.houses.show', $house)
            ->with('status', 'تم تسجيل الدفعة بنجاح بمبلغ '.number_format($data['amount'], 2).' د.ب');
    }

    public function update(Request $request, PropertyHouse $house, ContractPayment $payment): RedirectResponse
    {
        $this->ensureBelongsToHouse($house, $payment);

        $data = $request->validate($this->rules(), $this->messages());

        $payment->update($data);

        return redirect()
            ->route('admin.houses.show', $house)
            ->with('status', 'تم تحديث بيانات الدفعة بنجاح.');
    }

    public function destroy(PropertyHouse $house, ContractPayment $payment): RedirectResponse
    {
        $this->ensureBelongsToHouse($house, $payment);

        $payment->delete();

        return redirect()
            ->route('admin.houses.show', $house)
            ->with('status', 'تم حذف الدفعة.');
    }

    private function ensureBelongsToHouse(PropertyHouse $house, ContractPayment $payment): void
    {
        abort_unless((int) $payment->property_house_id === (int) $house->id, 404);
    }

    private function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'in:cash,benefit,bank_transfer,other'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function messages(): array
    {
        return [
            'amount.required' => 'حقل مبلغ الدفعة مطلوب.',
            'amount.numeric' => 'يجب أن يكون المبلغ رقماً صحيحاً.',
            'amount.min' => 'يجب أن يكون المبلغ أكبر من صفر (0.01 على الأقل).',
            'payment_date.required' => 'تاريخ الدفعة مطلوب.',
            'payment_date.date' => 'صيغة تاريخ الدفعة غير صحيحة.',
            'payment_method.required' => 'يرجى اختيار طريقة الدفع.',
            'payment_method.in' => 'طريقة الدفع المختارة غير صالحة.',
        ];
    }
}
