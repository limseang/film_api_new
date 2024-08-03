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

        // user language
        if(!$locale){
            // Take the default local language
            $locale = config('app.locale');
        }

        if(!is_null(Auth::user())){
            $user = DB::table('users')->find(Auth::user()->id);
            $locale = $user->language;
        }
        App::setlocale($locale);
        // get the language from the request header done
        $response= $next($request);

        // set Content Language header to the language
        //  $response->headers->set('Accept-Language', $locale);

         return $response;
    }
}
