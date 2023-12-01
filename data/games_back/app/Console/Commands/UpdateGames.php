<?php

namespace App\Console\Commands;

use App\Events\UpdateGamesRawgEvent;
use App\Models\Game;
use App\Models\GamePlatform;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateGames {flowCount=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update games command';

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

        $delete = $this->confirm('Delete all?');

        if ($delete) {
            Game::truncate();
            GamePlatform::truncate();
        }

        $keys = json_decode(env('RAWG_API_KEYS'));
        $key = $keys[array_rand($keys)];

        $count = ceil(
            Http::get(
                "https://api.rawg.io/api/games?key=$key&page_size=1&page=1"
            )->json()['count'] / 40
        );

        $this->info('Creating jobs all = ' . $count);

        $console = $this->getOutput();
        $pBar = $console->createProgressBar($count);
        $pBar->setFormat('verbose');
        $pBar->display();


        for ($i = 1; $i <= $count; $i++) {
            event(new UpdateGamesRawgEvent($i));
            $pBar->advance();
        }

        $this->info('Script ended');
    }
}
