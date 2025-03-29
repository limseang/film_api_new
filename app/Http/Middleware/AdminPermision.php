<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
class AdminPermision 
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //check user has role_id == 1

        if(auth()->user()->role_id == 1 ){
            return $next($request);
        }

        return response()->json([
            'message' => 'you not has permision with this function',
        ], 401);
    }
}
