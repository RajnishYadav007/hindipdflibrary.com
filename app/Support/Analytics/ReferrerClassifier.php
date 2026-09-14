<?php

namespace App\Support\Analytics;

class ReferrerClassifier
{
    public const SOURCE_GOOGLE = 'google';

    public const SOURCE_DIRECT = 'direct';

    public const SOURCE_SOCIAL = 'social';

    public const SOURCE_REFERRAL = 'referral';

    public const SOURCE_OTHER = 'other';

    /**
     * Known social network host fragments.
     *
     * @var array<int, string>
     */
    protected static array $socialHosts = [
        'facebook.com',
        'm.facebook.com',
        'twitter.com',
        'x.com',
        't.co',
        'instagram.com',
        'youtube.com',
        'linkedin.com',
        'reddit.com',
        'pinterest.com',
        'whatsapp.com',
        'web.whatsapp.com',
        'telegram.org',
        't.me',
        'quora.com',
    ];

    /**
     * Classify an incoming HTTP Referer URL into a deterministic source bucket.
     */
    public static function classify(?string $refererUrl, ?string $appHost = null): string
    {
        if (empty($refererUrl)) {
            return self::SOURCE_DIRECT;
        }

        $host = parse_url($refererUrl, PHP_URL_HOST);

        if (! $host) {
            return self::SOURCE_OTHER;
        }

        $host = strtolower($host);

        // Check if internal referrer
        if ($appHost && ($host === strtolower($appHost) || str_ends_with($host, '.'.strtolower($appHost)))) {
            return self::SOURCE_OTHER;
        }

        // Check Google search
        if (str_contains($host, 'google.') || str_contains($host, 'googleadservices.')) {
            return self::SOURCE_GOOGLE;
        }

        // Check Social
        foreach (self::$socialHosts as $socialHost) {
            if ($host === $socialHost || str_ends_with($host, '.'.$socialHost)) {
                return self::SOURCE_SOCIAL;
            }
        }

        return self::SOURCE_REFERRAL;
    }
}
