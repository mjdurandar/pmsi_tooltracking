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
        Schema::table('borroweds', function (Blueprint $table) {
            $table->string('return_date')->after('return_days_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borroweds', function (Blueprint $table) {
            $table->dropColumn('return_date');
        });
    }
};
