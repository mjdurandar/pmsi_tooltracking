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
        Schema::table('borrow_tools', function (Blueprint $table) {
            $table->dropColumn('quantity');
            $table->dropColumn('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_tools', function (Blueprint $table) {
            $table->integer('quantity');
            $table->integer('total');
        });
    }
};
