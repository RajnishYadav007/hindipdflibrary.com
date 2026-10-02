<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Audiobook;
use App\Models\AudiobookChapter;
use App\Models\Book;
use App\Models\Category;
use App\Models\DevotionalContent;
use App\Models\Language;
use App\Models\StorySeries;
use App\Support\Media\AudioUploadValidator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserSubmissionController extends Controller
{
    /**
     * Display a listing of all submissions belonging to the current user.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $audiobooks = Audiobook::forUser($userId)
            ->with(['translations', 'language'])
            ->latest()
            ->get();

        $devotionals = DevotionalContent::forUser($userId)
            ->with(['translations', 'language'])
            ->latest()
            ->get();

        $stories = StorySeries::forUser($userId)
            ->with(['translations', 'language', 'episodes'])
            ->latest()
            ->get();

        return view('dashboard.submissions.index', compact('audiobooks', 'devotionals', 'stories'));
    }

    /**
     * Show form to submit an audiobook.
     */
    public function createAudiobook(): View
    {
        $languages = Language::all();
        $books = Book::with('translations')
            ->where('status', 'published')
            ->latest()
            ->take(100)
            ->get();

        return view('dashboard.submissions.audiobook-create', compact('languages', 'books'));
    }

    /**
     * Store a submitted audiobook (always pending_review).
     */
    public function storeAudiobook(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'book_id' => ['nullable', 'exists:books,id'],
            'language_id' => ['required', 'exists:languages,id'],
            'narrator' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'external_audio_url' => ['nullable', 'url', 'max:500'],
            'audio_file' => ['nullable', 'file'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5120'],
            'copyright_declared' => ['accepted'],
            'rights_holder' => ['nullable', 'string', 'max:255'],
            'permission_notes' => ['nullable', 'string', 'max:1000'],
            'add_chapters_next' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('audio_file')) {
            AudioUploadValidator::validate($request->file('audio_file'), 'audio_file');
        }

        $audiobook = DB::transaction(function () use ($request, $validated) {
            $slug = Str::slug($validated['title']).'-'.Str::random(5);

            $audiobook = Audiobook::create([
                'book_id' => $validated['book_id'] ?? null,
                'language_id' => $validated['language_id'],
                'slug' => $slug,
                'author_name' => $validated['author_name'] ?? null,
                'narrator' => $validated['narrator'] ?? null,
                'status' => 'pending_review',
                'submission_type' => 'user_submission',
                'submitted_by' => $request->user()->id,
                'external_audio_url' => $validated['external_audio_url'] ?? null,
                'copyright_status' => 'copyright_declared',
                'rights_holder' => $validated['rights_holder'] ?? null,
                'permission_notes' => $validated['permission_notes'] ?? null,
            ]);

            // Hindi translation
            $audiobook->translations()->create([
                'locale' => 'hi',
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
            ]);

            // English translation if provided
            if (! empty($validated['title_en'])) {
                $audiobook->translations()->create([
                    'locale' => 'en',
                    'title' => $validated['title_en'],
                    'description' => $validated['description'] ?? null,
                ]);
            }

            // Handle cover upload
            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $filename = 'audiobook_'.$audiobook->id.'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('covers/audiobooks', $filename, 'public');

                $audiobook->media()->create([
                    'uuid' => (string) Str::uuid(),
                    'collection_name' => 'cover',
                    'name' => 'Cover Image',
                    'file_name' => $path,
                    'mime_type' => $file->getMimeType(),
                    'disk' => 'public',
                    'size' => $file->getSize(),
                ]);
            }

            // Handle uploaded audio file
            if ($request->hasFile('audio_file')) {
                $file = $request->file('audio_file');
                $filename = 'audio_'.$audiobook->id.'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('audio/audiobooks', $filename, 'public');

                $audiobook->media()->create([
                    'uuid' => (string) Str::uuid(),
                    'collection_name' => 'audio',
                    'name' => 'Main Audiobook Audio',
                    'file_name' => $path,
                    'mime_type' => $file->getMimeType(),
                    'disk' => 'public',
                    'size' => $file->getSize(),
                ]);
            }

            return $audiobook;
        });

        if ($request->boolean('add_chapters_next')) {
            return redirect()->route('dashboard.submissions.audiobook.chapters', $audiobook)
                ->with('success', 'ऑडियोबुक का विवरण सहेज लिया गया है। अब आप अध्याय (Chapters) और ऑडियो ट्रैक जोड़ सकते हैं।');
        }

        return redirect()->route('dashboard.submissions.index')
            ->with('success', 'आपकी ऑडियोबुक समीक्षा (Review) हेतु सफलतापूर्वक जमा कर दी गई है। एडमिन द्वारा अनुमोदन के पश्चात यह प्रकाशित होगी।');
    }

    /**
     * Show form to submit Chalisa.
     */
    public function createChalisa(): View
    {
        $languages = Language::all();
        $type = 'chalisa';

        return view('dashboard.submissions.devotional-create', compact('languages', 'type'));
    }

    /**
     * Show form to submit Aarti.
     */
    public function createAarti(): View
    {
        $languages = Language::all();
        $type = 'aarti';

        return view('dashboard.submissions.devotional-create', compact('languages', 'type'));
    }

    /**
     * Store submitted Chalisa or Aarti.
     */
    public function storeDevotional(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['chalisa', 'aarti'])],
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'deity_name' => ['required', 'string', 'max:100'],
            'author_name' => ['nullable', 'string', 'max:150'],
            'language_id' => ['required', 'exists:languages,id'],
            'content' => ['required', 'string', 'max:20000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'copyright_declared' => ['accepted'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $slug = Str::slug($validated['title']).'-'.Str::random(5);

            $devotional = DevotionalContent::create([
                'type' => $validated['type'],
                'language_id' => $validated['language_id'],
                'slug' => $slug,
                'deity_name' => $validated['deity_name'],
                'author_name' => $validated['author_name'] ?? null,
                'status' => 'pending_review',
                'submitted_by' => $request->user()->id,
                'copyright_status' => 'public_domain_devotional',
            ]);

            $devotional->translations()->create([
                'locale' => 'hi',
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'content' => strip_tags($validated['content'], '<p><br><strong><em><h3><h4><ul><ol><li>'),
            ]);

            if (! empty($validated['title_en'])) {
                $devotional->translations()->create([
                    'locale' => 'en',
                    'title' => $validated['title_en'],
                    'description' => $validated['description'] ?? null,
                    'content' => strip_tags($validated['content'], '<p><br><strong><em><h3><h4><ul><ol><li>'),
                ]);
            }
        });

        $label = $validated['type'] === 'chalisa' ? 'चालीसा' : 'आरती';

        return redirect()->route('dashboard.submissions.index')
            ->with('success', "आपकी {$label} समीक्षा हेतु जमा कर दी गई है। अनुमोदन के उपरांत प्रकाशित की जाएगी।");
    }

    /**
     * Show form to submit Children's Story or General Story.
     */
    public function createStory(Request $request): View
    {
        $languages = Language::all();
        $categories = Category::active()->whereNull('parent_id')->with('translations')->get();
        $storyType = in_array($request->query('type'), ['children', 'general']) ? $request->query('type') : 'children';

        return view('dashboard.submissions.story-create', compact('languages', 'categories', 'storyType'));
    }

    /**
     * Store submitted story series and first episode.
     */
    public function storeStory(Request $request): RedirectResponse
    {
        $storyType = $request->input('story_type', 'children');

        $rules = [
            'story_type' => ['nullable', Rule::in(['children', 'general'])],
            'classification' => ['nullable', 'string', 'max:100'],
            'series_title' => ['required', 'string', 'max:255'],
            'series_title_en' => ['nullable', 'string', 'max:255'],
            'age_group' => $storyType === 'general'
                ? ['nullable', Rule::in(['3-5', '6-8', '9-12', 'all'])]
                : ['required', Rule::in(['3-5', '6-8', '9-12', 'all'])],
            'category_id' => ['nullable', 'exists:categories,id'],
            'language_id' => ['required', 'exists:languages,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'episode_title' => ['required', 'string', 'max:255'],
            'episode_content' => ['required', 'string', 'max:30000'],
            'narration_type' => ['required', Rule::in(['tts', 'uploaded', 'none'])],
            'audio_file' => ['nullable', 'file'],
            'copyright_declared' => ['accepted'],
        ];

        $validated = $request->validate($rules);
        $ageGroup = ($storyType === 'general' && empty($validated['age_group'])) ? 'all' : ($validated['age_group'] ?? 'all');

        if ($request->hasFile('audio_file')) {
            AudioUploadValidator::validate($request->file('audio_file'), 'audio_file');
        }

        DB::transaction(function () use ($request, $validated, $ageGroup, $storyType) {
            $seriesSlug = Str::slug($validated['series_title']).'-'.Str::random(5);

            $series = StorySeries::create([
                'category_id' => $validated['category_id'] ?? null,
                'language_id' => $validated['language_id'],
                'slug' => $seriesSlug,
                'story_type' => $storyType,
                'classification' => $validated['classification'] ?? ($storyType === 'children' ? "आयु वर्ग: {$ageGroup}" : 'सामान्य कथा'),
                'age_group' => $ageGroup,
                'status' => 'pending_review',
                'submitted_by' => $request->user()->id,
                'copyright_status' => 'copyright_declared',
            ]);

            $series->translations()->create([
                'locale' => 'hi',
                'title' => $validated['series_title'],
                'description' => $validated['description'] ?? null,
            ]);

            if (! empty($validated['series_title_en'])) {
                $series->translations()->create([
                    'locale' => 'en',
                    'title' => $validated['series_title_en'],
                    'description' => $validated['description'] ?? null,
                ]);
            }

            // Create initial episode
            $episodeSlug = Str::slug($validated['episode_title']).'-ep1';
            $episode = $series->episodes()->create([
                'episode_number' => 1,
                'narration_type' => $validated['narration_type'],
                'status' => 'pending_review',
            ]);

            $episode->translations()->create([
                'locale' => 'hi',
                'title' => $validated['episode_title'],
                'slug' => $episodeSlug,
                'content' => strip_tags($validated['episode_content'], '<p><br><strong><em><h3><h4><ul><ol><li>'),
            ]);

            if ($request->hasFile('audio_file')) {
                $file = $request->file('audio_file');
                $filename = 'story_ep_'.$episode->id.'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('audio/stories', $filename, 'public');

                $episode->media()->create([
                    'uuid' => (string) Str::uuid(),
                    'collection_name' => 'audio',
                    'name' => 'Episode Narration Audio',
                    'file_name' => $path,
                    'mime_type' => $file->getMimeType(),
                    'disk' => 'public',
                    'size' => $file->getSize(),
                ]);
            }
        });

        $successMsg = $storyType === 'general'
            ? 'आपकी कथा/कहानी श्रृंखला समीक्षा हेतु सफलतापूर्वक जमा कर दी गई है।'
            : 'आपकी बाल कथा (Story Series & Episode 1) समीक्षा हेतु सफलतापूर्वक जमा कर दी गई है।';

        return redirect()->route('dashboard', ['tab' => 'submissions'])
            ->with('success', $successMsg);
    }

    /**
     * Delete user's pending audiobook submission.
     */
    public function destroyAudiobook(Request $request, Audiobook $audiobook): RedirectResponse
    {
        if ($audiobook->submitted_by !== $request->user()->id && ! $request->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($audiobook->isPublished() && ! $request->user()->is_admin) {
            abort(403, 'प्रकाशित ऑडियोबुक को हटाया नहीं जा सकता।');
        }

        $audiobook->delete();

        return redirect()->route('dashboard', ['tab' => 'submissions'])
            ->with('success', 'ऑडियोबुक सबमिशन हटा दिया गया है।');
    }

    /**
     * Delete user's pending devotional (Chalisa/Aarti) submission.
     */
    public function destroyDevotional(Request $request, DevotionalContent $devotional): RedirectResponse
    {
        if ($devotional->submitted_by !== $request->user()->id && ! $request->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($devotional->isPublished() && ! $request->user()->is_admin) {
            abort(403, 'प्रकाशित सामग्री को हटाया नहीं जा सकता।');
        }

        $devotional->delete();

        return redirect()->route('dashboard', ['tab' => 'submissions'])
            ->with('success', 'चालीसा/आरती सबमिशन हटा दिया गया है।');
    }

    /**
     * Delete user's pending story submission.
     */
    public function destroyStory(Request $request, StorySeries $story): RedirectResponse
    {
        if ($story->submitted_by !== $request->user()->id && ! $request->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($story->isPublished() && ! $request->user()->is_admin) {
            abort(403, 'प्रकाशित कथा श्रृंखला को हटाया नहीं जा सकता।');
        }

        $story->delete();

        return redirect()->route('dashboard', ['tab' => 'submissions'])
            ->with('success', 'कथा श्रृंखला सबमिशन हटा दिया गया है।');
    }

    /**
     * Display the detailed status, timeline, metadata, and editorial feedback of a submission.
     */
    public function show(Request $request, string $type, int $id): View
    {
        $user = $request->user();

        switch ($type) {
            case 'book':
                $item = Book::with(['user', 'translations', 'authors.translations', 'categories.translations', 'genres.translations', 'media'])
                    ->findOrFail($id);
                if ($item->user_id !== $user->id && ! $user->is_admin) {
                    abort(403, 'Unauthorized access to this submission.');
                }
                $typeLabel = 'पुस्तक (Book PDF)';
                $typeIcon = 'book-open';
                $liveUrl = $item->isPublished() ? route('books.show', $item->slug) : null;
                $deleteRoute = route('dashboard.books.destroy', $item);
                break;

            case 'audiobook':
                $item = Audiobook::with(['submitter', 'translations', 'language', 'chapters.translations', 'chapters.media', 'media', 'book.translations'])
                    ->findOrFail($id);
                if ($item->submitted_by !== $user->id && ! $user->is_admin) {
                    abort(403, 'Unauthorized access to this submission.');
                }
                $typeLabel = 'ऑडियोबुक (Audiobook)';
                $typeIcon = 'headphones';
                $liveUrl = $item->isPublished() ? url('/audiobooks/'.$item->slug) : null;
                $deleteRoute = route('dashboard.submissions.audiobook.destroy', $item);
                break;

            case 'devotional':
                $item = DevotionalContent::with(['submitter', 'translations', 'language'])
                    ->findOrFail($id);
                if ($item->submitted_by !== $user->id && ! $user->is_admin) {
                    abort(403, 'Unauthorized access to this submission.');
                }
                $typeLabel = $item->type === 'chalisa' ? 'श्री चालीसा (Chalisa)' : 'पावन आरती (Aarti)';
                $typeIcon = $item->type === 'chalisa' ? 'scroll-text' : 'flame';
                $liveUrl = $item->isPublished() ? url('/'.$item->type.'/'.$item->slug) : null;
                $deleteRoute = route('dashboard.submissions.devotional.destroy', $item);
                break;

            case 'story':
                $item = StorySeries::with(['submitter', 'translations', 'language', 'episodes.translations'])
                    ->findOrFail($id);
                if ($item->submitted_by !== $user->id && ! $user->is_admin) {
                    abort(403, 'Unauthorized access to this submission.');
                }
                $typeLabel = $item->story_type === 'children' ? 'बाल कहानी (Children Story)' : 'कथा व लोककथा (Story)';
                $typeIcon = $item->story_type === 'children' ? 'sparkles' : 'library';
                $liveUrl = $item->isPublished() ? url('/stories/'.$item->slug) : null;
                $deleteRoute = route('dashboard.submissions.story.destroy', $item);
                break;

            default:
                abort(404);
        }

        $publicUrl = $liveUrl;

        $typeConfig = [
            'icon' => $typeIcon,
            'name' => $typeLabel,
        ];

        return view('dashboard.submissions.show', compact('item', 'type', 'typeLabel', 'typeIcon', 'typeConfig', 'liveUrl', 'publicUrl', 'deleteRoute'));
    }

    /**
     * Show audiobook chapter manager.
     */
    public function manageChapters(Request $request, Audiobook $audiobook): View
    {
        if ($audiobook->submitted_by !== $request->user()->id && ! $request->user()->is_admin) {
            abort(403, 'Unauthorized access to this audiobook.');
        }

        $audiobook->load(['translations', 'chapters.translations', 'chapters.media']);

        $nextChapterNumber = ($audiobook->chapters->max('chapter_number') ?? 0) + 1;

        return view('dashboard.submissions.audiobook-chapters', compact('audiobook', 'nextChapterNumber'));
    }

    /**
     * Store a new chapter/track in the audiobook.
     */
    public function storeChapter(Request $request, Audiobook $audiobook): RedirectResponse
    {
        if ($audiobook->submitted_by !== $request->user()->id && ! $request->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($audiobook->isPublished() && ! $request->user()->is_admin) {
            abort(403, 'प्रकाशित ऑडियोबुक में सीधे अध्याय नहीं जोड़े जा सकते।');
        }

        $validated = $request->validate([
            'chapter_number' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'external_audio_url' => ['nullable', 'url', 'max:500'],
            'audio_file' => ['nullable', 'file'],
            'duration_seconds' => ['nullable', 'integer', 'min:1', 'max:86400'],
        ]);

        if (empty($validated['external_audio_url']) && ! $request->hasFile('audio_file')) {
            return back()->withInput()->withErrors([
                'audio_file' => 'कृपया ऑडियो फ़ाइल अपलोड करें या बाह्य ऑडियो लिंक (External Audio URL) प्रदान करें।',
            ]);
        }

        if ($request->hasFile('audio_file')) {
            AudioUploadValidator::validate($request->file('audio_file'), 'audio_file');
        }

        DB::transaction(function () use ($request, $validated, $audiobook) {
            $chapter = $audiobook->chapters()->create([
                'chapter_number' => $validated['chapter_number'],
                'duration_seconds' => ! empty($validated['duration_seconds']) ? (int) $validated['duration_seconds'] : null,
                'external_audio_url' => $validated['external_audio_url'] ?? null,
                'status' => 'draft',
                'sort_order' => $validated['chapter_number'],
            ]);

            $chapter->translations()->create([
                'locale' => 'hi',
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
            ]);

            if (! empty($validated['title_en'])) {
                $chapter->translations()->create([
                    'locale' => 'en',
                    'title' => $validated['title_en'],
                    'description' => $validated['description'] ?? null,
                ]);
            }

            if ($request->hasFile('audio_file')) {
                $file = $request->file('audio_file');
                $filename = 'ab_'.$audiobook->id.'_ch_'.$chapter->id.'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('audio/chapters', $filename, 'public');

                $chapter->media()->create([
                    'uuid' => (string) Str::uuid(),
                    'collection_name' => 'audio',
                    'name' => 'Chapter Audio Track',
                    'file_name' => $path,
                    'mime_type' => $file->getMimeType(),
                    'disk' => 'public',
                    'size' => $file->getSize(),
                ]);
            }

            // Recalculate total duration if possible
            $totalSeconds = $audiobook->chapters()->sum('duration_seconds');
            if ($totalSeconds > 0) {
                $audiobook->update(['duration_seconds' => $totalSeconds]);
            }
        });

        return redirect()->route('dashboard.submissions.audiobook.chapters', $audiobook)
            ->with('success', "अध्याय {$validated['chapter_number']} सफलतापूर्वक जोड़ दिया गया है।");
    }

    /**
     * Delete a chapter from an audiobook.
     */
    public function destroyChapter(Request $request, Audiobook $audiobook, AudiobookChapter $chapter): RedirectResponse
    {
        if ($audiobook->submitted_by !== $request->user()->id && ! $request->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($chapter->audiobook_id !== $audiobook->id) {
            abort(403, 'Invalid chapter.');
        }

        if ($audiobook->isPublished() && ! $request->user()->is_admin) {
            abort(403, 'प्रकाशित ऑडियोबुक के अध्यायों को सीधे हटाया नहीं जा सकता।');
        }

        $chapterNumber = $chapter->chapter_number;
        $chapter->delete();

        // Recalculate total duration if possible
        $totalSeconds = $audiobook->chapters()->sum('duration_seconds');
        $audiobook->update(['duration_seconds' => $totalSeconds > 0 ? $totalSeconds : null]);

        return redirect()->route('dashboard.submissions.audiobook.chapters', $audiobook)
            ->with('success', "अध्याय {$chapterNumber} हटा दिया गया है।");
    }
}
