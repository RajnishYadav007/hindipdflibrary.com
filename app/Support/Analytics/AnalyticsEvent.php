<?php

namespace App\Support\Analytics;

class AnalyticsEvent
{
    public const PAGE_VIEW = 'page_view';

    public const BOOK_VIEW = 'book_view';

    public const BOOK_READ = 'book_read';

    public const BOOK_DOWNLOAD = 'book_download';

    public const BOOK_FAVORITE = 'book_favorite';

    public const BOOK_UNFAVORITE = 'book_unfavorite';

    public const RATING_SUBMITTED = 'rating_submitted';

    public const REVIEW_SUBMITTED = 'review_submitted';

    public const SEARCH = 'search';

    public const SEARCH_RESULT_CLICK = 'search_result_click';

    public const AUTHOR_VIEW = 'author_view';

    public const CATEGORY_VIEW = 'category_view';

    public const LANGUAGE_SWITCH = 'language_switch';

    public const AUDIOBOOK_PLAY = 'audiobook_play';

    public const AUDIOBOOK_CHAPTER_PLAY = 'chapter_play';

    public const AUDIOBOOK_COMPLETED = 'audiobook_completed';

    public const CHALISA_LISTEN = 'chalisa_listen';

    public const AARTI_LISTEN = 'aarti_listen';

    public const STORY_LISTEN = 'story_listen';

    public const TTS_USAGE = 'tts_usage';

    public const STORY_EPISODE_VIEW = 'story_episode_view';

    /**
     * Complete list of allowed event names.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::PAGE_VIEW,
            self::BOOK_VIEW,
            self::BOOK_READ,
            self::BOOK_DOWNLOAD,
            self::BOOK_FAVORITE,
            self::BOOK_UNFAVORITE,
            self::RATING_SUBMITTED,
            self::REVIEW_SUBMITTED,
            self::SEARCH,
            self::SEARCH_RESULT_CLICK,
            self::AUTHOR_VIEW,
            self::CATEGORY_VIEW,
            self::LANGUAGE_SWITCH,
            self::AUDIOBOOK_PLAY,
            self::AUDIOBOOK_CHAPTER_PLAY,
            self::AUDIOBOOK_COMPLETED,
            self::CHALISA_LISTEN,
            self::AARTI_LISTEN,
            self::STORY_LISTEN,
            self::TTS_USAGE,
            self::STORY_EPISODE_VIEW,
        ];
    }

    /**
     * Determine if an event name is valid.
     */
    public static function isValid(string $eventName): bool
    {
        return in_array($eventName, self::all(), true);
    }
}
