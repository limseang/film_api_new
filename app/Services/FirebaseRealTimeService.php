<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Exception;
use Illuminate\Support\Facades\Log;

class FirebaseRealTimeService
{
    protected $database;
    
    public function __construct()
    {
        try {
            $firebase = (new Factory)
                ->withServiceAccount(base_path('firebase_credentials.json'))
                ->withDatabaseUri('https://popcornnews-31b43-default-rtdb.firebaseio.com/');
                
            $this->database = $firebase->createDatabase();
        } catch (Exception $e) {
            Log::error('Firebase Realtime DB error: ' . $e->getMessage());
            $this->database = null;
        }
    }
    
    /**
     * Update user online status
     */
    public function updateUserStatus($userId, $isOnline, $userData = [])
    {
        if (!$this->database) {
            return false;
        }
        
        try {
            $reference = $this->database->getReference('users_online/' . $userId);
            
            $data = array_merge([
                'online' => $isOnline,
                'last_active' => time(),
            ], $userData);
            
            $reference->set($data);
            return true;
        } catch (Exception $e) {
            Log::error('Firebase update user status error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get online users
     */
    public function getOnlineUsers($timeThreshold = 300) // 5 minutes threshold
    {
        if (!$this->database) {
            return [];
        }
        
        try {
            $reference = $this->database->getReference('users_online');
            $snapshot = $reference->getSnapshot();
            
            if (!$snapshot->exists()) {
                return [];
            }
            
            $onlineUsers = [];
            $currentTime = time();
            
            foreach ($snapshot->getValue() as $userId => $userData) {
                // Consider user online if they've been active in the last 5 minutes
                $isRecentlyActive = isset($userData['last_active']) && 
                                   ($currentTime - $userData['last_active']) <= $timeThreshold;
                                   
                $isMarkedOnline = isset($userData['online']) && $userData['online'] === true;
                
                if ($isRecentlyActive && $isMarkedOnline) {
                    $onlineUsers[$userId] = $userData;
                }
            }
            
            return $onlineUsers;
        } catch (Exception $e) {
            Log::error('Firebase get online users error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user status
     */
    public function getUserStatus($userId)
    {
        if (!$this->database) {
            return null;
        }
        
        try {
            $reference = $this->database->getReference('users_online/' . $userId);
            $snapshot = $reference->getSnapshot();
            
            if (!$snapshot->exists()) {
                return null;
            }
            
            return $snapshot->getValue();
        } catch (Exception $e) {
            Log::error('Firebase get user status error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Remove user status when logging out
     */
    public function removeUserStatus($userId)
    {
        if (!$this->database) {
            return false;
        }
        
        try {
            $reference = $this->database->getReference('users_online/' . $userId);
            $reference->remove();
            return true;
        } catch (Exception $e) {
            Log::error('Firebase remove user status error: ' . $e->getMessage());
            return false;
        }
    }
}