<?php

namespace App\Http\Controllers;

use App\Models\Artical;
use App\Models\BookMark;
use App\Models\Film;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;

class BookMarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $bookMarks = BookMark::where('status', 1)->get();
            return response()->json([
                'status' => 'success',
                'message' => $bookMarks,
            ]);

        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $bookMark = BookMark::where('user_id', $request->user_id)->where('post_id', $request->post_id)->where('post_type', $request->post_type)->first();
            if($bookMark){
                if($bookMark->status == 1) {
                    $bookMark->status = 2;
                    $bookMark->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => $bookMark,
                    ]);
                }
            }else{
                $bookMark = BookMark::create([

                    'user_id' => auth()->user()->id,
                    'post_id' => $request->post_id,
                    'post_type' => $request->post_type,
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => $bookMark,
                ]);
            }
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function detail($id)
    {
        try{
            $bookMark = BookMark::where('id', $id)->first();

            if($bookMark->post_type == 1){
                $artical = Artical::where('id', $bookMark->post_id)->first();
                return response()->json([
                    'status' => 'success',
                    'message' => $artical,
                ]);
            }

            if($bookMark->post_type == 2){
                $film = Film::where('id', $bookMark->post_id)->first();
                return response()->json([
                    'status' => 'success',
                    'message' => $film,
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => $bookMark,
            ]);

        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ]);
        }

    }

    public function delete(Request $request)
    {
        try{
            $user = User::find(auth()->user()->id);
            $bookMark = BookMark::where('user_id', $user)->where('post_id', $request->post_id)->where('post_type', $request->post_type)->first();
            if(!$bookMark){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong',
                ]);
            }
            $bookMark->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Delete successfully',
            ]);


        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ]);
        }

    }

    public function changeStatus(Request $request)
    {
        try{
            $user = User::find(auth()->user()->id);
            $bookMark = BookMark::where('user_id', $user)->where('post_id', $request->post_id)->where('post_type', $request->post_type)->first();
            if($bookMark){
              $bookMark->status = $request->status;
                $bookMark->save();
                }
                else {
                    $bookMark->status = 1;
                    $bookMark->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => $bookMark,
                    ]);
                }
            }
            catch(\Exception $e){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong',
                ]);
            }

    }






}
