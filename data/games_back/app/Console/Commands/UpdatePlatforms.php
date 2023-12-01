<?php

namespace App\Console\Commands;

use App\Models\Platform;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdatePlatforms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdatePlatforms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update platforms command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        array_map(fn($platform) => Platform::updateOrCreate([
            'id' => $platform['id']
        ], [
            'name' => $platform['name']
        ]),
            Http::get('https://api.rawg.io/api/platforms/lists/parents?key=75e3263f5eca4bbb803c7d5df0ffcb90')
                ->json()['results']
        );
    }
}
