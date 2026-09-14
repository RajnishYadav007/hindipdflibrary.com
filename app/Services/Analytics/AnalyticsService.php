<?php

namespace App\Services\Analytics;

use App\Models\AnalyticsEvent;
use App\Models\Book;
use App\Support\Analytics\AnalyticsEvent as EventCatalog;
use App\Support\Analytics\DeviceClassifier;
use App\Support\Analytics\ReferrerClassifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AnalyticsService
{
    /**
     * Record an analytics event safely.
     * Guaranteed never to throw an exception to the caller.
     *
     * @param  array<string, mixed>  $data
     */
    public function record(string $eventName, array $data = []): ?AnalyticsEvent
    {
        if (! config('analytics.enabled', true)) {
            return null;
        }

        if (! EventCatalog::isValid($eventName)) {
            Log::warning("Analytics rejected invalid event name: {$eventName}");

            return null;
        }

        try {
            // Validate and sanitize book_id if provided
            $bookId = isset($data['book_id']) ? (int) $data['book_id'] : null;
            if ($bookId && ! Book::where('id', $bookId)->exists()) {
                $bookId = null;
            }

            $searchQuery = isset($data['search_query'])
                ? mb_substr(trim((string) $data['search_query']), 0, 255)
                : null;

            $event = AnalyticsEvent::create([
                'event_name' => $eventName,
                'user_id' => isset($data['user_id']) ? (int) $data['user_id'] : null,
                'visitor_id' => isset($data['visitor_id']) ? substr((string) $data['visitor_id'], 0, 64) : null,
                'session_id' => isset($data['session_id']) ? substr((string) $data['session_id'], 0, 64) : null,
                'book_id' => $bookId,
                'author_id' => isset($data['author_id']) ? (int) $data['author_id'] : null,
                'category_id' => isset($data['category_id']) ? (int) $data['category_id'] : null,
                'search_query' => $searchQuery,
                'search_result_count' => isset($data['search_result_count']) ? (int) $data['search_result_count'] : null,
                'source' => isset($data['source']) ? substr((string) $data['source'], 0, 32) : 'other',
                'country' => isset($data['country']) ? substr((string) $data['country'], 0, 8) : 'unknown',
                'device_type' => isset($data['device_type']) ? substr((string) $data['device_type'], 0, 16) : 'unknown',
                'browser' => isset($data['browser']) ? substr((string) $data['browser'], 0, 32) : null,
                'operating_system' => isset($data['operating_system']) ? substr((string) $data['operating_system'], 0, 32) : null,
                'url_path' => isset($data['url_path']) ? substr((string) $data['url_path'], 0, 512) : null,
                'metadata' => isset($data['metadata']) && is_array($data['metadata']) ? $data['metadata'] : null,
                'occurred_at' => $data['occurred_at'] ?? now(),
            ]);

            return $event;
        } catch (Throwable $e) {
            Log::warning('Analytics recording failed quietly: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Capture an event from the current HTTP request.
     *
     * @param  array<string, mixed>  $extra
     */
    public function captureFromRequest(Request $request, string $eventName, array $extra = []): ?AnalyticsEvent
    {
        if ($this->isBot($request)) {
            return null;
        }

        $userAgent = $request->userAgent();
        $referer = $request->header('referer');
        $appHost = $request->getHost();

        $visitorId = $this->getOrCreateVisitorId($request);
        $sessionId = $this->getOrCreateSessionId($request);

        $country = $request->header('CF-IPCountry')
            ?? $request->header('X-Country-Code')
            ?? $request->server('GEOIP_COUNTRY_CODE')
            ?? 'unknown';

        $data = array_merge([
            'user_id' => $request->user()?->id,
            'visitor_id' => $visitorId,
            'session_id' => $sessionId,
            'source' => ReferrerClassifier::classify($referer, $appHost),
            'country' => strtolower($country),
            'device_type' => DeviceClassifier::classifyDevice($userAgent),
            'browser' => DeviceClassifier::detectBrowser($userAgent),
            'operating_system' => DeviceClassifier::detectOs($userAgent),
            'url_path' => '/'.ltrim($request->path(), '/'),
            'occurred_at' => now(),
        ], $extra);

        return $this->record($eventName, $data);
    }

    /**
     * Determine if request originates from a known bot or crawler.
     */
    public function isBot(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        if (empty($userAgent)) {
            return false;
        }

        $botSignatures = config('analytics.bot_signatures', []);

        foreach ($botSignatures as $signature) {
            if (str_contains($userAgent, strtolower($signature))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve or generate a pseudonymous visitor cookie.
     */
    public function getOrCreateVisitorId(Request $request): string
    {
        $cookieName = config('analytics.cookies.visitor_cookie', '_hpl_vid');
        $visitorId = $request->cookie($cookieName);

        if (! empty($visitorId) && is_string($visitorId) && preg_match('/^[a-zA-Z0-9_-]{20,64}$/', $visitorId)) {
            return $visitorId;
        }

        $newVisitorId = Str::random(32);
        $lifetime = config('analytics.cookies.visitor_lifetime', 60 * 24 * 365);

        Cookie::queue($cookieName, $newVisitorId, $lifetime, null, null, false, true, false, 'Lax');

        return $newVisitorId;
    }

    /**
     * Retrieve or generate a rolling session cookie.
     */
    public function getOrCreateSessionId(Request $request): string
    {
        $cookieName = config('analytics.cookies.session_cookie', '_hpl_sid');
        $sessionId = $request->cookie($cookieName);

        if (! empty($sessionId) && is_string($sessionId) && preg_match('/^[a-zA-Z0-9_-]{20,64}$/', $sessionId)) {
            // Re-queue with refreshed inactivity window
            $lifetime = config('analytics.cookies.session_lifetime', 30);
            Cookie::queue($cookieName, $sessionId, $lifetime, null, null, false, true, false, 'Lax');

            return $sessionId;
        }

        $newSessionId = Str::random(24);
        $lifetime = config('analytics.cookies.session_lifetime', 30);

        Cookie::queue($cookieName, $newSessionId, $lifetime, null, null, false, true, false, 'Lax');

        return $newSessionId;
    }
}
