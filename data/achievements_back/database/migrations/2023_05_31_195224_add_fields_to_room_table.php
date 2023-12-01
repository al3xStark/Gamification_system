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
        if (!Schema::hasColumns('room_items', ['coordinates'])) {
            Schema::table('room_items', function (Blueprint $table) {
                $table->unsignedBigInteger('default_room_item_id')->after('active');
                $table->string('coordinates');
                $table->string('rotation');
                $table->string('template');
            });
        }
        if (Schema::hasColumns('room_items', ['type'])) {
            Schema::table('room_items', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumns('room_items', ['coordinates'])) {
            Schema::table('room_items', function (Blueprint $table) {
                $table->dropColumn('coordinates');
                $table->dropColumn('rotation');
                $table->dropColumn('default_room_item_id');
                $table->dropColumn('template');
            });
        }
        if (!Schema::hasColumns('room_items', ['type'])) {
            Schema::table('room_items', function (Blueprint $table) {
                $table->string('type');
            });
        }
    }
};
