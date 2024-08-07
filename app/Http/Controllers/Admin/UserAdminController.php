<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\DataTables\UserAdminDataTable;
use App\Models\Role;
use App\Models\UserType;

class UserAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(UserAdminDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.user')]];
        return $dataTable->render('user.index', $data);
    }

    public function create()
    {
        $data['role'] = Role::where('id', '!=',1)->get();
        $data['userType'] = UserType::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('user.index'), 'page' => __('global.user')], ['link' => '#', 'page' => __('global.add')]];
        return view('user.create', $data);

    }
    public function lang($lang){
        $user = User::find(Auth::id());
        $user->language = $lang;
        $user->save();
        return redirect()->back();
    }
}
