<?php

namespace App\Filament\Widgets;

use App\Enums\BookStatus;
use App\Enums\ReviewStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalBooks = Book::count();
        $publishedBooks = Book::where('status', BookStatus::Published)->count();
        $draftBooks = Book::where('status', BookStatus::Draft)->count();
        $totalAuthors = Author::count();
        $totalCategories = Category::count();
        $pendingReviews = Review::where('status', ReviewStatus::Pending)->count();
        $totalUsers = User::count();

        return [
            Stat::make('Total Books', $totalBooks)
                ->description("Published: {$publishedBooks} | Draft: {$draftBooks}")
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            Stat::make('Total Authors', $totalAuthors)
                ->description('Active literary contributors')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Categories', $totalCategories)
                ->description('Taxonomy sections')
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            Stat::make('Pending Reviews', $pendingReviews)
                ->description('Awaiting moderation')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color($pendingReviews > 0 ? 'warning' : 'success'),

            Stat::make('Registered Users', $totalUsers)
                ->description('Library community members')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}
