<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_houses', function (Blueprint $table) {
            $table->string('nationality')->nullable()->after('buyer_name');
            $table->string('client_email')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('property_houses', function (Blueprint $table) {
            $table->dropColumn(['nationality', 'client_email']);
        });
    }
};
