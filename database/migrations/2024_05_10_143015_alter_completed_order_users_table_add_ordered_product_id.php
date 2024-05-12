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
        Schema::table('completed_order_users', function (Blueprint $table) {
            $table->foreignId('ordered_product_id')->nullable()->constrained('ordered_products')->after('track_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('completed_order_users', function (Blueprint $table) {
            $table->dropForeign(['ordered_product_id']);
            $table->dropColumn('ordered_product_id');
        });
    }
};
