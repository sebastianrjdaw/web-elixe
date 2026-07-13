<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('xibo:sync-displays')->dailyAt('08:00');
        $schedule->command('xibo:sync-displays')->dailyAt('14:00');
        $schedule->command('xibo:sync-displays')->dailyAt('20:00');
        $schedule->command('leads:send-daily-summary')->dailyAt('08:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
