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
        Schema::table('released_products', function (Blueprint $table) {
            $table->boolean('is_admin')->after('is_returned')->default(0);
            $table->boolean('is_supplier')->after('is_admin')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('released_products', function (Blueprint $table) {
            $table->dropColumn('is_admin');
            $table->dropColumn('is_supplier');
        });
    }
};
