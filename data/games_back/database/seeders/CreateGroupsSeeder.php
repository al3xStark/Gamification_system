<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateGroupsSeeder extends Seeder
{

    const GROUPS = [
        'Guest',
        'Register',
        'SuperUser',
        'ProjectAdmin',
        'ProjectManager',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::GROUPS as $groupName) {
            Group::firstOrCreate([
                'name' => $groupName,
            ]);
        }
    }
}
