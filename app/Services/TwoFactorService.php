<?php
namespace App\Services;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use App\Models\Storages;
use Exception;
use OSS\OssClient;
use OSS\Core\OssException;

class TwoFactorService
{

    public static function sendSMS()
    {
        try {
            if (config('app.debug')) {
                return [
                    'ResponseCode' => 'OK',
                ];
            }
            $accessKey = env('ALIBABA_SMS_ACCESS_KEY');
            $secretKey = env('ALIBABA_SMS_SECRET_KEY');
            AlibabaCloud::accessKeyClient($accessKey, $secretKey)
                ->regionId('ap-southeast-1')
                ->asDefaultClient();
            $response = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2018-05-01')
                ->action('SendMessageToGlobe')
                ->method('POST')
//                ->host('dysmsapi.ap-southeast-1.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "ap-southeast-1",
                        'To' => '+85593410672',
                        'Message' => 'hello kon papa',
                        'From' => 'Sunpay',
                    ],
                ])
                ->request();
            Log::info('Send SMS', 'sendSms', json_encode($response->toArray()));
            ServiceSmsLog::query()->create([
                'To' => '+85593410672',
                'Message' => 'hello kon papa',
                'response' => $response->toArray()
            ]);
            return $response->toArray();
        } catch (ClientException $e) {
            Log::error('ClientException', 'sendSms', $e->getErrorMessage());
            return $e->getMessage();
        } catch (ServerException $e) {
            Log::error('ServerException', 'sendSms', $e->getErrorMessage());
            return $e->getMessage();
        } catch (Exception $e) {
            Log::error('Exception', 'sendSms', $e->getMessage());
            return $e->getMessage();
        }
}
}


