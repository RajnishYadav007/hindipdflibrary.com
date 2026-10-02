<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Contributor\ContributorRewardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the authenticated user's unified reader and contributor dashboard.
     */
    public function index(Request $request, ContributorRewardService $rewardService): View
    {
        $user = $request->user();
        $userId = $user->id;

        // Ensure user's cached points & level are up-to-date
        $rewardService->reconcileUserPoints($user);
        $contributorStats = $rewardService->getContributorStats($user);
        $pointsLedger = $user->pointsLedger()->latest('id')->take(20)->get();

        // 1. Database-backed Aggregate KPI Counts (Optimized SQL count queries, zero N+1)
        $booksCount = $user->submittedBooks()->count();
        $audiobooksCount = $user->submittedAudiobooks()->count();
        $devotionalsCount = $user->submittedDevotionals()->count();
        $storiesCount = $user->submittedStories()->count();
        $totalContributions = $booksCount + $audiobooksCount + $devotionalsCount + $storiesCount;

        $booksPending = $user->submittedBooks()->whereIn('status', ['pending_review', 'under_review', 'draft'])->count();
        $audiobooksPending = $user->submittedAudiobooks()->whereIn('status', ['pending_review', 'under_review'])->count();
        $devotionalsPending = $user->submittedDevotionals()->whereIn('status', ['pending_review', 'under_review'])->count();
        $storiesPending = $user->submittedStories()->whereIn('status', ['pending_review', 'under_review'])->count();
        $pendingCount = $booksPending + $audiobooksPending + $devotionalsPending + $storiesPending;

        $booksPublished = $user->submittedBooks()->where('status', 'published')->count();
        $audiobooksPublished = $user->submittedAudiobooks()->where('status', 'published')->count();
        $devotionalsPublished = $user->submittedDevotionals()->where('status', 'published')->count();
        $storiesPublished = $user->submittedStories()->where('status', 'published')->count();
        $publishedCount = $booksPublished + $audiobooksPublished + $devotionalsPublished + $storiesPublished;

        $favoritesCount = $user->favoriteBooks()->count();
        $reviewsCount = $user->reviews()->count();

        $stats = [
            'total_contributions' => $totalContributions,
            'pending_count' => $pendingCount,
            'published_count' => $publishedCount,
            'favorites_count' => $favoritesCount,
            'reviews_count' => $reviewsCount,
            'books_count' => $booksCount,
            'audiobooks_count' => $audiobooksCount,
            'devotionals_count' => $devotionalsCount,
            'stories_count' => $storiesCount,
        ];

        // 2. Scoped Submissions (Eager-loaded relationships only where justified)
        $submittedBooks = $user->submittedBooks()
            ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
            ->latest('id')
            ->take(20)
            ->get();

        $submittedAudiobooks = $user->submittedAudiobooks()
            ->with(['translations', 'language'])
            ->latest('id')
            ->take(20)
            ->get();

        $submittedDevotionals = $user->submittedDevotionals()
            ->with(['translations', 'language'])
            ->latest('id')
            ->take(20)
            ->get();

        $submittedStories = $user->submittedStories()
            ->with(['translations', 'language', 'episodes'])
            ->latest('id')
            ->take(20)
            ->get();

        // 3. User's favorited books (latest 12 for dashboard grid)
        $favoriteBooks = $user->favoriteBooks()
            ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
            ->latest('favorites.created_at')
            ->take(12)
            ->get();

        // 4. User's reviews and ratings (latest 12)
        $reviews = $user->reviews()
            ->with(['book.translations', 'book.authors.translations'])
            ->latest('id')
            ->take(12)
            ->get();

        $activeTab = $request->query('tab', 'overview');

        return view('dashboard', compact(
            'user',
            'submittedBooks',
            'submittedAudiobooks',
            'submittedDevotionals',
            'submittedStories',
            'favoriteBooks',
            'reviews',
            'stats',
            'contributorStats',
            'pointsLedger',
            'activeTab'
        ));
    }
}
