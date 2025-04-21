<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PushNotificationService;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Log;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification {--user=} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending push notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing notification system...');
        
        try {
            if ($this->option('all')) {
                // Send to all users with FCM tokens
                $userLogins = UserLogin::whereNotNull('fcm_token')->get();
                $tokens = $userLogins->pluck('fcm_token')->toArray();
                
                $this->info('Found ' . count($tokens) . ' FCM tokens');
                
                if (empty($tokens)) {
                    $this->error('No FCM tokens found in the database.');
                    return 1;
                }
                
                $this->info('Sending test notification to all users...');
                
                PushNotificationService::pushMultipleNotification([
                    'token' => $tokens,
                    'title' => 'Test Notification',
                    'body' => 'This is a test notification from CinemagicKH - ' . now(),
                    'data' => [
                        'type' => 'test',
                        'timestamp' => now()->timestamp
                    ]
                ]);
                
                $this->info('Notification queued for ' . count($tokens) . ' users');
            } elseif ($userId = $this->option('user')) {
                // Send to specific user
                $userLogin = UserLogin::where('user_id', $userId)
                    ->whereNotNull('fcm_token')
                    ->latest()
                    ->first();
                
                if (!$userLogin || empty($userLogin->fcm_token)) {
                    $this->error('No FCM token found for user ID ' . $userId);
                    return 1;
                }
                
                $this->info('Sending test notification to user ID ' . $userId);
                
                PushNotificationService::pushNotification([
                    'token' => $userLogin->fcm_token,
                    'title' => 'Test Notification',
                    'body' => 'This is a test notification for you from CinemagicKH - ' . now(),
                    'data' => [
                        'type' => 'test',
                        'user_id' => $userId,
                        'timestamp' => now()->timestamp
                    ]
                ]);
                
                $this->info('Notification sent to user ID ' . $userId);
            } else {
                $this->error('Please specify --user=ID or --all option');
                return 1;
            }
            
            $this->info('Test completed. Check logs for details.');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Test notification error: ' . $e->getMessage());
            return 1;
        }
    }
}