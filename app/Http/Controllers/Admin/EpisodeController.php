<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\AlibabaStorage;
use App\Models\Film;

class EpisodeController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function create($filmId)
    {
        $data['film'] = Film::find($filmId);
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('film.index'), 'page' => __('sma.show_episode')], ['link' => '#', 'page' => __('sma.add')]];
        return view('episode.create', $data);
    }
    
}
