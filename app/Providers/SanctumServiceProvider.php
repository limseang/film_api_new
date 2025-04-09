<?php

namespace App\Providers;

use App\Auth\SanctumGuard;
use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\SanctumServiceProvider as LaravelSanctumServiceProvider;

class SanctumServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register this after the original Sanctum service provider
        $this->app->booted(function () {
            // Override the original guard resolving handler
            // This is safer than trying to rebind the Guard class itself
            $this->app->make(Factory::class)->extend('sanctum', function ($app, $name, array $config) {
                return new RequestGuard(
                    function ($request) use ($app) {
                        // Create a new SanctumGuard with the appropriate parameters
                        // First parameter is the Auth Factory
                        // Second parameter should be the token expiration time in minutes
                        return (new SanctumGuard(
                            $app->make(Factory::class),
                            $config['expiration'] ?? null // Use expiration from config, or null for default
                        ))->__invoke($request);
                    },
                    $app['request'],
                    $app['auth']->createUserProvider($config['provider'] ?? null)
                );
            });
        });
    }
}
