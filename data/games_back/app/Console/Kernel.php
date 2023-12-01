<?php

namespace App\Console;

use App\Console\Commands\UpdateGames;
use App\Console\Commands\UpdatePlatforms;
use App\Console\Commands\UpdateSteam;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdateSteam::class,
        UpdatePlatforms::class,
        UpdateGames::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('updateGames --type=1')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Schedules');

        require base_path('routes/console.php');
    }
}
