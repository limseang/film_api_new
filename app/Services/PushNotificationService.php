<?php
namespace App\Services;
    use Kreait\Firebase\Exception\FirebaseException;
    use Kreait\Firebase\Exception\MessagingException;
    use Kreait\Firebase\Messaging\CloudMessage;
    use Kreait\Firebase\Factory;
    use Exception;
    use Illuminate\Support\Facades\Log;

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
                $firebase = (new Factory)->withServiceAccount(__DIR__ . '/firebase_credentials.json');
                $messaging = $firebase->createMessaging();

                $notification = CloudMessage::withTarget('token', $businessParams['token'])
                    ->withNotification([
                        'title' => $businessParams['title'] ?? "",
                        'body' => $businessParams['body'] ?? "",
                        'image' => $businessParams['image'] ?? "",
                        'type' => $businessParams['type'] ?? '',
                        'data' => $businessParams['data'] ?? [],
                        'id' => $businessParams['id'] ?? '',
                        'sound' => $businessParams['sound'] ?? 'default',
                    ])->withData($businessParams['data'] ?? []);

                $messaging->send($notification);

            } catch (Exception $e) {
                log::error($e->getMessage());
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
        ]): void

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


