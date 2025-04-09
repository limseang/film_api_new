<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UploadController;
use Illuminate\Http\Request;
use App\Http\DataTables\RequestFilmDataTable;
use App\Models\RequestFilm;
use App\Models\User;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\DB;
use App\Constant\RolePermissionConstant;
use Exception;

class RequestFilmController extends Controller
{
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(RequestFilmDataTable $dataTable)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_DASHBOARD_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc'] = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('Request Film')]];
        return $dataTable->render('request_film.index', $data);
    }

    public function edit($id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_DASHBOARD_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['requestFilm'] = RequestFilm::with('user')->find($id);
        if(!$data['requestFilm']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  'Request film not found'
            ];
            return redirect()->route('request_film.index')->with($notification);
        }
        $data['bc'] = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('request_film.index'), 'page' => __('Request Film')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('request_film.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|in:1,2,3',
            'noted' => 'nullable|string',
        ]);

        try{
            DB::beginTransaction();
            $requestFilm = RequestFilm::find($id);
            if(!$requestFilm){
                $notification = [
                    'type' => 'exception',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => 'Request film not found',
                ];
                return redirect()->route('request_film.index')->with($notification);
            }
            
            $oldStatus = $requestFilm->status;
            $requestFilm->status = $request->status;
            if($request->has('noted') && !empty($request->noted)) {
                $requestFilm->noted = $request->noted;
            }
            $requestFilm->save();
            
            // Send notifications if status changed
            if($oldStatus != $request->status) {
                $pushNotificationService = new PushNotificationService();
                $user = User::where('id', $requestFilm->user_id)->first();
                
                if($user) {
                    $userLogin = UserLogin::where('user_id', $user->id)->get();
                    
                    if($request->status == 2) {
                        // Completed/Approved
                        foreach ($userLogin as $item) {
                            $data = [
                                'token' => $item->fcm_token,
                                'title' => 'Your request film was uploaded successfully',
                                'body' => "Now you can watch it! Please help us by rating the film. Thank you!",
                                'type' => 2,
                                'data' => [
                                    'id' => $requestFilm->id,
                                    'type' => '5',
                                ]
                            ];
                            $pushNotificationService->pushNotification($data);
                        }
                    } else if ($request->status == 3) {
                        // Rejected
                        foreach ($userLogin as $item) {
                            $data = [
                                'token' => $item->fcm_token,
                                'title' => 'Your request film was rejected',
                                'body' => $requestFilm->noted,
                                'type' => 3,
                                'data' => $requestFilm->id
                            ];
                            $pushNotificationService->pushNotification($data);
                        }
                    }
                }
            }
            
            DB::commit();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('request_film.index')->with($notification);
        } catch(Exception $e) {
            DB::rollBack();
            $notification = [
                'type' => 'exception',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => $e->getMessage()
            ];
            return redirect()->back()->withInput()->with($notification);
        }
    }

    public function destroy($id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_DASHBOARD_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $requestFilm = RequestFilm::find($id);
        if(!$requestFilm){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => 'Request film not found',
            ];
            return redirect()->route('request_film.index')->with($notification);
        }
        $requestFilm->delete();   
        $notification = [
            'type' => 'success',
            'icon' => trans('global.icon_success'),
            'title' => trans('global.title_updated'),
            'text' => 'Request film deleted successfully',
        ];
        return redirect()->route('request_film.index')->with($notification);
    }
}