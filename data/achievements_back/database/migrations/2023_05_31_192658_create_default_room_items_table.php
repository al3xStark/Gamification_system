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
        Schema::create('default_room_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('code')->index();
            $table->string('object');
            $table->string('material');
            $table->string('template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_room_items');
    }
};
