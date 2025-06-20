<?php

namespace App\Providers;

use App\Console\Commands\RestoreSystem;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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

        if (app()->isProduction()) {
            URL::macro('alternateHasCorrectSignature', function (HttpRequest $request, $absolute = true, array $ignoreQuery = []) {
                $ignoreQuery[] = 'signature';

                $absoluteUrl = url($request->path());
                $url = $absolute ? $absoluteUrl : '/' . $request->path();

                $queryString = collect(explode('&', (string) $request
                    ->server->get('QUERY_STRING')))
                    ->reject(fn($parameter) => in_array(Str::before($parameter, '='), $ignoreQuery))
                    ->join('&');

                $original = rtrim($url . '?' . $queryString, '?');

                // Use the application key as the HMAC key
                $key = config('app.key'); // Ensure app.key is properly set in .env

                if (empty($key)) {
                    return false;
                }

                $signature = hash_hmac('sha256', $original, $key);
                return hash_equals($signature, (string) $request->query('signature', ''));
            });

            URL::macro('alternateHasValidSignature', function (HttpRequest $request, $absolute = true, array $ignoreQuery = []) {
                return URL::alternateHasCorrectSignature($request, $absolute, $ignoreQuery)
                    && URL::signatureHasNotExpired($request);
            });

            Request::macro('hasValidSignature', function ($absolute = true, array $ignoreQuery = []) {
                return URL::alternateHasValidSignature($this, $absolute, $ignoreQuery);
            });
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
