<?php

namespace App\Providers;

use App\Contracts\Search\SearchEngineInterface;
use App\Services\Search\MySqlSearchEngine;
use Illuminate\Support\Facades\Gate;
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
    }
}
