<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Console\Commands\GoogleDataCollection;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for your application.
     */
    protected $commands = [
        GoogleDataCollection::class, // ✅ Add your command here
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Example: run every 2 days
        $schedule->command('site:sync-data')->everyTwoDays();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
