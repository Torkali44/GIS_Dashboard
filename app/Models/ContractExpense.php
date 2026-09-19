<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractExpense extends Model
{
    protected $fillable = [
        'property_house_id',
        'expense_type',
        'amount',
        'expense_date',
        'payment_method',
        'payee_name',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    /* ─── Expense type categories ─── */

    public static function expenseTypes(): array
    {
        return [
            'salary' => 'رواتب/أجور الفاحصين',
            'electrician' => 'كهربائي',
            'transport' => 'مواصلات',
            'gas' => 'بنزين',
            'photography' => 'تصوير',
            'report_prep' => 'إعداد التقرير',
            'printing' => 'طباعة',
            'equipment' => 'معدات',
            'materials' => 'مواد',
            'commissions' => 'عمولات',
            'other' => 'مصروفات أخرى',
        ];
    }

    public function getExpenseTypeLabelAttribute(): string
    {
        return self::expenseTypes()[$this->expense_type] ?? $this->expense_type;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'benefit' => 'Benefit',
            'bank_transfer' => 'تحويل بنكي',
            'cash' => 'كاش',
            default => 'أخرى',
        };
    }

    /* ─── Relationships ─── */

    public function propertyHouse(): BelongsTo
    {
        return $this->belongsTo(PropertyHouse::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(ExpenseAuditLog::class)->orderByDesc('created_at');
    }
}
