<?php

namespace App\Http\Middleware;

use App\Services\Analytics\AnalyticsService;
use App\Support\Analytics\AnalyticsEvent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureAnalyticsEvent
{
    public function __construct(
        protected AnalyticsService $analytics
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        // Defer recording until after response has been flushed to client
        if (function_exists('defer')) {
            defer(fn () => $this->analytics->captureFromRequest($request, AnalyticsEvent::PAGE_VIEW));
        } else {
            $this->analytics->captureFromRequest($request, AnalyticsEvent::PAGE_VIEW);
        }

        return $response;
    }

    /**
     * Determine if the request should trigger a page_view event.
     */
    protected function shouldTrack(Request $request, Response $response): bool
    {
        if (! config('analytics.enabled', true)) {
            return false;
        }

        // Only track successful GET requests
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return false;
        }

        // Ignore configured system/internal paths
        $ignored = config('analytics.ignored_paths', []);
        foreach ($ignored as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        // Ignore known automated crawlers
        if ($this->analytics->isBot($request)) {
            return false;
        }

        // Exclude authenticated administrative previews
        if ($request->user() && $request->user()->is_admin) {
            return false;
        }

        return true;
    }
}
