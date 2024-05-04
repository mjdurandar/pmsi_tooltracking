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
        Schema::table('serial_numbers', function (Blueprint $table) {
            $table->boolean('is_selected')->after('serial_number')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serial_numbers', function (Blueprint $table) {
            $table->dropColumn('is_selected');
        });
    }
};
