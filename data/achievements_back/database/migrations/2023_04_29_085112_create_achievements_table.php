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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('active')->index();
            $table->string('name');
            $table->integer('project_id')->index();
            $table->integer('count')->index();
            $table->integer('item_template_id')->index();
            $table->integer('event_id')->index();
            $table->string('event_fields')->index();
            $table->string('event_fields_hash')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
