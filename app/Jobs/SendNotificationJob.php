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

    protected $fcmTokens;
    protected $businessParams;

    // Job configuration
    public $tries = 5;           // Retry the job up to 5 times
    public $backoff = [60, 120, 300, 600]; // Increasing retry delays (1m, 2m, 5m, 10m)
    public $timeout = 300;       // Job timeout in seconds (5 minutes)
    public $maxExceptions = 3;   // Max exceptions before marking job as failed

    /**
     * Create a new job instance.
     */
    public function __construct(array $fcmTokens, array $businessParams)
    {
        $this->fcmTokens = $fcmTokens;
        $this->businessParams = $businessParams;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (empty($this->fcmTokens)) {
            Log::info('No FCM tokens provided, skipping notification job');
            return;
        }

        try {
            // Use the correct path to firebase credentials
            $credentialsPath = base_path('firebase_credentials.json');
            
            if (!file_exists($credentialsPath)) {
                Log::error('Firebase credentials file not found at: ' . $credentialsPath);
                return;
            }

            $firebase = (new Factory)->withServiceAccount($credentialsPath);
            $messaging = $firebase->createMessaging();

            // Create the notification
            $notification = Notification::create(
                $this->businessParams['title'],
                $this->businessParams['body'],
                $this->businessParams['image'] ?? null
            );

            // Create the base message
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($this->businessParams['data'] ?? []);

            // Firebase allows up to 500 recipients per multicast
            // We're already getting the tokens in chunks, but lets ensure they don't exceed 500
            $tokenChunks = array_chunk($this->fcmTokens, 500);
            
            foreach ($tokenChunks as $index => $tokenChunk) {
                try {
                    // Send multicast message
                    Log::info('Sending notification batch ' . ($index + 1) . '/' . count($tokenChunks) . ' (' . count($tokenChunk) . ' recipients)');
                    $report = $messaging->sendMulticast($message, $tokenChunk);

                    // Log success rate
                    $successCount = $report->successes()->count();
                    $failureCount = $report->failures()->count();
                    $totalCount = $successCount + $failureCount;
                    $successRate = ($totalCount > 0) ? ($successCount / $totalCount) * 100 : 0;
                    
                    Log::info("Notification batch result: {$successCount}/{$totalCount} sent successfully ({$successRate}%)");

                    // Log failures details for debugging
                    if ($failureCount > 0) {
                        $failedTokens = [];
                        foreach ($report->failures()->getItems() as $failure) {
                            $failedTokens[] = [
                                'token' => $failure->target()->value(),
                                'error' => $failure->error()->getMessage()
                            ];
                        }
                        Log::debug('Failed tokens details: ', $failedTokens);
                    }
                    
                    // Add delay between batches to prevent rate limiting
                    if ($index < count($tokenChunks) - 1) {
                        sleep(1);
                    }
                    
                } catch (MessagingException $e) {
                    Log::error('Firebase messaging exception: ' . $e->getMessage());
                    $this->fail($e);
                } catch (ConnectException $e) {
                    Log::error('Network connection error: ' . $e->getMessage());
                    // This is a temporary issue, retry the job
                    throw $e;
                } catch (RequestException $e) {
                    Log::error('HTTP request error: ' . $e->getMessage());
                    throw $e;
                }
            }
        } catch (FirebaseException $e) {
            Log::error('Firebase error: ' . $e->getMessage());
            $this->fail($e);
        } catch (Exception $e) {
            Log::error('Notification job error: ' . $e->getMessage());
            $this->fail($e);
        }
    }
}