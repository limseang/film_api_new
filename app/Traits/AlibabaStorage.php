<?php

namespace App\Traits;
use App\Models\Storages;
use Illuminate\Support\Facades\Log;
use OSS\OssClient;
use OSS\Core\OssException;
trait AlibabaStorage
{ 
    
    public  function UploadFile($file, $folder = null): int
    {
        $accessKeyId = env("ALIBABA_OSS_ACCESS_KEY");
        $accessKeySecret = env("ALIBABA_OSS_SECRET_KEY");
        $endpoint = env("ALIBABA_OSS_ENDPOINT");
        $bucket = env("ALIBABA_OSS_BUCKET");

        if ($folder) {
            // Add the specified folder to the path
            $object = trim($folder, '/') . '/';
        }else{
            $object = 'uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        }
        $object .= md5($file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->getRealPath();
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $result = $ossClient->uploadFile($bucket, $object, $filePath);

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

    public  function UploadFileUsed($file, $folder = null): int
    {
        $accessKeyId = env("ALIBABA_OSS_ACCESS_KEY");
        $accessKeySecret = env("ALIBABA_OSS_SECRET_KEY");
        $endpoint = env("ALIBABA_OSS_ENDPOINT");
        $bucket = env("ALIBABA_OSS_BUCKET");

        if ($folder) {
            // Add the specified folder to the path
            $object = trim($folder, '/') . '/';
        }else{
            $object = 'uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        }
        $object .= md5($file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->getRealPath();
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $result = $ossClient->uploadFile($bucket, $object, $filePath);

        if (!empty($result)) {
            $params = [
                'path' => $object,
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'type' => 'Episode',
                'is_used' => 'N'
            ];
            $storage = Storages::query()->create($params);
            return $storage->id;
        }
        else{
            return 0;
        }
    }

    public function uploadUrl($file, $folder = null): array
    {
        $accessKeyId = env("ALIBABA_OSS_ACCESS_KEY");
        $accessKeySecret = env("ALIBABA_OSS_SECRET_KEY");
        $endpoint = env("ALIBABA_OSS_ENDPOINT");
        $bucket = env("ALIBABA_OSS_BUCKET");

        // $object = 'uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        if ($folder) {
            // Add the specified folder to the path
            $object = trim($folder, '/') . '/';
        }else{
            $object = 'uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        }
        $object .= md5($file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->getRealPath();

        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $result = $ossClient->uploadFile($bucket, $object, $filePath);
        if (!empty($result)) {
            $params = [
                'path' => $object,
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'url' => $ossClient->signUrl($bucket,$object,3600,"GET",null)
            ];
            Storages::query()->create($params);
            return $params;
        }
        else{
            return [];
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
            if(empty($storage)){
                return '';
            }
            $signedUrl = $ossClient->signUrl($bucket,$storage->path,3600,"GET",null);

            return $signedUrl ?? '';
        } catch (OssException $e) {
            return $e->getMessage();
        }
    }

    public function getSignUrlNameSize($id): array
    {
        try {
            $accessKeyId = env("ALIBABA_OSS_ACCESS_KEY");
            $accessKeySecret = env("ALIBABA_OSS_SECRET_KEY");
            $endpoint = env("ALIBABA_OSS_ENDPOINT");
            $bucket = env("ALIBABA_OSS_BUCKET");

            $storage = Storages::query()->find($id);

            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            if(empty($storage)){
                return [];
            }
            // timeout 1000 years
            $timeout =  1000 * 365 * 24 * 3600;
            $signedUrl = $ossClient->signUrl($bucket,$storage->path,$timeout,"GET",null);

            return [
                'url' => $signedUrl ?? '',
                'name' => $storage->path,
                'size' => $storage->size
            ];
        } catch (OssException $e) {
            return [];
        }
    }

    public function deleteFile($id)
    {
        try {
            $accessKeyId = env("ALIBABA_OSS_ACCESS_KEY");
            $accessKeySecret = env("ALIBABA_OSS_SECRET_KEY");
            $endpoint = env("ALIBABA_OSS_ENDPOINT");
            $bucket = env("ALIBABA_OSS_BUCKET");

            $storage = Storages::query()->find($id);
            if(empty($storage)){
                return false;
            }
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->deleteObject($bucket, $storage->path);
            $storage->delete();
            return true;
        } catch (OssException $e) {
            return $e->getMessage();
        }
    }
}