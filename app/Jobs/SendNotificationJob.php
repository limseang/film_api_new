<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fcmToken;
    protected $businessParams;

    /**
     * Create a new job instance.
     */
    public function __construct(array $fcmToken, array $businessParams)
    {
        $this->fcmToken = $fcmToken;
        $this->businessParams = $businessParams;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $firebase = (new Factory)
                ->withServiceAccount(__DIR__ . '/firebase_credentials.json');
            $messaging = $firebase->createMessaging();

            $notification = Notification::create(
                $this->businessParams['title'],
                $this->businessParams['body'],
                $this->businessParams['image'] ?? null
            );

            $tokenChunks = array_chunk($this->fcmToken, 500);
            foreach ($tokenChunks as $tokens) {
                $message = CloudMessage::new()
                    ->withNotification($notification)
                    ->withData($this->businessParams['data'] ?? []);

                $report = $messaging->sendMulticast($message, $tokens);

                if ($report->hasFailures()) {
                    foreach ($report->failures()->getItems() as $failure) {
                        Log::error('Failed to send notification to ' . $failure->target()->value() . ': ' . $failure->error()->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Push notification error: ' . $e->getMessage());
        }
    }
}
