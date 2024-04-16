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
        Schema::table('admin_histories', function (Blueprint $table) {
            $table->foreignId('user_id')->after('tools_and_equipment_id')->constrained('users');
            $table->integer('total_price')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_histories', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('total_price');
        });
    }
};
