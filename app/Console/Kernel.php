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
        foreach (['08:00', '14:00', '20:00'] as $time) {
            $schedule->command('xibo:sync-displays')
                ->dailyAt($time)
                ->timezone(config('app.timezone'))
                ->withoutOverlapping(30)
                ->onOneServer();
        }

        $schedule->command('leads:send-daily-summary')
            ->dailyAt('08:00')
            ->timezone(config('app.timezone'))
            ->withoutOverlapping(30)
            ->onOneServer();
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
