<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_payments', function (Blueprint $table) {
            $table->index('payment_date');
        });

        Schema::table('contract_expenses', function (Blueprint $table) {
            $table->index('expense_date');
        });

        Schema::table('property_houses', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('contract_status');
        });
    }

    public function down(): void
    {
        Schema::table('contract_payments', function (Blueprint $table) {
            $table->dropIndex(['payment_date']);
        });

        Schema::table('contract_expenses', function (Blueprint $table) {
            $table->dropIndex(['expense_date']);
        });

        Schema::table('property_houses', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['contract_status']);
        });
    }
};
