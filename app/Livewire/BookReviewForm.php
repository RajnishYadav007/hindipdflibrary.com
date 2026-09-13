<?php

namespace App\Livewire;

use App\Actions\Reviews\SubmitReviewAction;
use App\Enums\ReviewStatus;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookReviewForm extends Component
{
    public Book $book;

    public int $rating = 5;

    public string $title = '';

    public string $body = '';

    public bool $submitted = false;

    public ?string $existingStatus = null;

    public bool $isEditing = false;

    protected function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:150'],
            'body' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function mount(Book $book): void
    {
        $this->book = $book;

        if (Auth::check()) {
            $existing = Review::where('user_id', Auth::id())
                ->where('book_id', $this->book->id)
                ->first();

            if ($existing) {
                $this->rating = (int) $existing->rating;
                $this->title = (string) ($existing->title ?? '');
                $this->body = (string) ($existing->body ?? '');
                $this->existingStatus = $existing->status instanceof ReviewStatus
                    ? $existing->status->value
                    : (string) $existing->status;
            }
        }
    }

    public function setRating(int $stars): void
    {
        $this->rating = max(1, min(5, $stars));
    }

    public function toggleEdit(): void
    {
        $this->isEditing = ! $this->isEditing;
    }

    public function submit(SubmitReviewAction $submitReviewAction): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'));

            return;
        }

        $this->validate();

        $review = $submitReviewAction->handle(
            user: Auth::user(),
            book: $this->book,
            rating: $this->rating,
            title: $this->title ?: null,
            body: $this->body ?: null,
            ip: request()->ip(),
            userAgent: request()->userAgent()
        );

        $this->existingStatus = $review->status instanceof ReviewStatus
            ? $review->status->value
            : (string) $review->status;

        $this->submitted = true;
        $this->isEditing = false;

        session()->flash('review_success', 'Thank you! Your rating and review have been submitted and are pending moderation.');
    }

    public function render(): View
    {
        return view('livewire.book-review-form');
    }
}
