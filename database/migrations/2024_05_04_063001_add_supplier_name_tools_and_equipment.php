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
        Schema::table('tools_and_equipment', function (Blueprint $table) {
            $table->string('supplier_name')->after('product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools_and_equipment', function (Blueprint $table) {
            $table->dropColumn('supplier_name');
        });
    }
};