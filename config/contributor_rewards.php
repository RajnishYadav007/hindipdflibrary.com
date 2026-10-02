<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Contributor Library Points Weighting
    |--------------------------------------------------------------------------
    |
    | Points awarded to the contributor when an approved literature unit is
    | published. Strictly decoupled from traffic, pageviews, or AdSense.
    |
    */
    'points' => [
        'book' => 100,
        'audiobook' => 150,
        'story_children' => 75,
        'story_general' => 75,
        'chalisa' => 50,
        'aarti' => 50,
        'editorial_correction' => 25,
    ],

    /*
    |--------------------------------------------------------------------------
    | Contributor Levels & Thresholds
    |--------------------------------------------------------------------------
    |
    | Levels based strictly on the count of approved, published contributions.
    |
    */
    'levels' => [
        'reader' => [
            'key' => 'reader',
            'min_approved' => 0,
            'label' => 'पाठक (Reader)',
            'short_label' => 'पाठक',
            'icon' => 'book-open',
            'color' => 'slate',
        ],
        'contributor' => [
            'key' => 'contributor',
            'min_approved' => 1,
            'label' => 'साहित्य प्रस्तोता (Contributor)',
            'short_label' => 'प्रस्तोता',
            'icon' => 'sparkles',
            'color' => 'amber',
        ],
        'regular_contributor' => [
            'key' => 'regular_contributor',
            'min_approved' => 5,
            'label' => 'सक्रिय योगदानकर्ता (Regular Contributor)',
            'short_label' => 'सक्रिय योगदानकर्ता',
            'icon' => 'check-circle',
            'color' => 'emerald',
        ],
        'library_contributor' => [
            'key' => 'library_contributor',
            'min_approved' => 15,
            'label' => 'पुस्तकालय सहयोगी (Library Contributor)',
            'short_label' => 'पुस्तकालय सहयोगी',
            'icon' => 'library',
            'color' => 'cyan',
        ],
        'senior_contributor' => [
            'key' => 'senior_contributor',
            'min_approved' => 30,
            'label' => 'वरिष्ठ योगदानकर्ता (Senior Contributor)',
            'short_label' => 'वरिष्ठ योगदानकर्ता',
            'icon' => 'shield',
            'color' => 'violet',
        ],
        'distinguished_contributor' => [
            'key' => 'distinguished_contributor',
            'min_approved' => 50,
            'label' => 'विशिष्ट योगदानकर्ता (Distinguished Contributor)',
            'short_label' => 'विशिष्ट योगदानकर्ता',
            'icon' => 'sparkles',
            'color' => 'rose',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Contribution Milestones
    |--------------------------------------------------------------------------
    |
    | Stepped targets for contributor recognition and progress visualization.
    |
    */
    'milestones' => [1, 5, 10, 15, 25, 30, 50, 100],

    /*
    |--------------------------------------------------------------------------
    | Referral Program Configuration
    |--------------------------------------------------------------------------
    |
    | Configurable referral incentives. Points are strictly awarded ONLY when
    | a referred user's first contributed literature is approved & published.
    | Zero points for signups or page visits.
    |
    */
    'referrals' => [
        'enabled' => env('CONTRIBUTOR_REFERRALS_ENABLED', true),
        'points' => (int) env('CONTRIBUTOR_REFERRAL_POINTS', 100),
        'attribution_window_days' => 30,
    ],
];
