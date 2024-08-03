<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated',
            ], 401);
        }

        if ($user->role_id == 2 || $user->role_id == 1 || $user->id == $request->id) {
            return $next($request);
        }

        return response()->json([
            'message' => 'You do not have permission for this function',
        ], 401);
    }
}
