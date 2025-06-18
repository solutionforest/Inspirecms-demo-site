<?php

namespace App\Providers;

use App\Console\Commands\RestoreSystem;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        if (env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
        
        // Ensure can reach on RestoreSystemCountdown Livewire component
        $schedule = $this->app[\Illuminate\Console\Scheduling\Schedule::class];
        $schedule
            ->command(RestoreSystem::class)
            ->cron("*/" . RestoreSystem::SCHEDULE_IN_MINS . " * * * *",)
            ->onFailure(function () {
                // Handle failure…
            });
    }
}
