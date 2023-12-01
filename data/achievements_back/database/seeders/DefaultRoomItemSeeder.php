<?php

namespace Database\Seeders;

use App\Models\DefaultRoomItem;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DefaultRoomItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DefaultRoomItem::truncate();

        $items = [
            'sky',
            'grass',
            'room',
            'shelf',
            'window',
            'door',
            'board',
            'comp',
            'desc',
            'picture',
            'place',
            'room2',
            'chip',
            'memory',
            'power',
            'room_3',
            'shelf_2',
            'motherboard',
            'video',
        ];

        foreach ($items as $item) {

            $filename = Str::uuid()->toString();

            $obj = $this->uploadFile($filename, $item, 'obj');
            $material = $this->uploadFile($filename, $item, 'mtl');
            $template = $this->uploadFile($filename, $item, 'jpg');

            DefaultRoomItem::create([
                'code' => $item,
                'object' => asset('storage/' . $obj),
                'material' => asset('storage/' . $material),
                'template' => asset('storage/' . $template),
            ]);
        }
    }

    /**
     * save base64 string to file
     *
     * @param $file
     * @param $type
     * @return string
     */
    public function uploadFile($name, $file, $type): string
    {
        $file = UploadedFile::fake()->createWithContent(
            $type . '/' . $file . '.' . $type,
            str_replace('%FILENAME%', $name, file_get_contents(__DIR__ . '/defaultRoomItems/' . $file . '.' . $type))
        );

        return $file->storeAs($type, $name . '.' . $type);
    }
}
