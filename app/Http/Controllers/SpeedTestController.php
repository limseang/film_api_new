<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpeedTestController extends Controller
{
    public function serveFile()
    {
        $filePath = 'test-file.bin'; // Ensure this file exists in the storage/app directory
        return response()->file(storage_path('app/' . $filePath));
    }
}
