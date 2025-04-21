<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class WebSocketService
{
    /**
     * Store user online status in the database
     * 
     * @param int $userId
     * @param bool $isOnline
     * @param array $userData
     * @return bool
     */
    public function updateUserStatus($userId, $isOnline, $userData = [])
    {
        try {
            // Store user status in database
            $data = array_merge([
                'user_id' => $userId,
                'online' => $isOnline,
                'last_active' => time(),
                'updated_at' => now(),
            ], $userData);
            
            // Use the DB to store online status instead of Firebase
            \DB::table('online_users')->updateOrInsert(
                ['user_id' => $userId],
                $data
            );
            
            return true;
        } catch (Exception $e) {
            Log::error('WebSocket update user status error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all online users
     * 
     * @param int $timeThreshold Seconds to consider a user online (default 5 minutes)
     * @return array
     */
    public function getOnlineUsers($timeThreshold = 300)
    {
        try {
            $currentTime = time();
            $threshold = $currentTime - $timeThreshold;
            
            // Get users who have been active in the last X minutes
            $onlineUsers = \DB::table('online_users')
                ->where('online', true)
                ->where('last_active', '>=', $threshold)
                ->get();
                
            return $onlineUsers->toArray();
        } catch (Exception $e) {
            Log::error('WebSocket get online users error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user status
     * 
     * @param int $userId
     * @return object|null
     */
    public function getUserStatus($userId)
    {
        try {
            return \DB::table('online_users')
                ->where('user_id', $userId)
                ->first();
        } catch (Exception $e) {
            Log::error('WebSocket get user status error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Remove user status when logging out
     * 
     * @param int $userId
     * @return bool
     */
    public function removeUserStatus($userId)
    {
        try {
            \DB::table('online_users')
                ->where('user_id', $userId)
                ->update([
                    'online' => false,
                    'last_active' => time(),
                    'updated_at' => now()
                ]);
                
            return true;
        } catch (Exception $e) {
            Log::error('WebSocket remove user status error: ' . $e->getMessage());
            return false;
        }
    }
}