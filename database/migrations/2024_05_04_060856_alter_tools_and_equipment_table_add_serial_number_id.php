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
            $table->foreignId('serial_number_id')->nullable()->constrained('serial_numbers')->after('product_id');
            $table->boolean('is_delivered')->default(false)->after('serial_number_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools_and_equipment', function (Blueprint $table) {
            $table->dropForeign(['serial_number_id']);
            $table->dropColumn('serial_number_id');
            $table->dropColumn('is_delivered');
        });
    }
};
