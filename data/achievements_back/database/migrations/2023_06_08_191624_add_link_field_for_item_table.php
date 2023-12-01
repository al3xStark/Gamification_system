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
        if (!Schema::hasColumns('room_items', ['link'])) {
            Schema::table('room_items', function (Blueprint $table) {
                $table->string('link')->default('');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumns('room_items', ['link'])) {
            Schema::table('room_items', function (Blueprint $table) {
                $table->dropColumn('link');
            });
        }
    }
};
