<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateProjectsKeys extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::get();

        foreach ($projects as $project) {
            $crypt = Crypt::encryptString(Str::uuid()->toString());
            DB::statement('UPDATE `projects` SET `api_key`="' . $crypt . '" where `id`=' . $project->id);
        }
    }
}
