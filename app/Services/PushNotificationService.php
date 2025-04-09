<?php
namespace App\Services;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\Notification;
use App\Jobs\SendNotificationJob;

class PushNotificationService
{
    /**
     * Send notification to a single device
     * 
     * @param array $businessParams Contains token, title, body and other notification data
     * @return void
     */
    public static function pushNotification(array $businessParams = [
        'token' => "",
        'title' => "",
        'body' => "",
        'sound' => 'default',
        'data' => [],
    ]): void
    {
        try {
            // Initialize Firebase Messaging
            $firebase = (new Factory)->withServiceAccount(__DIR__ . '/firebase_credentials.json');
            $messaging = $firebase->createMessaging();

            // Validate required fields
            if (empty($businessParams['token'])) {
                throw new Exception('The token is required for push notifications.');
            }
            if (empty($businessParams['title']) || empty($businessParams['body'])) {
                throw new Exception('Notification title and body are required.');
            }

            // Create Notification Object
            $notification = Notification::create($businessParams['title'], $businessParams['body'], $businessParams['image'] ?? null);

            // Create CloudMessage
            $message = CloudMessage::withTarget('token', $businessParams['token'])
                ->withNotification($notification)
                ->withData($businessParams['data'] ?? [])
                ->withSound($businessParams['sound'] ?? 'default');

            // Send the Notification
            $messaging->send($message);

        } catch (Exception $e) {
            // Log error
            Log::error('Push notification error: ' . $e->getMessage());
        }
    }

    /**
     * Send notifications to multiple devices using queued jobs
     * 
     * @param array $businessParams Contains tokens array, title, body and other notification data
     * @return void
     */
    public static function pushMultipleNotification(array $businessParams = [
        'token' => [],
        'title' => "",
        'body' => "",
        'data' => [],
    ]): void
    {
        try {
            // Validate input
            if (empty($businessParams['token']) || !is_array($businessParams['token'])) {
                throw new Exception('Tokens array is required for multiple push notifications.');
            }

            if (empty($businessParams['title']) || empty($businessParams['body'])) {
                throw new Exception('Notification title and body are required.');
            }

            // Chunk tokens into groups of 500 (Firebase's maximum for multicast)
            $tokenChunks = array_chunk($businessParams['token'], 500);
            
            // Process each chunk as a separate queued job
            foreach ($tokenChunks as $tokenChunk) {
                SendNotificationJob::dispatch($tokenChunk, $businessParams)->onQueue('notifications');
            }
            
            Log::info('Queued ' . count($tokenChunks) . ' notification jobs for ' . count($businessParams['token']) . ' recipients');
            
        } catch (Exception $e) {
            Log::error('Error queueing push notifications: ' . $e->getMessage());
        }
    }
}