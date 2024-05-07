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
        Schema::table('ordered_products', function (Blueprint $table) {
            $table->dropColumn('is_completed');
            $table->dropColumn('is_canceled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordered_products', function (Blueprint $table) {
            $table->boolean('is_completed')->after('track_orders_id')->default(false);
            $table->boolean('is_canceled')->after('is_completed')->default(false);
        });
    }
};
