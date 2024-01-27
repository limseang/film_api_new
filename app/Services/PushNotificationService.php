<?php
namespace App\Services;
    use Kreait\Firebase\Messaging\CloudMessage;
    use Kreait\Firebase\Factory;
    use Exception;
    use Illuminate\Support\Facades\Log;

    class PushNotificationService {

       public static function pushNotification(array $businessParams=[
            'token' => "",
            'title' => "",
            'body' => "",
           'data' => [],
        ]): void
        {
            try{
            $firebase = (new Factory)
                ->withServiceAccount(__DIR__.'/firebase_credentials.json');
            $messaging = $firebase->createMessaging();
            $notification = CloudMessage::withTarget('token', $businessParams['token'])
                ->withNotification([
                    'title' => $businessParams['title'] ?? "",
                    'body' => $businessParams['body'] ?? "",
                    'image' => $businessParams['image'] ?? "",
                    'type' => $businessParams['type'] ?? '',
                    'data' => $businessParams['data'] ?? [],
                    'id' => $businessParams['id'] ?? '',
                    'sound' => 'default',
                ])->withData($businessParams['data'] ?? []);
            $messaging->send($notification);

            }catch (Exception $e){
              log::error($e->getMessage());
            }

        }

        public static function pushMultipleNotification(array $businessParams=[
            'token' => [],
            'title' => "",
            'body' => "",
            'data' => [],
        ]): void
        {
            try{
                $firebase = (new Factory)
                    ->withServiceAccount(__DIR__.'/firebase_credentials.json');
                $messaging = $firebase->createMessaging();
                foreach ($businessParams['token'] as $token){
                    $notification = CloudMessage::withTarget('token', $token)
                        ->withNotification([
                            'title' => $businessParams['title'] ?? "",
                            'body' => $businessParams['body'] ?? "",
                            'image' => $businessParams['image'] ?? "",
                            'type' => $businessParams['type'] ?? '',
                            'data' => $businessParams['data'] ?? [],
                            'id' => $businessParams['id'] ?? '',
                            'sound' => 'default',
                        ])->withData($businessParams['data'] ?? []);
                    $messaging->send($notification);
                }

            }catch (Exception $e){
                log::error($e->getMessage());
            }

        }
    }


