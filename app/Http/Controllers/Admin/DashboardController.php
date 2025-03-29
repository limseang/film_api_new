<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('lang');
    }
    
    public function index()
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => '#', 'page' => __('global.dashboard')]];
        return view('dashboard', $data);
    }
}
