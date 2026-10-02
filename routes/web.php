<?php

use App\Http\Controllers\AudiobookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DevotionalContentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Media\SecureAudioStreamController;
use App\Http\Controllers\Media\TextToSpeechController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\FavoriteController;
use App\Http\Controllers\User\UserBookController;
use App\Http\Controllers\User\UserSubmissionController;
use App\Http\Controllers\VedPuranController;
use Illuminate\Support\Facades\Storage;

// Storage asset fallback (guarantees images and files load reliably even if symlink is missing on Windows or Shared Hosting)
Route::get('/storage/{path}', function (string $path) {
    if (Storage::disk('public')->exists($path)) {
        return Storage::disk('public')->response($path);
    }
    abort(404);
})->where('path', '.*')->name('storage.fallback');

// XML Sitemap & Robots.txt
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// Search click tracking route (302 redirects to book)
Route::get('/search/click', [SearchController::class, 'click'])->name('search.click');
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

// Localized Public Content Routes (/{locale}/...)
Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}'])
    ->middleware('set.locale')
    ->group(function () {
        // Localized Homepage
        Route::get('/', HomeController::class)->name('home');

        // Catalog Indexes
        Route::get('/books', [BookController::class, 'index'])->name('books.index');
        Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/genres', [CategoryController::class, 'genres'])->name('genres.index');
        Route::get('/ved-puran', [VedPuranController::class, 'index'])->name('ved-puran');

        // Audiobooks System
        Route::get('/audiobooks', [AudiobookController::class, 'index'])->name('audiobooks.index');
        Route::get('/audiobooks/{slug}', [AudiobookController::class, 'show'])->name('audiobooks.show');

        // Chalisa & Aarti (Devotional System)
        Route::get('/chalisa', [DevotionalContentController::class, 'chalisas'])->name('chalisa.index');
        Route::get('/aarti', [DevotionalContentController::class, 'aartis'])->name('aarti.index');
        Route::get('/chalisa/{slug}', [DevotionalContentController::class, 'showChalisa'])->name('chalisa.show');
        Route::get('/aarti/{slug}', [DevotionalContentController::class, 'showAarti'])->name('aarti.show');

        // Children's Stories System
        Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
        Route::get('/stories/{series_slug}', [StoryController::class, 'series'])->name('stories.series');
        Route::get('/stories/{series_slug}/{episode_slug}', [StoryController::class, 'episode'])->name('stories.episode');

        // Localized Book Actions
        Route::get('/book/{slug}/read', [BookController::class, 'read'])->name('books.read');
        Route::get('/book/{slug}/reader', [BookController::class, 'interactiveReader'])->name('books.interactive_reader');
        Route::get('/books/{slug}/reader', [BookController::class, 'interactiveReader'])->name('books.plural.interactive_reader');
        Route::get('/book/{slug}/manifest', [BookController::class, 'archiveManifest'])->name('books.archive_manifest');
        Route::get('/books/{slug}/manifest', [BookController::class, 'archiveManifest'])->name('books.plural.archive_manifest');
        Route::get('/book/{slug}/download', [BookController::class, 'download'])
            ->middleware('throttle:downloads')
            ->name('books.download');

        // Localized Book Detail (canonical singular & plural alias)
        Route::get('/book/{slug}', [BookController::class, 'show'])->name('books.show');
        Route::get('/hanuman-chalisa', [BookController::class, 'showHanumanChalisa'])->name('hanuman-chalisa');
        Route::get('/hanuman-aarti', fn (string $locale) => redirect("/{$locale}/book/hanuman-aarti", 301))->name('hanuman-aarti');
        Route::get('/bajrang-baan', fn (string $locale) => redirect("/{$locale}/book/bajrang-baan", 301))->name('bajrang-baan');

        // Localized Author Detail (canonical singular & plural alias)
        Route::get('/author/{slug}', [AuthorController::class, 'show'])->name('authors.show');
        Route::get('/authors/{slug}', [AuthorController::class, 'show'])->name('authors.plural.show');

        // Localized Category Detail (canonical singular & plural alias)
        Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.plural.show');

        // Localized Search & Discovery
        Route::get('/search', [SearchController::class, 'index'])
            ->middleware('throttle:search')
            ->name('search');

        // Static & Editorial Pages
        Route::get('/about', [PageController::class, 'about'])->name('pages.about');
        Route::get('/help', [PageController::class, 'help'])->name('pages.help');
        Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
        Route::post('/contact', [PageController::class, 'submitContact'])->name('pages.contact.submit');
        Route::get('/suggest-book', [PageController::class, 'suggestBook'])->name('pages.suggest-book');
        Route::post('/suggest-book', [PageController::class, 'submitSuggestBook'])->name('pages.suggest-book.submit');
        Route::get('/privacy', [PageController::class, 'privacy'])->name('pages.privacy');
        Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');
        Route::get('/copyright', [PageController::class, 'copyright'])->name('pages.copyright');
        Route::get('/public-domain', [PageController::class, 'publicDomain'])->name('pages.public-domain');
    });

// Unprefixed Routes (Backwards compatibility and direct URLs)
Route::get('/', HomeController::class)->name('home.root');

Route::get('/books', [BookController::class, 'index']);
Route::get('/authors', [AuthorController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/genres', [CategoryController::class, 'genres']);
Route::get('/ved-puran', [VedPuranController::class, 'index'])->name('ved-puran.root');

Route::get('/books/{slug}/read', [BookController::class, 'read']);
Route::get('/book/{slug}/read', [BookController::class, 'read']);
Route::get('/books/{slug}/reader', [BookController::class, 'interactiveReader']);
Route::get('/book/{slug}/reader', [BookController::class, 'interactiveReader']);
Route::get('/books/{slug}/manifest', [BookController::class, 'archiveManifest']);
Route::get('/book/{slug}/manifest', [BookController::class, 'archiveManifest']);
Route::get('/books/{slug}/download', [BookController::class, 'download'])->middleware('throttle:downloads');
Route::get('/books/{slug}', [BookController::class, 'show']);
Route::get('/book/{slug}', fn (string $slug) => redirect("/books/{$slug}", 301));
Route::get('/hanuman-chalisa', [BookController::class, 'showHanumanChalisa'])->name('hanuman-chalisa.root');
Route::get('/hanuman-aarti', fn () => redirect('/hi/book/hanuman-aarti', 301))->name('hanuman-aarti.root');
Route::get('/bajrang-baan', fn () => redirect('/hi/book/bajrang-baan', 301))->name('bajrang-baan.root');

Route::get('/authors/{slug}', [AuthorController::class, 'show']);
Route::get('/author/{slug}', fn (string $slug) => redirect("/authors/{$slug}", 301));

Route::get('/categories/{slug}', [CategoryController::class, 'show']);
Route::get('/category/{slug}', fn (string $slug) => redirect("/categories/{$slug}", 301));

Route::get('/search', [SearchController::class, 'index'])
    ->middleware('throttle:search')
    ->name('search.root');

// Static Pages Unprefixed Aliases
Route::get('/about', [PageController::class, 'about']);
Route::get('/help', [PageController::class, 'help']);
Route::get('/contact', [PageController::class, 'contact']);
Route::post('/contact', [PageController::class, 'submitContact']);
Route::get('/suggest-book', [PageController::class, 'suggestBook']);
Route::post('/suggest-book', [PageController::class, 'submitSuggestBook']);
Route::get('/privacy', [PageController::class, 'privacy']);
Route::get('/terms', [PageController::class, 'terms']);
Route::get('/copyright', [PageController::class, 'copyright']);
Route::get('/public-domain', [PageController::class, 'publicDomain']);

// Secure Audio Streaming Endpoint (Range requests & authorization enabled)
Route::get('/audio/stream/{uuid}', [SecureAudioStreamController::class, 'stream'])->name('audio.stream');

// Server-side Hindi Text-To-Speech Stream
Route::get('/api/tts/stream', [TextToSpeechController::class, 'stream'])->name('tts.stream');

Route::get('/audiobooks', [AudiobookController::class, 'index']);
Route::get('/audiobooks/{slug}', [AudiobookController::class, 'show']);
Route::get('/chalisa', [DevotionalContentController::class, 'chalisas']);
Route::get('/aarti', [DevotionalContentController::class, 'aartis']);
Route::get('/chalisa/{slug}', [DevotionalContentController::class, 'showChalisa']);
Route::get('/aarti/{slug}', [DevotionalContentController::class, 'showAarti']);
Route::get('/stories', [StoryController::class, 'index']);
Route::get('/stories/{series_slug}', [StoryController::class, 'series']);
Route::get('/stories/{series_slug}/{episode_slug}', [StoryController::class, 'episode']);

// Authenticated User Portal & Dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Book Submissions ("अपनी बुक्स ऐड करें")
    Route::get('/dashboard/books/create', [UserBookController::class, 'create'])->name('dashboard.books.create');
    Route::post('/dashboard/books', [UserBookController::class, 'store'])->name('dashboard.books.store');
    Route::delete('/dashboard/books/{book}', [UserBookController::class, 'destroy'])->name('dashboard.books.destroy');

    // User Submissions (Audiobooks, Chalisas, Aartis, Stories)
    Route::get('/dashboard/submissions', [UserSubmissionController::class, 'index'])->name('dashboard.submissions.index');

    // Audiobook submissions & chapters
    Route::get('/dashboard/submissions/audiobook/create', [UserSubmissionController::class, 'createAudiobook'])->name('dashboard.submissions.audiobook.create');
    Route::post('/dashboard/submissions/audiobook', [UserSubmissionController::class, 'storeAudiobook'])->name('dashboard.submissions.audiobook.store');
    Route::delete('/dashboard/submissions/audiobook/{audiobook}', [UserSubmissionController::class, 'destroyAudiobook'])->name('dashboard.submissions.audiobook.destroy');
    Route::get('/dashboard/submissions/audiobook/{audiobook}/chapters', [UserSubmissionController::class, 'manageChapters'])->name('dashboard.submissions.audiobook.chapters');
    Route::post('/dashboard/submissions/audiobook/{audiobook}/chapters', [UserSubmissionController::class, 'storeChapter'])->name('dashboard.submissions.audiobook.chapters.store');
    Route::delete('/dashboard/submissions/audiobook/{audiobook}/chapters/{chapter}', [UserSubmissionController::class, 'destroyChapter'])->name('dashboard.submissions.audiobook.chapters.destroy');

    // Devotional submissions (Chalisa & Aarti)
    Route::get('/dashboard/submissions/chalisa/create', [UserSubmissionController::class, 'createChalisa'])->name('dashboard.submissions.chalisa.create');
    Route::get('/dashboard/submissions/aarti/create', [UserSubmissionController::class, 'createAarti'])->name('dashboard.submissions.aarti.create');
    Route::post('/dashboard/submissions/devotional', [UserSubmissionController::class, 'storeDevotional'])->name('dashboard.submissions.devotional.store');
    Route::delete('/dashboard/submissions/devotional/{devotional}', [UserSubmissionController::class, 'destroyDevotional'])->name('dashboard.submissions.devotional.destroy');

    // Story submissions (Children & General)
    Route::get('/dashboard/submissions/story/create', [UserSubmissionController::class, 'createStory'])->name('dashboard.submissions.story.create');
    Route::post('/dashboard/submissions/story', [UserSubmissionController::class, 'storeStory'])->name('dashboard.submissions.story.store');
    Route::delete('/dashboard/submissions/story/{story}', [UserSubmissionController::class, 'destroyStory'])->name('dashboard.submissions.story.destroy');

    // Submission Detail View (must be defined AFTER specific create routes)
    Route::get('/dashboard/submissions/{type}/{id}', [UserSubmissionController::class, 'show'])
        ->whereNumber('id')
        ->whereIn('type', ['book', 'audiobook', 'devotional', 'story'])
        ->name('dashboard.submissions.show');

    // User Favorites management
    Route::post('/favorites/{book}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::delete('/dashboard/favorites/{book}', [FavoriteController::class, 'destroy'])->name('dashboard.favorites.destroy');

    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
