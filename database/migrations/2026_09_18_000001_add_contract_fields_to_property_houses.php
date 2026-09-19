<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_houses', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->after('total_percentage');
            $table->string('contract_number')->unique()->nullable()->after('reference_code');
            $table->date('contract_date')->nullable()->after('contract_number');
            $table->string('contract_status')->default('draft')->after('contract_date');
            $table->string('phone')->nullable()->after('buyer_name');
            $table->string('payment_method')->nullable()->after('phone');
            $table->text('contract_notes')->nullable()->after('final_general_notes');
        });
    }

    public function down(): void
    {
        Schema::table('property_houses', function (Blueprint $table) {
            $table->dropColumn([
                'price',
                'contract_number',
                'contract_date',
                'contract_status',
                'phone',
                'payment_method',
                'contract_notes',
            ]);
        });
    }
};
