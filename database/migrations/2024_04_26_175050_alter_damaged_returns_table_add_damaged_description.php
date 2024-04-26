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
        Schema::table('damaged_returns', function (Blueprint $table) {
            $table->string('damaged_description')->after('status')->nullable();
            $table->string('delivery_date')->after('damaged_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('damaged_returns', function (Blueprint $table) {
            $table->dropColumn('damaged_description');
            $table->dropColumn('delivery_date');
        });
    }
};
