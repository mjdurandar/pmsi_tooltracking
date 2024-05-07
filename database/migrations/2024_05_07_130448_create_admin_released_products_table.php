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
        Schema::create('admin_released_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tools_and_equipment_id')->nullable()->constrained('tools_and_equipment');
            $table->json('serial_numbers')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_released_products');
    }
};
