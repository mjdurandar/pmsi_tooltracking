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
        Schema::create('tools_and_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description', 255);
            $table->string('image')->nullable();
            $table->string('product_code');
            $table->integer('stocks');  
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade')->nullable();
            $table->integer('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools_and_equipment');
    }
};
