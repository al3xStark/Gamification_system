<?php

namespace Database\Seeders;

use App\Models\RoomItem;
use Illuminate\Database\Seeder;

class CreateRoomItemssSeeder extends Seeder
{
    const ITEMS = [
        [
            'active' => true,
            'type' => 'desk',
            'name' => 'Desk',
        ],
        [
            'active' => true,
            'type' => 'chair',
            'name' => 'Сhair',
        ],
        [
            'active' => true,
            'type' => 'sofa',
            'name' => 'Sofa',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::ITEMS as $item) {
            RoomItem::firstOrCreate($item);
        }
    }
}
