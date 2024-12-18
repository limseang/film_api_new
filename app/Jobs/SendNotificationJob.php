<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
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
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fcmToken;
    protected $businessParams;

    public $tries = 5; // Retry the job up to 5 times
    public $backoff = 60; // Wait for 60 seconds before retrying

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

                try {
                    // Attempt to send the message
                    $report = $messaging->sendMulticast($message, $tokens);

                    // Handle failures in sending messages
                    if ($report->hasFailures()) {
                        foreach ($report->failures()->getItems() as $failure) {
                            Log::error('Failed to send notification to ' . $failure->target()->value() . ': ' . $failure->error()->getMessage());
                        }
                    }
                } catch (ConnectException $e) {
                    Log::error('Network error while sending notifications: ' . $e->getMessage());
                } catch (RequestException $e) {
                    Log::error('Request error while sending notifications: ' . $e->getMessage());
                } catch (MessagingException $e) {
                    Log::error('Firebase Messaging error: ' . $e->getMessage());
                }
            }
        } catch (FirebaseException $e) {
            Log::error('Firebase error: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Push notification error: ' . $e->getMessage());
        }
    }
}
