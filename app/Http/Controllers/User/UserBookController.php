<?php

namespace App\Http\Controllers\User;

use App\Actions\Books\SubmitUserBookAction;
use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserBookController extends Controller
{
    /**
     * Show the form for submitting a new book.
     */
    public function create(): View
    {
        $authors = Author::active()
            ->with('translations')
            ->get()
            ->sortBy(fn ($a) => $a->translations->first()?->name ?? $a->slug);

        $categories = Category::active()
            ->whereNull('parent_id')
            ->with('translations')
            ->get()
            ->sortBy(fn ($c) => $c->translations->first()?->name ?? $c->slug);

        $languages = Language::all();

        return view('dashboard.books.create', compact('authors', 'categories', 'languages'));
    }

    /**
     * Store a newly submitted book in storage.
     */
    public function store(Request $request, SubmitUserBookAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'title_en' => ['nullable', 'string', 'max:200'],
            'author_id' => ['nullable', 'exists:authors,id'],
            'new_author_name' => ['nullable', 'string', 'max:150'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'original_language_id' => ['nullable', 'exists:languages,id'],
            'publication_year' => ['nullable', 'integer', 'min:1800', 'max:'.(date('Y') + 1)],
            'pages' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'publisher' => ['nullable', 'string', 'max:150'],
            'isbn' => ['nullable', 'string', 'max:20'],
            'edition' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:5000'],
            'source_type' => ['nullable', 'string', 'in:pdf,external'],
            'source_url' => ['nullable', 'url', 'max:500', 'required_if:source_type,external'],
            'copyright_status' => ['nullable', 'string'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:51200', 'required_if:source_type,pdf'],
            'cover_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5120'],
        ]);

        if (empty($validated['author_id']) && empty($validated['new_author_name'])) {
            return back()->withInput()->withErrors([
                'author_id' => 'कृपया लेखक का चयन करें या नया लेखक नाम दर्ज करें। (Please select or enter an author name.)',
            ]);
        }

        $book = $action->handle(
            $request->user(),
            $validated,
            $request->file('pdf_file'),
            $request->file('cover_image')
        );

        $isHindi = app()->getLocale() === 'hi';
        $message = $isHindi
            ? 'धन्यवाद! आपकी पुस्तक "'.$book->title.'" सफलतापूर्वक सबमिट कर दी गई है। संपादन समीक्षा के उपरांत इसे पुस्तकालय में प्रकाशित किया जाएगा।'
            : 'Thank you! Your book "'.$book->title.'" has been submitted for editorial review and cataloging.';

        return redirect()->route('dashboard', ['tab' => 'books'])->with('status', $message);
    }

    /**
     * Delete a pending draft book submitted by the user.
     */
    public function destroy(Request $request, Book $book): RedirectResponse
    {
        if ($book->user_id !== $request->user()->id && ! $request->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($book->isPublished() && ! $request->user()->is_admin) {
            abort(403, 'प्रकाशित पुस्तक को हटाया नहीं जा सकता।');
        }

        $book->delete();

        $isHindi = app()->getLocale() === 'hi';
        $message = $isHindi
            ? 'पुस्तक सबमिशन हटा दिया गया है।'
            : 'Book submission has been removed.';

        return redirect()->route('dashboard', ['tab' => 'submissions'])->with('status', $message);
    }
}
