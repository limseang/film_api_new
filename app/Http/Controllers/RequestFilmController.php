<?php

namespace App\Http\Controllers;

use App\Models\RequestFilm;
use App\Models\User;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class RequestFilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $requestFilms = RequestFilm::where('status', 1)->get();
            $uploadController = new UploadController();
            foreach ($requestFilms as $requestFilm){
                $user = User::where('id', $requestFilm->user_id)->first();
                $requestFilm->user_name = $user->name;
                $requestFilm->user_avatar = $user->avatar ? $uploadController->getSignedUrl($user->avatar) : null;
                $requestFilm->film_image = $uploadController->getSignedUrl($requestFilm->film_image);
            }
            return response()->json([
                'status' => 'success',
                'message' => $requestFilms,
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ],
                500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $uploadController = new UploadController();
            $requestFilm = RequestFilm::create([
                'user_id' => auth()->user()->id,
                'film_name' => $request->film_name,
                'film_link' => $request->film_link,
                'film_image' => $uploadController->UploadFile($request->film_image),
                'film_description' => $request->film_description,
                'noted' => $request->noted,
                'status' => 1

            ]);
            $requestFilm->save();

            $pushNotificationService = new PushNotificationService();
            $user = User::where('role_id', 1)->orWhere('role_id', 2)->get();
            foreach ($user as $item) {
                $userID = $item->id;
                $userLogin = UserLogin::where('user_id', $userID)->get();
                foreach ($userLogin as $item) {
//                    dd($item->fcm_token);
                   $data = [
                       'token' => $item->fcm_token,
                        'title' => 'new request film',
                        'body' => $requestFilm->film_name,
                        'type' => 1,
                        'data' => [
                            'id' => $requestFilm->id,
                            'type' => '4',
                        ]
                    ];

                    $pushNotificationService->pushNotification($data);
                }
            }


            return response()->json([
                'status' => 'success',
                'message' => 'Thank you for your request film',
            ]);


        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ],
                500);
        }
    }

    public function detail($id)
    {
        try{
            $requestFilm = RequestFilm::where('id', $id)->first();

            $user = User::where('id', $requestFilm->user_id)->first();
            $requestFilm->user_name = $user->name;
            $requestFilm->user_avatar = $user->avatar;
            return response()->json([
                'status' => 'success',
                'message' => $requestFilm,
            ]);


        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error ',
                'error' => $e->getMessage()
            ],
                500);
        }
    }

    public function destroy($id)
    {
        try {

            $requestFilm = RequestFilm::where('id', $id)->first();
            if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->id == $requestFilm->user_id) {
                $requestFilm->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Delete successfully',
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'you not has permision with this function',
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $requestFilm = RequestFilm::where('id', $id)->first();
            $requestFilm->status = $request->status;
            $requestFilm->save();
            if($request->status == 2){
                $user = User::where('id', $requestFilm->user_id)->first();
                $userLogin = UserLogin::where('user_id', $user->id)->get();
                foreach ($userLogin as $item) {
                    $data = [
                        'token' => $item->fcm_token,
                        'title' => 'your request film accepted',
                        'body' => $requestFilm->film_name,
                        'type' => 2,
                        'data' => [
                            'id' => $requestFilm->id,
                            'type' => '5',
                        ]
                    ];
                    $pushNotificationService = new PushNotificationService();
                    $pushNotificationService->pushNotification($data);
                }
            }
            else if ($request->status == 3){
                $requestFilm->noted = $request->noted;
                $user = User::where('id', $requestFilm->user_id)->first();
                $userLogin = UserLogin::where('user_id', $user->id)->get();
                foreach ($userLogin as $item) {
                    $data = [
                        'token' => $item->fcm_token,
                        'title' => 'your request film was rejected',
                        'body' => $requestFilm->noted,
                        'type' => 3,
                        'data' => $requestFilm->id
                    ];
                    $pushNotificationService = new PushNotificationService();
                    $pushNotificationService->pushNotification($data);
                }
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Update successfully',
            ]);




        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ]);
        }
    }
}
