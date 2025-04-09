<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NotificationQueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:worker {--queue=notifications : The queue to listen on}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start processing the notification queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queue = $this->option('queue');
        
        $this->info("Starting notification queue worker for queue: {$queue}");
        
        $this->call('queue:work', [
            '--queue' => $queue,
            '--sleep' => '3',
            '--tries' => '5',
            '--max-time' => '3600',
            '--max-jobs' => '500',
            '--stop-when-empty' => false,
        ]);
    }
}