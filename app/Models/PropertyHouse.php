<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PropertyHouse extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'client_name',
        'address',
        'reference_code',
        'contract_number',
        'contract_date',
        'contract_status',
        'inspection_date',
        'notes',
        // Property fields
        'activity',
        'property_type',
        'building_status',
        'document_number',
        'intro_number',
        'villa_number',
        'road',
        'compound',
        'area',
        'buyer_name',
        'nationality',
        'phone',
        'client_email',
        'payment_method',
        'id_number',
        'developer_name',
        'engineering_supervisor',
        'main_contractor',
        'property_age',
        'land_area',
        'building_area',
        'floors_count',
        'rooms_count',
        'bathrooms_count',
        'halls_count',
        'parking_count',
        'kitchens_count',
        'total_percentage',
        'inspector_rating_override',
        // Contract / financial fields
        'price',
        'final_result_text',
        'final_general_notes',
        'contract_notes',
        'report_delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'total_percentage' => 'integer',
            'price' => 'decimal:2',
            'inspection_date' => 'date',
            'contract_date' => 'date',
            'report_delivered_at' => 'date',
        ];
    }

    /* ─── Auto-generate contract number ─── */

    protected static function booted(): void
    {
        static::creating(function (self $house) {
            if (empty($house->contract_number)) {
                $house->contract_number = self::generateContractNumber();
            }
            if (empty($house->contract_date)) {
                $house->contract_date = now()->toDateString();
            }
        });
    }

    public static function generateContractNumber(): string
    {
        $next = function (): string {
            $year = now()->format('Y');
            $lastHouse = self::query()
                ->where('contract_number', 'like', "GIS-{$year}-%")
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($lastHouse && preg_match('/GIS-\d{4}-(\d+)/', $lastHouse->contract_number, $m)) {
                $seq = ((int) $m[1]) + 1;
            } else {
                $seq = 1;
            }

            return sprintf('GIS-%s-%04d', $year, $seq);
        };

        return DB::transactionLevel() > 0
            ? $next()
            : DB::transaction($next);
    }

    public function downloadBasename(): string
    {
        $base = (string) ($this->contract_number ?: $this->id);
        $safe = preg_replace('/[^A-Za-z0-9._-]+/', '-', $base) ?: (string) $this->id;

        return 'contract-' . trim($safe, '-');
    }

    /* ─── Dates ─── */

    public function reportInspectionDate(): Carbon
    {
        return $this->inspection_date
            ?? $this->created_at
            ?? now();
    }

    public function reportInspectionDateFormatted(): string
    {
        return $this->reportInspectionDate()->format('Y-m-d');
    }

    /* ─── Relationships ─── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ContractPayment::class)->orderBy('payment_date');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ContractExpense::class)->orderBy('expense_date');
    }

    /* ─── Financial Computed Attributes ─── */

    public function getTotalPaidAttribute(): float
    {
        if (array_key_exists('payments_sum', $this->attributes)) {
            return (float) ($this->attributes['payments_sum'] ?? 0);
        }

        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->sum('amount');
        }

        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) ($this->price ?? 0) - $this->total_paid;
    }

    public function getTotalExpensesAttribute(): float
    {
        if (array_key_exists('expenses_sum', $this->attributes)) {
            return (float) ($this->attributes['expenses_sum'] ?? 0);
        }

        if ($this->relationLoaded('expenses')) {
            return (float) $this->expenses->sum('amount');
        }

        return (float) $this->expenses()->sum('amount');
    }

    public function getNetProfitAttribute(): float
    {
        return (float) ($this->price ?? 0) - $this->total_expenses;
    }

    public function getPaymentStatusAttribute(): string
    {
        if (! $this->price || $this->price <= 0) {
            return 'unpaid';
        }

        $paid = $this->total_paid;

        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid >= $this->price) {
            return 'paid';
        }

        return 'partial';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'مدفوع بالكامل',
            'partial' => 'مدفوع جزئي',
            default => 'غير مدفوع',
        };
    }

    public function getContractStatusLabelAttribute(): string
    {
        return match ($this->contract_status) {
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => 'مسودة',
        };
    }
}
