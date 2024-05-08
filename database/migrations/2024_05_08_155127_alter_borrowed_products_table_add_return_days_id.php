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
        Schema::table('borrowed_products', function (Blueprint $table) {
            $table->foreignId('return_days_id')->nullable()->constrained('return_days');
            $table->string('penalty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowed_products', function (Blueprint $table) {
            $table->dropForeign(['return_days_id']);
            $table->dropColumn('return_days_id');
            $table->dropColumn('penalty');
        });
    }
};
