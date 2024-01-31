<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('power_tools', function (Blueprint $table) {
            $table->date('sales_date')->nullable()->after('is_sold_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('power_tools', function (Blueprint $table) {
            $table->dropColumn('sales_date');
        });
    }
};
