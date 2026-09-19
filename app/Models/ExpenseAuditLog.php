<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'contract_expense_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(ContractExpense::class, 'contract_expense_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'إنشاء',
            'updated' => 'تعديل',
            'deleted' => 'حذف',
            default => $this->action,
        };
    }
}
