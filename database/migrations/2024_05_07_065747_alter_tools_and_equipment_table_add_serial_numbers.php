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
            $table->json('serial_numbers')->after('is_approved')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools_and_equipment', function (Blueprint $table) {
            $table->dropColumn('serial_numbers');
        });
    }
};
