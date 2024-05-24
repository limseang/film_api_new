<?php

namespace App\Http\Controllers;

use App\Models\PremiumUser;
use Illuminate\Http\Request;

class PremiumUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $premiumUser = PremiumUser::with('user')->get();
            $data = [];
            foreach ($premiumUser as $item){
                $data[] = [
                    'id' => $item->id,
                    'user' => $item->user->name,
                    'payment_id' => $item->payment_id,
                    'payment_method' => $item->payment_method,
                    'register_date' => $item->register_date,
                    'expired_date' => $item->expired_date,
                    'status' => $item->status,
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
            $premiumUser = new PremiumUser();
            $premiumUser->user_id = $request->user_id;
            $premiumUser->payment_id = $request->payment_id;
            $premiumUser->payment_method = $request->payment_method;
            $premiumUser->register_date = $request->register_date;
            $premiumUser->expired_date = $request->expired_date;
            $premiumUser->recipe_id = $request->recipe_id;
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
              $premiumUser = PremiumUser::where('user_id',auth()->user()->id)->get();
              $data = [];
              foreach ($premiumUser as $item){
                $data[] = [
                     'id' => $item->id,
                     'user' => $item->user->name,
                     'payment_id' => $item->payment_id,
                     'payment_method' => $item->payment_method,
                     'register_date' => $item->register_date,
                     'expired_date' => $item->expired_date,
                     'status' => $item->status,
                ];
              }
              return $this->sendResponse($data );
         }
         catch (\Exception $e){
              return $this->sendError($e->getMessage(), );
       }

   }


    public function edit(PremiumUser $premiumUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PremiumUser $premiumUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
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
