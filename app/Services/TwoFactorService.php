<?php
namespace App\Services;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Tea\Request;
use App\Models\Storages;
use Exception;
use Illuminate\Support\Facades\Log;
use OSS\OssClient;
use OSS\Core\OssException;

class TwoFactorService
{

    public static function sendSMS($phone, $message)
    {
        try {


            $accessKey = env('ALIBABA_SMS_ACCESS_KEY');
            $secretKey = env('ALIBABA_SMS_SECRET_KEY');
       AlibabaCloud::accessKeyClient($accessKey, $secretKey)
                ->regionId('ap-southeast-1')
                ->asDefaultClient();

            $response = AlibabaCloud::rpc()
                ->action('SendMessageToGlobe')
                ->product('Dysmsapi')
                ->version('2018-05-01')
                ->method('POST')
                ->host('dysmsapi.ap-southeast-1.aliyuncs.com')
                ->options([
                    'query' => [
                        "To" => "855" . $phone,
                        "From" => "CinemagicKh",
                        "Message" => $message,
                    ],
                ])

                ->request();

//
//            ServiceSmsLog::query()->create([
//                'To' => '+85593410672',
//                'Message' => 'hello kon papa',
//                'response' => $response->toArray()
//            ]);

            dd ( $response->toArray() );
        } catch (ClientException $e) {
            Log::error('ClientException: ' . $e->getErrorMessage(), ['method' => 'sendSms']);
            return $e->getMessage();
        } catch (ServerException $e) {
            Log::error('ServerException: ' . $e->getErrorMessage(), ['method' => 'sendSms']);
            return $e->getMessage();
        } catch (Exception $e) {
            Log::error('Exception: ' . $e->getMessage(), ['method' => 'sendSms']);
            return $e->getMessage();
        }
}
}


