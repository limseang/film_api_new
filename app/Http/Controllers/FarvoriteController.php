<?php

namespace App\Http\Controllers;

use App\Models\Farvorite;
use App\Models\Film;
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
            $farvorite = Farvorite::with('film', 'article')->where('user_id', Auth()->user()->id)->orderByDesc('id')->paginate(21, ['*'], 'page', $page);
            if($farvorite->item_type = 2){
                $film = Film::find($farvorite->item_id);
                $uploadController = new UploadController();
                $data = [
                    'id' => $film->id,
                    'title' => $film->title,
                    'release_date' => $film->release_date,
                    'poster' => $film->poster ? $uploadController->getSignedUrl($film->poster) : null,
                    'rating' => (string) $this->countRate($film->id),
                    'type' => $film->types ? $film->types->name : null,
                    'created_at' => $film->created_at,

                ];
            }
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
  public function delete($id)
  {
      try{
            $farvorite = Farvorite::find($id);
            if(!$farvorite){
                return response()->json([
                    'message' => 'Farvorite not found',
                ], 404);
            }
            //only user can delete their own farvorite
            if($farvorite->user_id != Auth()->user()->id){
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Farvorite deleted failed',
                'error' => $e->getMessage()
            ], 400);
      }

  }
}
