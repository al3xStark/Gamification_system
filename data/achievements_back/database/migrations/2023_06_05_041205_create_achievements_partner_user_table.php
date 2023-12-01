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
        Schema::create('achievement_partner_user', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('achievement_id');
            $table->integer('partner_user_id');
            $table->integer('new')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievement_partner_user');
    }
};
