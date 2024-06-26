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
        Schema::table('borroweds', function (Blueprint $table) {
            $table->string('detail')->nullable()->after('tools_and_equipment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borroweds', function (Blueprint $table) {
            $table->dropColumn('detail');
        });
    }
};
