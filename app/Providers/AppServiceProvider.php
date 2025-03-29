<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Activity::saving(function (Activity $activity) {
            $agent = new Agent();
            $platform = $agent->platform();
            $browser = $agent->browser();
            $activity->ip_address = request()->ip();
            $activity->platform = $platform;
            $activity->device = $agent->device();
            $activity->browser_version = $agent->version($browser);
            $activity->browser = $browser;
        });
    }
}
