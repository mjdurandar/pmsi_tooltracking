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
        Schema::create('total_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_order_id')->nullable()->constrained('track_orders');
            $table->string('vat_total');
            $table->string('total_price');
            $table->string('grand_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('total_prices');
    }
};
