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
        // Start with default locale
        $locale = config('app.locale', 'en');

        // Parse Accept-Language header if present
        if ($request->hasHeader('Accept-Language')) {
            $locale = $this->parseAcceptLanguage($request->header('Accept-Language'));
        }

        // Override with user preference if authenticated
        if (Auth::check()) {
            $user = DB::table('users')->find(Auth::user()->id);

            // Check if user exists and has language property
            if ($user && property_exists($user, 'language') && !empty($user->language)) {
                $locale = $user->language;
            }
        }

        // Validate and normalize the locale
        $locale = $this->normalizeLocale($locale);

        // Set the application locale
        try {
            App::setLocale($locale);
        } catch (\Exception $e) {
            // Fallback to default locale if setting fails
            App::setLocale(config('app.locale', 'en'));
        }

        // Process the request
        $response = $next($request);

        // Optionally set Content-Language header
        // $response->headers->set('Content-Language', $locale);

        return $response;
    }

    /**
     * Parse the Accept-Language header and extract the primary language
     *
     * @param string $acceptLanguage
     * @return string
     */
    private function parseAcceptLanguage($acceptLanguage)
    {
        // Default locale
        $defaultLocale = config('app.locale', 'en');

        if (empty($acceptLanguage)) {
            return $defaultLocale;
        }

        // Parse the Accept-Language header
        // Example: "en_US,en;q=0.9,zh_TW;q=0.8,zh;q=0.7"
        $languages = [];

        // Split by comma
        $parts = explode(',', $acceptLanguage);

        foreach ($parts as $part) {
            // Remove quality value (q=0.9)
            $lang = explode(';', trim($part))[0];

            // Get the language code
            if (!empty($lang)) {
                // Convert en_US to en, zh_TW to zh_TW (keep as is)
                // You can adjust this logic based on your needs
                $languages[] = trim($lang);
            }
        }

        // Return the first language if available
        return !empty($languages) ? $languages[0] : $defaultLocale;
    }

    /**
     * Normalize locale format for Laravel
     * Converts en_US to en, zh_TW to zh-TW, etc.
     *
     * @param string $locale
     * @return string
     */
    private function normalizeLocale($locale)
    {
        // Get available locales from config
        $availableLocales = config('app.available_locales', ['en', 'zh', 'zh_TW']);

        // Clean the locale string
        $locale = trim($locale);

        // Check if exact match exists
        if (in_array($locale, $availableLocales)) {
            return $locale;
        }

        // Try to extract base language (en_US -> en)
        $baseLanguage = explode('_', $locale)[0];
        $baseLanguage = explode('-', $baseLanguage)[0];

        if (in_array($baseLanguage, $availableLocales)) {
            return $baseLanguage;
        }

        // Check for locale with different format (en_US vs en-US)
        $alternateFormat = str_replace('_', '-', $locale);
        if (in_array($alternateFormat, $availableLocales)) {
            return $alternateFormat;
        }

        $alternateFormat = str_replace('-', '_', $locale);
        if (in_array($alternateFormat, $availableLocales)) {
            return $alternateFormat;
        }

        // Return default if no match found
        return config('app.locale', 'en');
    }
}
