<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the language from the request header
        $locale = $request->header('Accept-Language');

        // If no language in header, use default
        if (!$locale) {
            $locale = config('app.locale');
        }

        // Check if user is authenticated
        if (Auth::check()) {
            $user = DB::table('users')->find(Auth::user()->id);

            // Check if user exists and has language property
            if ($user && property_exists($user, 'language') && !empty($user->language)) {
                $locale = $user->language;
            }
        }

        // Set the application locale
        App::setLocale($locale);

        // Process the request
        $response = $next($request);

        // Optionally set Content-Language header
        // $response->headers->set('Content-Language', $locale);

        return $response;
    }
}
