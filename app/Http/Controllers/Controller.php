<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function sendResponse(
        $data = [],
        int $code = 200,
        string $message = ''

        //pagination data
    ) {
        $message = !empty($message) ? $message :  'success';
        return response()->json([
            'code' => $code,
            'status' => $message,
            'count' => count($data),
            'data' => $data
        ], $code ?? 200);
    }


    public function sendError(
        $data = [],
        int $code = 400,
        string $message = '',

    ) {
        $message = !empty($message) ? $message : 'failed';
        return response()->json([
            'code' => $code ?? 400,
            'status' => $message,
            'data' => $data
        ], $code ?? 400);
    }
}
