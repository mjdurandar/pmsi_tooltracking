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
        Schema::table('purchased_items', function (Blueprint $table) {
            $table->foreignId('damaged_return_id')->nullable()->after('user_id')->constrained('damaged_returns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchased_items', function (Blueprint $table) {
            $table->dropColumn('damaged_return_id');
        });
    }
};
