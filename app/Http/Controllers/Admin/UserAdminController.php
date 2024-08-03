<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function lang($lang){
        $user = User::find(Auth::id());
        $user->language = $lang;
        $user->save();
        return redirect()->back();
    }
}
