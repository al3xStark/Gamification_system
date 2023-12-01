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
        if (!Schema::hasColumns('steam_games', ['is_free'])) {
            Schema::table('steam_games', function (Blueprint $table) {
                $table->integer('is_free')->default(0);
                $table->text('detailed_description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumns('steam_games', ['is_free'])) {
            Schema::table('steam_games', function (Blueprint $table) {
                $table->dropColumn('is_free');
                $table->dropColumn('detailed_description');
            });
        }
    }
};
