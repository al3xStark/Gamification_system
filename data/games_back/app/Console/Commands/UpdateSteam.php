<?php

namespace App\Console\Commands;

use App\Events\UpdateGamesSteamEvent;
use App\Models\SteamGame;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateSteam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateSteam';

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
     * @throws Exception
     */
    public function handle()
    {
        $console = $this->getOutput();

        $items = Http::get('https://api.steampowered.com/ISteamApps/GetAppList/v0002/?format=json')->json(
        )['applist']['apps'];

        $pBar = $console->createProgressBar(count($items));
        $pBar->setFormat('verbose');
        $pBar->display();

        array_map(
            function ($game) use ($pBar) {
                $pBar->advance();

                if (!empty($game['name']) && !empty($game['appid'])) {
                    SteamGame::updateOrCreate([
                        'name' => $game['name'],
                    ], [
                        'app_id' => $game['appid'],
                    ]);

                    event(new UpdateGamesSteamEvent($game['appid']));
                }
            },
            $items
        );
    }
}
