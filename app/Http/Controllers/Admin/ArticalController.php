<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\ArtistDataTable;
use App\Models\Artist;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\AlibabaStorage;

class ArticalController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(ArtistDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.artist')]];
        return $dataTable->render('artist.index', $data);
    }
}
