<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Analytics Master Switch
    |--------------------------------------------------------------------------
    */
    'enabled' => env('ANALYTICS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Data Retention Policies (in days)
    |--------------------------------------------------------------------------
    | Raw events are pruned aggressively to conserve shared-hosting disk space.
    | Aggregated daily reporting tables are preserved for long-term intelligence.
    */
    'retention' => [
        'raw_events_days' => (int) env('ANALYTICS_RAW_RETENTION_DAYS', 30),
        'daily_aggregates_days' => (int) env('ANALYTICS_DAILY_RETENTION_DAYS', 365),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie Configuration
    |--------------------------------------------------------------------------
    | Privacy-safe pseudonymous cookies without personal information.
    */
    'cookies' => [
        'visitor_cookie' => '_hpl_vid',
        'visitor_lifetime' => 60 * 24 * 365, // 1 year (minutes)
        'session_cookie' => '_hpl_sid',
        'session_lifetime' => 30, // 30 minutes inactivity window (minutes)
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored Paths & Patterns
    |--------------------------------------------------------------------------
    | Paths that should not be tracked by the public page view middleware.
    */
    'ignored_paths' => [
        'admin*',
        'livewire*',
        'sitemap.xml',
        'robots.txt',
        'favicon.ico',
        '_debugbar*',
        'build/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bot & Crawler User Agent Signatures
    |--------------------------------------------------------------------------
    | Automated crawlers to exclude from human public traffic metrics.
    */
    'bot_signatures' => [
        'bot',
        'crawl',
        'spider',
        'slurp',
        'googlebot',
        'bingbot',
        'yandex',
        'baiduspider',
        'duckduckbot',
        'facebookexternalhit',
        'twitterbot',
        'rogerbot',
        'linkedinbot',
        'embedly',
        'quora link preview',
        'showyoubot',
        'outbrain',
        'pinterest',
        'curl',
        'wget',
        'python-requests',
        'postmanruntime',
    ],
];
