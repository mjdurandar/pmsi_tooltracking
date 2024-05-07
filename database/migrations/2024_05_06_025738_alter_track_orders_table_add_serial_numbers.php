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
        Schema::table('track_orders', function (Blueprint $table) {
            $table->json('serial_numbers')->after('is_canceled')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('track_orders', function (Blueprint $table) {
            $table->dropColumn('serial_numbers');
        });
    }
};
