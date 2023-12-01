<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('rawg_id');
            $table->string('name')->index();
            $table->string('description')->default('');
            $table->string('slug')->index();
            $table->float('rating');
            $table->integer('ratings_count');
            $table->string('background_image')->default('');
            $table->text('short_screenshots');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
