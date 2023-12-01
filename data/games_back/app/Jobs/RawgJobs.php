<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Game;
use Exception;
use Illuminate\Support\Facades\Http;

class RawgJobs extends Jobs
{
    /**
     * @var string
     */
    public $queue = 'rawg';

    /**
     * @var int
     */
    private mixed $data;

    /**
     * @var string
     */
    private string $eventType;

    /**
     * Create a new job instance.
     *
     * @param string $eventType
     * @param mixed $data
     */
    public function __construct(string $eventType, mixed $data)
    {
        $this->data = $data;
        $this->eventType = $eventType;
    }

    public function backoff()
    {
        return [1, 2, 3];
    }

    /**
     * Execute the job.
     *
     * @return bool
     * @throws Exception
     */
    public function handle(): bool
    {
        $keys = json_decode(env('RAWG_API_KEYS'));
        $key = $keys[array_rand($keys)];

        $request = Http::get("https://api.rawg.io/api/games?key=$key&ordering=added&page_size=40&page=" . $this->data);
        $games = $request->json();

        if ($request->status() !== 200 || empty($games) || !is_array($games['results'])) {
            throw new Exception('Bad response. Url: ' . "https://api.rawg.io/api/games?key=$key&ordering=added&page_size=40&page=" . $this->data . ", status: " . $request->status() . " result: " . json_encode($request->body()));
        }

        foreach ($games['results'] as $game) {
            $gameItem = Game::updateOrCreate(
                [
                    'slug' => $game['slug'],
                ],
                [
                    'rawg_id' => $game['id'],
                    'name' => $game['name'] ?? '',
                    'rating' => $game['rating'] ?? 0,
                    'ratings_count' => $game['ratings_count'] ?? 0,
                    'background_image' => $game['background_image'] ?? '',
                    'short_screenshots' => $game['short_screenshots'] ?? [],
                ],
            );

            $gameItem->platform()->attach(
                array_map(fn($platform) => $platform['platform']['id'],
                    $game['parent_platforms'])
            );
        }

        return true;
    }
}
