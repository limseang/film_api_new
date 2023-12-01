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
            'image' => "",
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
                    'sound' => 'default',
                ]);
            $messaging->send($notification);
            }catch (Exception $e){
              log::error($e->getMessage());
              throw new Exception($e->getMessage());
            }

        }
    }


