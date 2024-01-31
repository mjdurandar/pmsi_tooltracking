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
            $table->boolean('is_sold_out')->after('supplier_id')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('power_tools', function (Blueprint $table) {
            $table->dropColumn('is_sold_out');
        });
    }
};
