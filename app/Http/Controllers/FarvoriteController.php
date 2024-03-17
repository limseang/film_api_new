<?php

namespace App\Http\Controllers;

use App\Models\Farvorite;
use App\Models\Film;
use App\Models\Rate;
use Illuminate\Http\Request;

class FarvoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        try{
            $farvorite = Farvorite::with('film', 'article')->orderByDesc('id')->paginate(21, ['*'], 'page', $page);
            $data = $farvorite->map(function ($farvorite) {
                return [
                    'id' => $farvorite->id,
                    'user_id' => $farvorite->user_id,
                    'item_type' => $farvorite->item_type,
                    'item_id' => $farvorite->item_id,
                    'status' => $farvorite->status,
                ];
            });
            return $this->sendResponse([
                'current_page' => $farvorite->currentPage(),
                'last_page' => $farvorite->lastPage(),
                'per_page' => $farvorite->perPage(),
                'total' => $farvorite->total(),
                'data' => $data,
            ]);
        }
        catch(\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function ownFavorite(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        try{
            $farvorite = Farvorite::with('film', 'article')->where('user_id', Auth()->user()->id)->where('item_type', 2)->orderByDesc('id')->paginate(21, ['*'], 'page', $page);

            $data = $farvorite->map(function ($farvorite) {
                $uploadController = new UploadController();
                $farvorites = [
                    'farvorite_id' => $farvorite->id,
                    'id' => $farvorite->film->id,
                    'title' => $farvorite->film->title,
                    'release_date' => $farvorite->film->release_date,
                    'rating' => (string) $this->countRate($farvorite->film->id),
                    'poster' => $farvorite->film->poster ? $uploadController->getSignedUrl($farvorite->film->poster) : null,
                    'type' => $farvorite->film->types ? $farvorite->film->types->name : null,
                    'created_at' => $farvorite->film->created_at,
                ];
                return $farvorites;
            });
            return $this->sendResponse([
                'current_page' => $farvorite->currentPage(),
                'last_page' => $farvorite->lastPage(),
                'per_page' => $farvorite->perPage(),
                'total' => $farvorite->total(),
                'favorite' => $data,
            ]);

        }
        catch(\Exception $e){
            return $this->sendError($e->getMessage());
        }

    }
    public function countRate($film_id){
        $rates = Rate::where('film_id',$film_id)->get();
        $total = 0;
        foreach ($rates as $rate){
            $total += $rate->rate;
        }
        if(count($rates) == 0){
            return 0;
        }
        return number_format($total/count($rates), 1);
    }

    public function changeStatus($id)
    {
        try{
            $farvorite = Farvorite::find($id);
            if(!$farvorite){
                return response()->json([
                    'message' => 'Farvorite not found',
                ], 404);
            }
            if($farvorite->user_id != Auth()->user()->id){
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
            $farvorite->status = !$farvorite->status;
            $farvorite->save();
            return $this->sendResponse($farvorite);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }

    }


    public function detail($id)
    {
        try{
            $farvorite = Farvorite::with('film', 'article')->find($id);
            if(!$farvorite){
                return response()->json([
                    'message' => 'Farvorite not found',
                ], 404);
            }
            if($farvorite->type == 2){
                $film = Film::find($farvorite->item_id);
                $data = [
                    'id' => $farvorite->id,
                    'user_id' => $farvorite->user_id,
                    'item_type' => $farvorite->item_type,
                    'item_id' => $farvorite->item_id,
                    'status' => $farvorite->status,
                    'film' => $film,
                ];
            }
            return $this->sendResponse($data);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }

    }

    public function create(Request $request)
    {
        try{
            $farvorite = Farvorite::create([
                'user_id' => Auth()->user()->id,
                'item_type' => $request->item_type,
                'item_id' => $request->item_id,
                'status' => 1,
            ]);
            return $this->sendResponse($farvorite);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     */
  public function delete(Request $request)
  {
      try{

            $farvorite = Farvorite::find($request->id);
            dd($farvorite);

//            if(!$farvorite){
//                return response()->json([
//                    'message' => 'Farvorite not found',
//                ], 404);
//            }
            if($farvorite->user_id != Auth()->user()->id){
                return $this->sendError('Unauthorized');
            }
            $farvorite->delete();
            return $this->sendResponse($farvorite);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
      }

  }
}
