<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_house_id')->constrained()->cascadeOnDelete();
            $table->string('expense_type'); // salary, electrician, transport, gas, photography, report_prep, printing, equipment, materials, commissions, other
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('payment_method')->default('cash');
            $table->string('payee_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_expenses');
    }
};
