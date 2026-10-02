<?php

namespace App\Providers;

use App\Contracts\Search\SearchEngineInterface;
use App\Models\Audiobook;
use App\Models\Book;
use App\Models\DevotionalContent;
use App\Models\StorySeries;
use App\Observers\ContentPublicationObserver;
use App\Services\Search\MySqlSearchEngine;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            SearchEngineInterface::class,
            MySqlSearchEngine::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::defaults([
            'locale' => config('localization.default_locale', 'en'),
        ]);

        // Implicitly grant "Super Admin" role all permission checks using can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Configure production-safe rate limiters
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('downloads', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('reviews', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Contributor Reward Observer registration
        Book::observe(ContentPublicationObserver::class);
        Audiobook::observe(ContentPublicationObserver::class);
        DevotionalContent::observe(ContentPublicationObserver::class);
        StorySeries::observe(ContentPublicationObserver::class);
    }
}
