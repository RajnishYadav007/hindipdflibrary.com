<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// XML Sitemap & Robots.txt
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// Localized Public Content Routes (/{locale}/...)
Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}'])
    ->middleware('set.locale')
    ->group(function () {
        // Localized Homepage
        Route::get('/', HomeController::class)->name('home');

        // Localized Book Detail (canonical singular & plural alias)
        Route::get('/book/{slug}', [BookController::class, 'show'])->name('books.show');
        Route::get('/books/{slug}', [BookController::class, 'show'])->name('books.plural.show');

        // Localized Author Detail (canonical singular & plural alias)
        Route::get('/author/{slug}', [AuthorController::class, 'show'])->name('authors.show');
        Route::get('/authors/{slug}', [AuthorController::class, 'show'])->name('authors.plural.show');

        // Localized Category Detail (canonical singular & plural alias)
        Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.plural.show');

        // Localized Search & Discovery
        Route::get('/search', [SearchController::class, 'index'])->name('search');
    });

// Unprefixed Routes (Phase 6 backwards-compatibility)
Route::get('/', HomeController::class)->name('home.root');

Route::get('/books/{slug}', [BookController::class, 'show']);
Route::get('/book/{slug}', fn (string $slug) => redirect("/books/{$slug}", 301));

Route::get('/authors/{slug}', [AuthorController::class, 'show']);
Route::get('/author/{slug}', fn (string $slug) => redirect("/authors/{$slug}", 301));

Route::get('/categories/{slug}', [CategoryController::class, 'show']);
Route::get('/category/{slug}', fn (string $slug) => redirect("/categories/{$slug}", 301));

Route::get('/search', [SearchController::class, 'index'])->name('search.root');

// User Profile & Dashboard
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
