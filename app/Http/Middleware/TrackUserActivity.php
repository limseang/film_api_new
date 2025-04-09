<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FirebaseRealTimeService;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    protected $firebaseService;
    
    public function __construct(FirebaseRealTimeService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (auth()->check()) {
            $user = auth()->user();
            
            // Update Firebase with user's online status
            $this->firebaseService->updateUserStatus($user->id, true, [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role_id,
                'is_admin' => $user->role_id === 1 || $user->role_id === 2,
                'last_url' => $request->fullUrl(),
                'last_ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }
        
        return $next($request);
    }
}