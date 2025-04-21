<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\WebSocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlineStatusController extends Controller
{
    protected $webSocketService;
    
    public function __construct(WebSocketService $webSocketService)
    {
        $this->webSocketService = $webSocketService;
    }
    
    /**
     * Update user online status
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }
        
        $userData = [
            'app_version' => $request->input('app_version'),
            'device_info' => $request->input('device_info'),
            'screen' => $request->input('screen'),
        ];
        
        $status = $request->input('status', 'online') === 'online';
        
        $success = $this->webSocketService->updateUserStatus($user->id, $status, $userData);
        
        return response()->json([
            'success' => $success
        ]);
    }
    
    /**
     * Get current online users count
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOnlineCount()
    {
        $onlineUsers = $this->webSocketService->getOnlineUsers();
        
        return response()->json([
            'success' => true,
            'count' => count($onlineUsers)
        ]);
    }
    
    /**
     * Mark user as offline on logout
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutStatus()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }
        
        $success = $this->webSocketService->removeUserStatus($user->id);
        
        return response()->json([
            'success' => $success
        ]);
    }
}