<?php

namespace App\Http\Controllers;

use App\Models\PremiumUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PremiumUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $uploadController = new UploadController();
            $premiumUser = PremiumUser::with('user')->get();
            $data = [];
            foreach ($premiumUser as $item){
                //if user avtar is null

                $data[] = [
                    'id' => $item->id,
                    'user' => $item->user->name,
                    'avtar' =>   $item->user->avtar = $item->user->avtar == null ? 'https://cinemagickh.oss-ap-southeast-7.aliyuncs.com/uploads/2023/05/31/220e277427af033f682f8709e54711ab.webp' : $uploadController->getSignedUrl($item->user->avtar),
                    'payment_id' => $item->payment_id,
                    'payment_method' => $item->payment_method,
                    'register_date' => $item->register_date,
                    'expired_date' => $item->expired_date,
                    'recipe_id' => $uploadController->getSignedUrl($item->recipe_id),
                    //if status == 1 show pading 2 aprove 3 reject 4 expired
                    'status' => $item->status == 1 ? 'pending' : ($item->status == 2 ? 'approve' : ($item->status == 3 ? 'reject' : 'expired')),
                ];
            }
            return $this->sendResponse($data );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $premiumUser = new PremiumUser();
            $premiumUser->user_id = auth()->user()->id;
            $premiumUser->payment_id = $request->payment_id;
            $premiumUser->payment_method = $request->payment_method;
            $premiumUser->register_date = $request->register_date;
            $premiumUser->expired_date = Carbon::parse($request->register_date)->addMonth();
            $premiumUser->recipe_id = $uploadController->UploadFile($request->file('recipe_id'));
            $premiumUser->status = 1;
            $premiumUser->save();
            return $this->sendResponse($premiumUser );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }
    }

    public function changeStatus( Request $request)
    {
        try{
            $premiumUser = PremiumUser::find($request->id);
            $premiumUser->status = $request->status;
            $premiumUser->save();
            return $this->sendResponse($premiumUser );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }
    }

   public function ownPremium()
   {
       try{
           $uploadController = new UploadController();
              $premiumUser = PremiumUser::where('user_id',auth()->user()->id)->get();
              $data = [];
            if($premiumUser){
                foreach ($premiumUser as $item){
                    $data[] = [
                        'id' => $item->id,
                        'user' => $item->user->name,
                        'avtar' =>   $item->user->avtar == null ? 'https://cinemagickh.oss-ap-southeast-7.aliyuncs.com/uploads/2023/05/31/220e277427af033f682f8709e54711ab.webp' : $uploadController->getSignedUrl($item->user->avtar),
                        'payment_id' => $item->payment_id,
                        'payment_method' => $item->payment_method,
                        'register_date' => $item->register_date,
                        'expired_date' => $item->expired_date,
                        'recipe_id' => $uploadController->getSignedUrl($item->recipe_id),
                        //if status == 1 show pading 2 aprove 3 reject 4 expired
                        'status' => $item->status == 1 ? 'pending' : ($item->status == 2 ? 'approve' : ($item->status == 3 ? 'reject' : 'expired')),
                    ];
                }
            }
            else{
                $data = 'You are not premium user';
            }
              return $this->sendResponse($data );
         }
         catch (\Exception $e){
              return $this->sendError($e->getMessage(), );
       }

   }




    public function destroy($id)
    {
        try{
            $premiumUser = PremiumUser::find($id);
            $premiumUser->delete();
            return $this->sendResponse($premiumUser );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage(), );
        }
    }
}
