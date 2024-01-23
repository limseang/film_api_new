<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try{

            $gifts = Gift::query()->where('expired_date', '>=', date('Y-m-d h:i:s'))->where('quantity', '>', 0)->get();
            $uploadController = new UploadController();
            foreach($gifts as $gift){
                $gift->image = $uploadController->getSignedUrl($gift->image);
            }
            return response()->json([
                'status' => true,
                'message' => 'Gifts List',
                'data' => $gifts
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Gifts List Failed',
                'data' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $gift = Gift :: create([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $uploadController->uploadFile($request->file('image'), 'gifts'),
                'code' => $this->random_strings(5),
                'noted' => $request->noted,
                'point' => $request->point,
                'quantity' => $request->quantity,
                'expired_date' => $request->expired_date,
                'status' => $request->status
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Gift Created',
                'data' => $gift
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Gift Created Failed',
                'data' => $e->getMessage()
            ]);
        }
    }

    public function random_strings($length_of_string)
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($str_result), 0, $length_of_string);
    }



    public function destroy($id)
    {
        try{
            $gift = Gift::find($id);
            $gift->delete();
            return response()->json([
                'status' => true,
                'message' => 'Gift Deleted',
                'data' => $gift
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Gift Deleted Failed',
                'data' => $e->getMessage()
            ]);
        }
    }

    public function showByID($id)
    {
        try{
            $gift = Gift::find($id);
            $uploadController = new UploadController();
            $gift->image = $uploadController->getSignedUrl($gift->image);
            return response()->json([
                'status' => true,
                'message' => 'Gift Detail',
                'data' => $gift
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Gift Detail Failed',
                'data' => $e->getMessage()
            ]);
        }

    }
}
