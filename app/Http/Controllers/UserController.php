<?php

namespace App\Http\Controllers;

use App\Models\Artical;
use App\Models\Film;
use App\Models\role;
use App\Models\Type;
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

            foreach ($user as $item){
                if(!empty($item['avatar'])){
                    if (filter_var($item['avatar'], FILTER_VALIDATE_URL)) {

                    }
                    else{
                        $item['avatar'] = $cloudController->getSignedUrl($item['avatar']);
                    }
                }
                else{
                    $item['avatar'] = 'https://cinemagickh.oss-ap-southeast-7.aliyuncs.com/uploads/2023/05/31/220e277427af033f682f8709e54711ab.webp';
                }
            }
            $data [] = $user;
            return response()->json([
                'message' => 'success',
                'data' => $data
            ], 200);

        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'error',
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
                'message' => 'success',
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
                'message' => 'success'
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'message' => 'error',
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
                'message' => 'success',
                'avatar' => $cloudController->getSignedUrl($user->avatar)
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'message' => 'error',
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
         $users = $user->toArray();
         $response =[];
         if(!empty($users)){
             foreach ($users as  $key=> $value){
                 $response[$key] = (string)$value;
             }
         }


         return response()->json([
             'message' => 'success',
             'user' => $response,
         ], 200);



     }
     catch (Exception $e){
         return response()->json([
             'message' => 'error',
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
                'message' => 'success',
                'token' => $token,
                'user' => $user
            ]);
        }
        catch(Exception $e){
            return response()->json([
                'status' => 501,
                'message' => 'error',
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
            'message' => 'success',
            'notification' => $data
        ], 200);

    }

    public function sendNotificationGlobeAll(Request $request)
    {
        try{
            $user = UserLogin::all();
            foreach ($user as $item){
                $data = [
                    'token' => $item->fcm_token,
                    'title' => $request->title,
                    'body' => $request->body,
                    'data' => [
                        'id' => $request->id,
                        'type' => $request->type,
                    ]
                ];
                PushNotificationService::pushNotification($data);
            }
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'error',
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
                'message' => 'success',
                'name' => $user->name
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function editPone(Request $request)
    {
        try{
            $user = auth()->user();
            $user->phone = $request->phone;
            $user->save();
            return response()->json([
                'message' => 'success',
                'phone' => $user->phone
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'error',
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
                'message' => 'success',
                'user' => $user
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'message' => 'error',
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

   public function deleteAccount()
   {
       try{
           $user = auth()->user();
           $user->delete();
           return response()->json([
               'message' => 'success',
           ], 200);
       }
       catch (Exception $e){
           return response()->json([
               'message' => 'error',
               'error' => $e->getMessage()
           ], 500);
       }

   }





}
