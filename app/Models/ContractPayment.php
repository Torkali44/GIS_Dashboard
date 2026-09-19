<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractPayment extends Model
{
    protected $fillable = [
        'property_house_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function propertyHouse(): BelongsTo
    {
        return $this->belongsTo(PropertyHouse::class);
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
}
