<?php

namespace App\Http\Controllers;

use App\Models\role;
use App\Models\User;
use App\Models\UserType;
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
            $uploadController = new UploadController();
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
//            $user->avatar = $uploadController->UploadFile($request->file('avatar'));
            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
            ], 200);

        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'User failed register',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request){
        // validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'fcm_token' => 'required|string',
        ]);
        $model = User::query()->where('email', $request->email)->first();
        $model->fcm_token = $request->fcm_token;
        if(!empty($model['avatar'])){
            $cloudController = new UploadController();
            $model['avatar'] = $cloudController->getSignedUrl($model['avatar']);
        }
        if(empty($model)){
            return request()->json([
                'status' => 500,
                'message' => 'Error',
            ]);
        }
        if(!Hash::check($request->password, $model->password)){
            return request()->json([
                'status' => 500,
                'message' => 'Password or Email incorrect',
            ]);
        }
        $token =$model->createToken(config('app.name'))->plainTextToken;
        return response()->json([
            'status' => 200,
            'message' => 'Sucess',
            'user' => $model,
            'token' => $token,
        ]);
        $model->save(
            [
                'fcm_token' => $request->fcm_token,
            ]
        );

    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Success',
        ]);
    }

//    public function addAvatar(Request $request){
//        try{
//            $cloudController = new UploadController();
//            $user = auth()->user();
//            $this->deleteAvatar($user->avatar!=null);
//            $user->save();
//            $user->avatar = $cloudController->UploadFile($request->file('image'));
//            $user->save();
//            return response()->json([
//                'message' => 'User successfully add avatar',
//                'user' => $user
//
//            ], 200);
//        }
//        catch(Exception $e){
//            return response()->json([
//                'message' => 'User failed add avatar',
//                'error' => $e->getMessage()
//            ], 500);
//        }
//    }

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

    public function userinfo()
    {
     try{
         //show avatar as link
         $cloudController = new UploadController();
         $user = auth()->user();
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

}
