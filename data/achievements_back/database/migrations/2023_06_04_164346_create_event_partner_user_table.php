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
        Schema::create('event_partner_user', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('event_id')->index();
            $table->integer('partner_user_id')->index();
            $table->integer('count')->index();
            $table->string('fields')->index();
            $table->string('fields_hash')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_partner_user');
    }
};
