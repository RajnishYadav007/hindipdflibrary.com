<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Book routes & singular alias
Route::get('/books/{slug}', [BookController::class, 'show'])->name('books.show');
Route::get('/book/{slug}', fn (string $slug) => redirect()->route('books.show', ['slug' => $slug], 301));

// Author routes & singular alias
Route::get('/authors/{slug}', [AuthorController::class, 'show'])->name('authors.show');
Route::get('/author/{slug}', fn (string $slug) => redirect()->route('authors.show', ['slug' => $slug], 301));

// Category routes & singular alias
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/category/{slug}', fn (string $slug) => redirect()->route('categories.show', ['slug' => $slug], 301));

// Search route
Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
