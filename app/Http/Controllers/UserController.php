<?php

namespace App\Http\Controllers;

use App\Models\role;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\UserType;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    public  function  index(){
        try{
            $cloudController = new UploadController();
           $user = User::all();
            $role = role::all();
            foreach ($user as $item) {
                foreach ($role as $roleItem) {
                    if ($item->role_id == $roleItem->id) {
                        $item->role_id = $roleItem->name;
                    }
                }
                if ($item->avatar != null) {
                    $item->avatar = $cloudController->getSignedUrl($item->avatar);
                }
           }
            return response()->json([
                'message' => 'users retrieved successfully',
                'users' => $user
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'users retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function register(Request $request) {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'phone' => 'required|numeric|unique:users',
                'password' => 'required|string',
            ]);
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
            ], 200);


        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request){
        // validation
        try{
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
            // check user
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return response()->json([
                    'status' => 401,
                    'message' => 'Account not much',
                ]);
            }
            // check password
            if(!Hash::check($request->password, $user->password)){
                return response()->json([
                    'status' => 401,
                    'message' => 'Account not much',
                ]);
            }
            // create token
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'token' => $token,
                'user' => $user
            ]);
        }
        catch(Exception $e){
            return response()->json([
                'status' => 501,
                'message' => 'Error',
                'error' => $e->getMessage()
            ]);
        }


    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Success',
        ]);
    }

    public function addAvatar(Request $request){
        try{
            $cloudController = new UploadController();
            $user = auth()->user();
            $user->avatar = $cloudController->uploadFile($request->avatar, 'avatar');
            $user->save();
            return response()->json([
                'message' => 'successfully',
                'avatar' => $cloudController->getSignedUrl($user->avatar)
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

//    public function deleteAvatar ($avatarId)
//
//    {
//        try{
//            $cloudController = new UploadController();
//            $user = auth()->user();
//            $user->avatar = $cloudController->delete($user->avatar);
//            $user->save();
//            return response()->json([
//                'message' => 'User successfully delete avatar',
//                'user' => $user
//
//            ], 200);
//        }
//        catch(Exception $e){
//            return response()->json([
//                'message' => 'User failed delete avatar',
//                'error' => $e->getMessage()
//            ], 500);
//        }
//
//    }

    public function forgetpwd (Request $request)
    {


    }

    public function userinfo(Request $request)
    {
     try{
         //show avatar as link
         $cloudController = new UploadController();
         $user = auth()->user();
         $user->fcm_token = $request->fcm_token;
         $user->save();
         if(!empty($user['avatar'])){
             $user['avatar'] = $cloudController->getSignedUrl($user['avatar']);
         }
         if($user['point'] >= 1 && $user['point'] <= 100){
             $user['user_type'] = 1;
         }
         if($user['point'] >= 101 && $user['point'] <= 500){
             $user['user_type'] = 2;
         }
         if($user['point'] >= 501 && $user['point'] <= 1000){
             $user['user_type'] = 3;
         }
         if($user['point'] >= 1001 && $user['point'] <= 2000){
             $user['user_type'] = 4;
         }
//         $role = role::find($user['role_id']);
//         $userType = UserType::find($user['user_type']);
//         $user['role_id'] = $role['name'];
//            $user['user_type'] = $userType['name'];


         return response()->json([
             'message' => 'User successfully get info',
             'user' => $user,
         ], 200);



     }
     catch (Exception $e){
         return response()->json([
             'message' => 'User failed add avatar',
             'error' => $e->getMessage()
         ], 500);
     }

    }

 public function appleLogin()
    {
        return Socialite::driver("sign-in-with-apple")
            ->scopes(["name", "email"])
            ->redirect();

    }

    public function appleRedirect()
    {
        $user = Socialite::driver("sign-in-with-apple")->user();
        dd($user);
    }

    public function updateFCM($fcm_token){
        try{
            $user = auth()->user();
            $user->fcm_token = $fcm_token;
            $user->save();
            return response()->json([
                'message' => 'successfully',
                'user' => $user
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function sendNotification(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required',
        ]);
        $data = [
            'token' => $request->fcm_token,
            'title' => 'New Artical',
            'body' => 'New Artical has been created',
            'type' => '1',
            'id' => '1',
        ];
        PushNotificationService::pushNotification($data);

    }

    public function sendNotificationGlobe(Request $request)
    {
        try{
            $user = UserLogin::all();
            foreach ($user as $item){
                $data = [
                    'token' => $item->fcm_token,
                    'title' => $request->title,
                    'body' => $request->body
                ];
                PushNotificationService::pushNotification($data);
            }
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 500);
        }

    }

}
