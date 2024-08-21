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

    public function uploadVideo(Request $request)
    {
        $video = $request->file('video');
        if(empty($video)){
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'error'
                ]
            ]);
        }
        $result = $this->UploadFile($video, 'Episode');
        if ($result) {
            return response()->json(['success' => true, 'file_id' => $result]);
        }
        return response()->json(['success' => false,'file_id' => 0],);
        
    }
    
}
