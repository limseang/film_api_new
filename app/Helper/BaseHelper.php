<?php

use Carbon\Carbon;
use App\Constant\UserConstant;
use App\Models\Permission;
use App\Models\RolePermission;

if(!function_exists('isOwner')){
    function isOwner(){
        return auth()->guard('admin')->user()->role_id == UserConstant::ROLE_OWNER;
    }
}
if(!function_exists('authorize')){
    function authorize($permission){
       $permission = Permission::where('name', $permission)->first();
       if(empty($permission)){
           return false;
       }
       if(isOwner()){
           return true;
       }
       $rolePermission = RolePermission::where('role_id', auth()->user()->role_id)->where('permission_id', $permission->id)->first();
         if(empty($rolePermission)){
              return false;
         }
        return true;
       
    }
}

if(!function_exists('authorizeMessage')){
    function authorizeMessage(){
            return  'You are not authorized to access this page';

    }
}

// format date time
if(!function_exists('formatDateTime')){
    function formatDateTime($date){
        return date('d-m-Y h:i:s', strtotime($date));
    }
}

// format date
if(!function_exists('formatDate')){
    function formatDate($date){
        return date('m-d-Y', strtotime($date));
    }
}

// limit description with 10 words
if(!function_exists('limitDescription')){
    function limitDescription($description){
        $words = explode(' ', $description);
        return implode(' ', array_slice($words, 0, 5));
    }
}

if (!function_exists('maskText')) {
    /**
     * Hidden privacy data, phone number, email, etc
     * @param mixed $payload
     * @return string
     */
    function maskText(mixed $payload): string
    {
        return $payload ? substr_replace((string)$payload, '***', 3, 3) : '';
    }
}

if (!function_exists('moneyFormatter')) {

    /**
     * @param float $amount
     * @param string $symbol
     * @param int $decimal
     * @return string
     */
    function moneyFormatter(float $amount, int $decimal = 2, string $symbol = ""): string
    {
        return $symbol . number_format($amount, $decimal);
    }
}

if (!function_exists('moneyFormat')) {

    /**
     * @param float $amount
     * @param string $symbol
     * @param int $decimal
     * @return string
     */
    function moneyFormat($amount, int $decimal = 8, string $symbol = ""): string
    {
        $numberString = rtrim(rtrim(number_format($amount, $decimal), '0'), '.');
        if ($numberString == '0') {
            return '0.00';
        }
        if ($amount < 0) {
            return '-' . $symbol . substr($numberString, 1);
        }
        return $symbol . $numberString;
    }
}

if (!function_exists('uniqueRandomId')) {

    /**
     * @return mixed
     * @throws Exception
     */
    function uniqueRandomId(): int
    {
        return random_int(100000000, 999999999);
    }
}

if (!function_exists('phoneNumberFormatter')) {
    /**
     * @param string $phoneCode
     * @param string $phoneNumber
     * @return string
     */
    function phoneNumberFormatter(string $phoneCode, string $phoneNumber): string
    {
        $phoneNumber = ltrim($phoneNumber, '0');
        return $phoneCode . $phoneNumber;
    }
}
if (!function_exists('hiddenPrivacy')) {
    /**
     * Hidden privacy data, phone number, email, etc
     * @param mixed $payload
     * @return string
     */
    function hiddenPrivacy(mixed $payload): string
    {
        return $payload ? substr_replace((string)$payload, '****', 4, 4) : '';
    }
}
if (!function_exists('spacesForNumber')) {
    /**
     * @param $inputString
     * @param int $splitLength
     * @return string
     */
    function spacesForNumber($inputString, int $splitLength = 4): string
    {
        // 将字符串拆分成数组，每个元素是一个字符 en: Split the string into an array, each element is a character
        $characters = str_split($inputString);
        // 初始化一个新数组来存储添加空格后的字符 en: Initialize a new array to store characters after adding spaces
        $result = [];
        // 遍历原始数组，并在每隔4个字符之后添加一个空格 en: Loop through the original array and add a space after every $splitLength characters
        $count = 0;
        foreach ($characters as $char) {
            $result[] = $char;
            $count++;
            // 如果计数达到4，添加一个空格并重置计数 en: If the count reaches $splitLength, add a space and reset the count
            if ($count === $splitLength) {
                $result[] = ' ';
                $count = 0;
            }
        }
        // 将数组中的字符连接成一个新的字符串 en: Concatenate characters in an array into a new string
        return trim(implode('', $result));
    }

    if(!function_exists('dateTimeFormat')) {
        function dateTimeFormat($date)
        {
            if (empty($date)) {
                return '';
            }
            return Carbon::parse($date)->setTimezone(config('app.timezone'))->format(config('setup.datetime_format'));
        }
    }
    if(!function_exists('dateFormat')) {
        function dateFormat($date, $format = 'd-m-Y')
        {
            if (empty($date)) {
                return '';
            }
            return Carbon::parse($date)->setTimezone(config('app.timezone'))->format(config('setup.date_format'));
        }
    }
}