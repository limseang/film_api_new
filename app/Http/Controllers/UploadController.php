<?php

namespace App\Http\Controllers;

use OSS\OssClient;
use App\Models\Storages;
use OSS\Core\OssException;
use Illuminate\Support\Env;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public  function UploadFile($file)
    {
        $accessKeyId = env("ALIBABA_OSS_ACCESS_KEY");
        $accessKeySecret = env("ALIBABA_OSS_SECRET_KEY");
        $endpoint = env("ALIBABA_OSS_ENDPOINT");
        $bucket = env("ALIBABA_OSS_BUCKET");
        
        $object = 'uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        $object .= md5($file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->getRealPath();

        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $result = $ossClient->uploadFile($bucket, $object, $filePath);
        // if use base64 upload
        // $content = "base64";
        // $ossClient->putObject($bucket, $object, $content);

        if (!empty($result)) {
            $params = [
                'path' => $object,
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize()
            ];
            $storage = Storages::query()->create($params);
            return $storage->id;
        }
        else{
            return 0;
        }
    }
    
    public function getSignedUrl($id): string
    {
        try {
            

            $accessKeyId = env("ALIBABA_OSS_ACCESS_KEY");
        $accessKeySecret = env("ALIBABA_OSS_SECRET_KEY");
        $endpoint = env("ALIBABA_OSS_ENDPOINT");
        $bucket = env("ALIBABA_OSS_BUCKET");

            $storage = Storages::query()->find($id);
            $timeout = 3600;

            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $signedUrl = $ossClient->signUrl($bucket,$storage->path,3600,"GET",null);
            // $ossClient->signUrl($bucket, $storage->path, $timeout);

            return $signedUrl;
        } catch (OssException $e) {
            Log::error($e->getErrorMessage());
            return $e->getMessage();
        }
    }

    public function delete($id)
    {
        //delete file from oss
        $storage = Storages::query()->find($id);
        $accessKeyId = env("ALIBABA_OSS_ACCESS_KEY");
        $accessKeySecret = env("ALIBABA_OSS_SECRET_KEY");
        $endpoint = env("ALIBABA_OSS_ENDPOINT");
        $bucket = env("ALIBABA_OSS_BUCKET");
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $ossClient->deleteObject($bucket, $storage->path);
        //delete file from database
        $storage->delete();
        }

    }

