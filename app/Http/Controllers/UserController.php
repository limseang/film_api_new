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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

//            $users = $user->toArray();
//            $response =[];
//            if(!empty($users)){
//                foreach ($users as  $key=> $value){
//                    $response[$key] = (string)$value;
//                }
//            }


            return $this->sendResponse($user);



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

    // Todo: Payment Link
    public function createPaymentLink(Request $request)
    {

        // Generate a unique transaction ID
        $transaction_id = time(); // Using time() to generate a unique transaction ID as in the example
        $amount = $request->amount;
        $user_id = auth()->user()->id;
        $remark = $request->remark;

        // Generate the success URL with transaction_id and user_id
        $success_url = "https://www.raksmeypay.com/payment/sample-success-page?transaction_id={$transaction_id}&token={$user_id}";
        $encoded_success_url = urlencode($success_url);

        $profile_key = "188bedcc6235ff1617dde3dd78deb1e49408b7a42db46f71f572c1a82569ee4d8c5a4848fc21c0df";
        $hash = sha1($profile_key . $transaction_id . $amount . $encoded_success_url . $remark);

        $parameters = [
            "transaction_id" => $transaction_id,
            "amount" => $amount,
            "success_url" => $encoded_success_url,
            "remark" => $remark,
            "hash" => $hash
        ];

        $queryString = http_build_query($parameters);
        $my_payment_url = "https://www.raksmeypay.com/payment/request/4d9312574ca3ebb2ead00027f18d1ef8";
        $payment_link_url = $my_payment_url . "?" . $queryString;

        // Generate QR code URL
        $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($payment_link_url);

        return response()->json([
            'payment_link' => $payment_link_url,
            'qr_code_url' => $qr_code_url,
            'transaction_id' => $transaction_id,
            'success_url' => $success_url
        ]);
    }


    public function handlePaymentCallback(Request $request)
    {
        // Validate the request


        $transaction_id = $request->transaction_id;
        $received_hash = $request->hash;

        // Calculate the hash to verify
        $profile_key = "188bedcc6235ff1617dde3dd78deb1e49408b7a42db46f71f572c1a82569ee4d8c5a4848fc21c0df";
        $calculated_hash = sha1($profile_key . $transaction_id);

        // Verify the hash
        if ($calculated_hash !== $received_hash) {
            return response()->json(['error' => 'Invalid hash'], 400);
        }

        // Process the transaction, update database, etc.
        // Here you would typically update the transaction status in your database

        return response()->json(['success' => true, 'transaction_id' => $transaction_id]);
    }

    public function generatePaymentQrCode(Request $request)
    {


        // Get the amount from the request
        $amount = $request->input('amount');

        // Generate a unique transaction ID
        $transaction_id = time(); // Using current timestamp for uniqueness

        // Payment URL and success URL
        $my_payment_url = "https://www.raksmeypay.com/payment/request/4d9312574ca3ebb2ead00027f18d1ef8";
        $profile_key = "188bedcc6235ff1617dde3dd78deb1e49408b7a42db46f71f572c1a82569ee4d8c5a4848fc21c0df";
        $success_url = "http://localhost/sample-payment/payment_success.php?transaction_id={$transaction_id}&amount={$amount}";

        // Generate hash
        $hash = sha1($profile_key . $transaction_id . $amount . $success_url);

        // Build query string
        $parameters = [
            "transaction_id" => $transaction_id,
            "amount" => $amount,
            "success_url" => urlencode($success_url),
            "hash" => $hash
        ];

        $queryString = http_build_query($parameters);
        $payment_link_url = $my_payment_url . "?" . $queryString;

        // Make the API call to get the QR code data
        $response = Http::get($payment_link_url);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to generate payment link'], 500);
        }

        // Extract the data from the response
        $data = $response->body(); // Assuming the QR code data is directly in the response body

        // Generate QR code URL
        $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($data);

        // Return the QR code URL and transaction ID in the response
        return response()->json([
            'success' => 1,
            'payment_link' => $payment_link_url,
            'qr_code_url' => $qr_code_url,
            'transaction_id' => $transaction_id
        ]);
    }

    public function handlePaymentWebhook(Request $request)
    {
        // Log the incoming webhook request for debugging
        Log::info('Payment Webhook received:', $request->all());

        // Validate the webhook request if necessary (e.g., check a signature)
        // $this->validateWebhook($request);

        // Process the webhook data
        $transaction_id = $request->input('transaction_id');
        $status = $request->input('status');
        $amount = $request->input('amount');

        // Handle different event types
        switch ($status) {
            case 'success':
                // Handle successful payment
                $this->handleSuccessfulPayment($transaction_id, $amount);
                break;
            case 'failed':
                // Handle failed payment
                $this->handleFailedPayment($transaction_id, $amount);
                break;
            case 'pending':
                // Handle pending payment
                $this->handlePendingPayment($transaction_id, $amount);
                break;
            default:
                // Handle unknown status
                Log::warning('Unknown payment status:', $status);
                break;
        }

        // Return a response to acknowledge receipt of the webhook
        return response()->json(['status' => 'received'], 200);
    }

    protected function handleSuccessfulPayment($transaction_id, $amount)
    {
        // Update the payment status in the database
        // Notify the user about the successful payment
        // Any other logic you need
        Log::info("Payment successful for transaction ID: $transaction_id, Amount: $amount");
    }

    protected function handleFailedPayment($transaction_id, $amount)
    {
        // Update the payment status in the database
        // Notify the user about the failed payment
        // Any other logic you need
        Log::info("Payment failed for transaction ID: $transaction_id, Amount: $amount");
    }

    protected function handlePendingPayment($transaction_id, $amount)
    {
        // Update the payment status in the database
        // Notify the user about the pending payment
        // Any other logic you need
        Log::info("Payment pending for transaction ID: $transaction_id, Amount: $amount");
    }

    public function handleTelegramLogin(Request $request)
    {
        $data = $request->all();

        // Log incoming data for debugging
        Log::info('Telegram Login Data:', $data);

        // Ensure all required parameters are present
        log::error('hash array', $data['hash']);
        log::error('id array', $data->id);
        if (!isset($data->hash, $data->id, $data->auth_date)) {
            Log::error('Missing required parameters.', $data);
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        // Verify the Telegram data
        if ($this->verifyTelegramData($data)) {
            try {
                // Define default values for not-nullable fields
                $defaultName = $data->username ?? 'No Name';
                $defaultLanguage = 'en'; // Assuming 'en' as default if language is not provided


             //check userUUID if exist
                if(!$data->id){
                    Log::error('Invalid Telegram data verification failed.', $data);
                    return response()->json(['error' => 'Invalid Telegram login data'], 401);

                }
                $user = User::where('userUUID', $data->id)->first();
                if(!$user){
                    $user = new User();
                    $user->userUUID = $data->id;
                    $user->name = $defaultName;
                    $user->avatar = $data->photo_url ?? '';
                    $user->comeFrom = 'telegram';
                    $user->language = $defaultLanguage;
                    $user->save();
                }
                Log::info('User created/updated:', $user->toArray());


                // Verify if user object is not null
                if (!$user) {
                    Log::error('User creation/update returned null.', $data);
                    return response()->json(['error' => 'Failed to create or update user.'], 500);
                }

                // Generate a personal access token for the user
                $token = $user->createToken('telegram-login')->plainTextToken;

                // Log successful login
                Log::info('User logged in:', $user->toArray());

                return response()->json([
                    'token' => $token,
                    'user' => $user,
                ], 200);
            } catch (\Exception $e) {
                // Log the error details
                Log::error('Error creating/updating user:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return response()->json(['error' => 'Server error. Please try again later.'], 500);
            }
        } else {
            Log::error('Invalid Telegram data verification failed.', $data);
            return response()->json(['error' => 'Invalid Telegram login data'], 401);
        }
    }


    public function verifyTelegramData($data)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

        // Step 1: Ensure 'hash' exists in the data
        if (!isset($data['hash'])) {
            Log::error('Telegram login: hash not found', $data);
            return false;
        }

        // Step 2: Prepare the check string by excluding 'hash' and any other unexpected fields
        // Only keep the keys that are defined by Telegram in the response
        $expectedKeys = ['auth_date', 'first_name', 'id', 'last_name', 'photo_url', 'username'];
        $filteredData = collect($data)
            ->only($expectedKeys) // Only include expected Telegram fields
            ->map(function ($value, $key) {
                return "$key=$value";
            })
            ->sortKeys() // Sort keys in alphabetical order
            ->implode("\n"); // Join as a single string with newline separator

        // Log the check string for debugging
        Log::info('Check string for Telegram verification:', ['check_string' => $filteredData]);

        // Step 3: Create the secret key using your bot's token
        $secretKey = hash('sha256', $botToken, true);

        // Step 4: Generate the hash for comparison using HMAC-SHA256
        $generatedHash = hash_hmac('sha256', $filteredData, $secretKey);

        // Step 5: Log both the generated and received hashes for debugging
        Log::info('Generated hash:', ['generated_hash' => $generatedHash]);
        Log::info('Received hash:', ['received_hash' => $data['hash']]);

        // Step 6: Compare the generated hash with the received hash
        if (!hash_equals($generatedHash, $data['hash'])) {
            Log::error('Hash mismatch detected.', [
                'generated_hash' => $generatedHash,
                'received_hash' => $data['hash']
            ]);
            return false;
        }

        return true;
    }
















}
