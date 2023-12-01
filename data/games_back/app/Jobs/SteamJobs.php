<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\SteamGame;
use Exception;
use Illuminate\Support\Facades\Http;

class SteamJobs extends Jobs
{
    /**
     * @var string
     */
    public $queue = 'steam';

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
        $this->data = 271590;

        $url = 'https://store.steampowered.com/api/appdetails?appids=' . $this->data;
        $request = Http::get($url);
        $gameDetail = $request->json();

        if (
            $request->status() !== 200
            || empty($gameDetail)
            || empty($gameDetail[$this->data]['success'])
            || empty($gameDetail[$this->data]['data'])
        ) {
            throw new Exception(
                'Bad response. Url: ' . $url . ", status: " . $request->status() . " result: " . json_encode(
                    $request->body()
                )
            );
        }

        $data = [
            'is_free' => $gameDetail[$this->data]['data']['is_free'],
            'detailed_description' => $gameDetail[$this->data]['data']['detailed_description'],
        ];

        SteamGame::updateOrCreate([
            'app_id' => $this->data,
        ], $data);

        return true;
    }
}
