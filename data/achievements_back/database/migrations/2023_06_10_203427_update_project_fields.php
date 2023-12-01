<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumns('projects', ['api_key'])) {
            DB::statement('ALTER TABLE projects DROP INDEX projects_api_key_index');
            DB::statement('ALTER TABLE projects MODIFY COLUMN api_key TEXT');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumns('projects', ['api_key'])) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('api_key')->index()->change();
            });
        }
    }
};
