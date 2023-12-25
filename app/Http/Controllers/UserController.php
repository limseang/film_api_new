<?php

namespace App\Http\Controllers;

use App\Models\Artical;
use App\Models\Film;
use App\Models\role;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\UserType;
use App\Services\PushNotificationService;
use Exception;
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
                    if (filter_var($item->avatar, FILTER_VALIDATE_URL)) {
                    } else {
                        $item->avatar = $cloudController->getSignedUrl($item->avatar);
                    }
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
                    'message' => 'email Account not much',
                ]);
            }
            // check password
            if(!Hash::check($request->password, $user->password)){
                return response()->json([
                    'status' => 401,
                    'message' => 'passwordAccount not much',
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
    public function logout(Request $request)
    {
        try{
            $request->user()->currentAccessToken()->delete();
            UserLogin::where('user_id', auth()->user()->id)->first()->delete();
            return response()->json([
                'message' => 'User successfully signed out'
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'message' => 'User failed signed out',
                'error' => $e->getMessage()
            ], 500);
        }
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

             if (filter_var($user['avatar'], FILTER_VALIDATE_URL)) {

             }
             else{
                 $user['avatar'] = $cloudController->getSignedUrl($user['avatar']);
             }

         }
         else{
             $user['avatar'] = 'https://cinemagickh.oss-ap-southeast-7.aliyuncs.com/uploads/2023/05/31/220e277427af033f682f8709e54711ab.webp';
         }
         if(is_numeric($user['point']) && substr($user['point'], 0 ,1) === '-'){
              //return point in string
                $user['point'] = '1';
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

 public function socialLogin(Request $request)
    {
        try{
            $user = new User();
            //check userUUID has or not
            $userUUID = User::where('userUUID',$request->userUUID)->first();
            if(!$userUUID){
                $user->userUUID = $request->userUUID;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->avatar = $request->avatar;
                $user->comeFrom = $request->comeFrom;
                $user->save();
            }
            $user = User::where('userUUID',$request->userUUID,)->first();

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


    public function sendNotification(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required',
        ]);
        $data = [
            'token' => $request->fcm_token,
            'title' => 'New Artical'.','.$request->id,
            'body' => 'New Artical has been created',
            'data' => [
                'id' => '40',
                'type' => '1',
            ]
        ];
        PushNotificationService::pushNotification($data);
        return response()->json([
            'message' => 'successfully',
            'notification' => $data
        ], 200);

    }

    public function sendNotificationGlobe(Request $request)
    {
        try{
            $user = UserLogin::all();
            foreach ($user as $item){
                $data = [
                    'token' => $item->fcm_token,
                    'title' => $request->title,
                    'body' => $request->body,
                    'data' => [
                    'id' => '',
                    'type' => '1',
                ]
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

    public function editName(Request $request)
    {
        try{
            $user = auth()->user();
            $user->name = $request->name;
            $user->save();
            return response()->json([
                'message' => 'successfully',
                'name' => $user->name
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function editPassword(Request $request)
    {
        try{
            $user = auth()->user();
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json([
                'message' => 'successfully',
                'user' => $user
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 500);
        }

    }


    /* Todo: Blade Method */
    public function loginBlade(Request $request)

    {
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
                    'message' => 'email Account not much',
                ]);
            }
            // check password
            if(!Hash::check($request->password, $user->password)){
                return response()->json([
                    'status' => 401,
                    'message' => 'passwordAccount not much',
                ]);
            }
            // create token
            $token = $user->createToken('auth_token')->plainTextToken;
            $count = User::all()->count();
            $article = Artical::all()->count();
            $film = Film::all()->count();
            return view('home', compact('token','count','article','film'));
        }
        catch(Exception $e){
            return response()->json([
                'status' => 501,
                'message' => 'Error',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function countAllUser(){
        try{
            $user = User::all();
            $count = count($user);
            return view('home', compact('count'));
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }



}
