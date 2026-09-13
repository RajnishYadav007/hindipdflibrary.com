<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');
        $supportedLocales = array_keys(config('localization.supported_locales', ['en' => [], 'hi' => []]));

        if ($locale !== null) {
            if (! in_array($locale, $supportedLocales, true)) {
                abort(404, "Unsupported locale: [{$locale}]");
            }

            App::setLocale($locale);
            URL::defaults(['locale' => $locale]);

            try {
                Carbon::setLocale($locale);
            } catch (\Throwable) {
                // Ignore if not present
            }
        } else {
            URL::defaults(['locale' => App::getLocale()]);
        }

        return $next($request);
    }
}
