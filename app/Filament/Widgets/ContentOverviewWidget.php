<?php

namespace App\Filament\Widgets;

use App\Models\Audiobook;
use App\Models\Book;
use App\Models\DevotionalContent;
use App\Models\StoryEpisode;
use App\Models\StorySeries;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $booksCount = Book::count();
        $audiobooksCount = Audiobook::count();
        $chalisaCount = DevotionalContent::where('type', 'chalisa')->count();
        $aartiCount = DevotionalContent::where('type', 'aarti')->count();
        $seriesCount = StorySeries::count();
        $episodesCount = StoryEpisode::count();

        $pendingAudiobooks = Audiobook::where('status', 'pending_review')->count();
        $pendingDevotionals = DevotionalContent::where('status', 'pending_review')->count();
        $pendingStories = StorySeries::where('status', 'pending_review')->count() +
                          StoryEpisode::where('status', 'pending_review')->count();

        $totalPendingSubmissions = $pendingAudiobooks + $pendingDevotionals + $pendingStories;

        return [
            Stat::make('Books (पुस्तकें)', $booksCount)
                ->description('PDF Library Collection')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            Stat::make('Audiobooks (ऑडियोबुक्स)', $audiobooksCount)
                ->description("Pending: {$pendingAudiobooks}")
                ->descriptionIcon('heroicon-m-speaker-wave')
                ->color('amber'),

            Stat::make('Chalisa (चालीसा)', $chalisaCount)
                ->description('Spiritual stanzas')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),

            Stat::make('Aarti (आरती)', $aartiCount)
                ->description('Sacred verses')
                ->descriptionIcon('heroicon-m-sun')
                ->color('orange'),

            Stat::make('Story Series (बाल कथाएं)', $seriesCount)
                ->description("{$episodesCount} Total Episodes")
                ->descriptionIcon('heroicon-m-bookmark-square')
                ->color('success'),

            Stat::make('Pending Submissions', $totalPendingSubmissions)
                ->description($totalPendingSubmissions > 0 ? 'Action required by moderator' : 'All submissions reviewed')
                ->descriptionIcon('heroicon-m-clock')
                ->color($totalPendingSubmissions > 0 ? 'danger' : 'success'),
        ];
    }
}
