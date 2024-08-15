<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\ArticalDataTable;
use App\Models\Artist;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\AlibabaStorage;
use App\Models\Origin;
use App\Models\Film;
use App\Models\Tag;
use App\Models\Type;

class ArticalController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(ArticalDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.artical')]];
        return $dataTable->render('artical.index', $data);
    }

    public function create()
    {
        $data['origins'] = Origin::where('status',1)->get();
        $data['categories'] = Category::where('status',1)->get();
        $data['type'] = Type::where('status',1)->get();
        $data['tag'] = Tag::where('status',1)->get();
        $data['film'] = Film::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('artical.index'), 'page' => __('sma.artical')], ['link' => '#', 'page' => __('global.add')]];
        return view('artical.create', $data);
    }
}
