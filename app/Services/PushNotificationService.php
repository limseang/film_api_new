<?php
namespace App\Services;
    use Kreait\Firebase\Exception\FirebaseException;
    use Kreait\Firebase\Exception\MessagingException;
    use Kreait\Firebase\Messaging\CloudMessage;
    use Kreait\Firebase\Factory;
    use Exception;
    use Illuminate\Support\Facades\Log;
    use Kreait\Firebase\Messaging\Notification;

    class PushNotificationService
    {

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
                \Log::error('Push notification error: ' . $e->getMessage());
            }
        }

        /**
         * @throws MessagingException
         * @throws FirebaseException
         */
        public static function pushMultipleNotification(array $businessParams = [
            'token' => [],
            'title' => "",
            'body' => "",
            'data' => [],
        ])
        : void

        {
            try {
                $firebase = (new Factory)
                    ->withServiceAccount(__DIR__ . '/firebase_credentials.json');
                $messaging = $firebase->createMessaging();

                $notification = CloudMessage::new()
                    ->withNotification([
                        'title' => $businessParams['title'] ?? "",
                        'body' => $businessParams['body'] ?? "",
                        'image' => $businessParams['image'] ?? "",
                        'type' => $businessParams['type'] ?? '',
                        'data' => $businessParams['data'] ?? [],
                        'id' => $businessParams['id'] ?? '',
                        'sound' => $businessParams['sound'] ?? 'default',
                    ])->withData($businessParams['data'] ?? []);

                $tokenChunks = array_chunk($businessParams['token'], 10);

                foreach ($tokenChunks as $tokens) {
                    $report = $messaging->sendMulticast($notification, $tokens);

                    if ($report->hasFailures()) {
                        foreach ($report->failures()->getItems() as $failure) {
                            Log::error('Failed to send notification to ' . $failure->target()->value() . ': ' . $failure->error()->getMessage());
                        }
                    }
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }


