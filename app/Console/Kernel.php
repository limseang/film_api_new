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
        // Schedule the upcoming films job
        $schedule->job(new \App\Jobs\CommingSoonFilm)->everyThreeHours();
        
        // Schedule the FCM token cleanup job to run daily
        $schedule->command('fcm:cleanup')->daily();
        
        // Schedule the queue worker to run every minute in case it crashes
        $schedule->command('queue:work --queue=notifications --stop-when-empty')
                ->everyMinute()
                ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    protected $commands = [
// Other commands...
        \App\Console\Commands\TmdbDownloader::class,
        \App\Console\Commands\ImportTmdbToFilm::class,
        \App\Console\Commands\NotificationQueueWorker::class,
        \App\Console\Commands\TestNotification::class,
        \App\Console\Commands\CleanupFcmTokens::class,
    ];

}


