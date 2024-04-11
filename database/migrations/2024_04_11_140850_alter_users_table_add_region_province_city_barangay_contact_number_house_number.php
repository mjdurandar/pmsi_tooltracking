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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable();
            $table->foreignId('province_id')->nullable();
            $table->string('city');
            $table->string('barangay');
            $table->string('house_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['province_id']);
            $table->dropColumn('city');
            $table->dropColumn('barangay');
            $table->dropColumn('house_number');
        });
    }
};
