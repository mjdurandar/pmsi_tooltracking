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
        Schema::create('user_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_ordered_product_id')->constrained('customer_ordered_products')->nullable();
            $table->string('shipment_date');
            $table->string('delivery_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_deliveries');
    }
};
