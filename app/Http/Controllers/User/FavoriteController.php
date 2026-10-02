<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Favorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Toggle a book's favorited status for the authenticated user.
     */
    public function toggle(Request $request, Book $book): RedirectResponse
    {
        $user = $request->user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        $isHindi = app()->getLocale() === 'hi';

        if ($favorite) {
            $favorite->delete();
            $message = $isHindi ? 'पुस्तक पसंदीदा सूची से हटा दी गई है।' : 'Book removed from your saved list.';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
            ]);
            $message = $isHindi ? 'पुस्तक पसंदीदा सूची में जोड़ दी गई है।' : 'Book added to your saved list!';
        }

        return back()->with('status', $message);
    }

    /**
     * Remove a book from favorites.
     */
    public function destroy(Request $request, Book $book): RedirectResponse
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('book_id', $book->id)
            ->delete();

        $isHindi = app()->getLocale() === 'hi';
        $message = $isHindi ? 'पुस्तक पसंदीदा सूची से हटा दी गई है।' : 'Book removed from favorites.';

        return redirect()->route('dashboard', ['tab' => 'favorites'])->with('status', $message);
    }
}
