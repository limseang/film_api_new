<?php

namespace App\Http\Controllers;

use App\Models\Artical;
use App\Models\Category;
use App\Models\Film;
use App\Models\PremiumUser;
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
                'count' => $user->count(),
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
            $data = [
                'user_id' => $user->id,
                'fcm_token' => $request->fcm_token,
            ];
            return response()->json([
                'message' => 'success',
                'user' => $data,
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

            $token = $user->createToken('auth_token')->plainTextToken;
            return $this->sendResponse([
                'token' => $token,
                'user' => $user,
            ]);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }


    }
    public function logout(Request $request)
    {
        try{
            $request->user()->currentAccessToken()->delete();

            return $this->sendResponse();
        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }
    public function addAvatar(Request $request){
        try{
            $cloudController = new UploadController();
            $user = auth()->user();
            $user->avatar = $cloudController->uploadFile($request->avatar);
            $user->save();
            $data = [
                'avatar' => $cloudController->getSignedUrl($user->avatar)
            ];
            return $this->sendResponse($data);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function AdminHome()
    {
        try{
            $total = User::count();
            $total_film = Film::count();
            $total_artical = Artical::count();
            $toal_category = Category::count();

            $data = [
                'user' => $total,
                'film' => $total_film,
                'artical' => $total_artical,
                'category' => $toal_category
            ];
            return $this->sendResponse($data);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }

    }




    public function userinfo(Request $request)
    {
     try{
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


         return $this->sendResponse($response);



     }
     catch (Exception $e){
         return $this->sendError($e->getMessage());
     }

    }

 public function socialLogin(Request $request)
    {
        try{
            $user = new User();
            $userUUID = User::where('userUUID',$request->userUUID)->first();
            if(!$userUUID){
                $user->userUUID = $request->userUUID;
                $user->name = $request->name ?? 'No Name';
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->avatar = $request->avatar;
                $user->comeFrom = $request->comeFrom;
                $user->save();
            }
            $user = User::where('userUUID',$request->userUUID,)->first();

            $token = $user->createToken('auth_token')->plainTextToken;


            return response()->json([
                'token' => $token,
                'user' => $user->name,
            ]);



        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
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
                'id' => '440',
                'type' => '2',
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
            $fcmToken = [];
            UserLogin::chunk(10, function ($users) use (&$fcmToken){
                foreach ($users as $user) {
                    $fcmToken[] = $user->fcm_token;
//                    dd($fcmToken);
                }
            });
//            dd($fcmToken);
            PushNotificationService::pushMultipleNotification([
                'token' => $fcmToken,
                'title' => 'test',
                'body' => 'test322',
                'data' => [
                    'id' => '1',
                    'type' => '2',
                ]
            ]);
            return response()->json([
                'message' => 'success',
                'notification' => $fcmToken
            ], 200);
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
            $data = [
                'name' => $user->name
            ];
            return $this->sendResponse($data);
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
        }

    }

    public function changePhone(Request $request)
    {
        try{
            $user = auth()->user();
            $user->phone = $request->phone;
            $user->save();
            $data = [
                'phone' => $user->phone
            ];
            return $this->sendResponse($data);
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        try{
            $user = auth()->user();
            //check old password
            if(!Hash::check($request->old_password, $user->password)){
                return response()->json([
                    'message' => 'Old password not match',
                ], 400);
            }
           if(Hash::check($request->new_password, $user->password)){
                return response()->json([
                    'message' => 'New password can not be the same as old password',
                ], 400);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();
            return $this->sendResponse();
        }
        catch (Exception $e){
            return $this->sendError($e->getMessage());
        }

    }




   public function deleteAccount()
   {
       try{
           $user = auth()->user();
           $user->delete();
              return $this->sendResponse();
       }
       catch (Exception $e){
              return $this->sendError($e->getMessage());
       }

   }


   // Todo: Admin
    public function AdminLogin(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return response()->json([
                    'status' => 401,
                    'message' => 'Account not much',
                ]);
            }
            if(!Hash::check($request->password, $user->password)){
                return response()->json([
                    'status' => 401,
                    'message' => 'Account not much',
                ]);
            }
            $role = UserType::where('user_id', $user->id)->first();
            if($role->role_id != 1 || $role->role_id != 2){
                return response()->json([
                    'status' => 401,
                    'message' => 'Account not much',
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        }
        catch(Exception $e){
            return $this->sendError($e->getMessage());
        }

    }





}
