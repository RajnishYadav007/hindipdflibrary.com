<?php

namespace App\Http\Controllers;

use App\Models\DevotionalContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DevotionalContentController extends Controller
{
    /**
     * Display a listing of published Chalisas.
     */
    public function chalisas(Request $request): View
    {
        $query = DevotionalContent::published()
            ->chalisas()
            ->with(['translations', 'language', 'media']);

        if ($request->filled('deity')) {
            $query->forDeity($request->input('deity'));
        }

        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('deity_name', 'like', "%{$term}%")
                    ->orWhere('author_name', 'like', "%{$term}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', "%{$term}%"));
            });
        }

        $items = $query->orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $type = 'chalisa';
        $pageTitle = 'सम्पूर्ण चालीसा संग्रह (Shree Chalisa Sangrah)';
        $pageDescription = 'सभी प्रमुख देवी-देवताओं की पावन चालीसा पाठ, हिन्दी अर्थ सहित एवं शून्य-लागत ब्राउज़र ऑडियो श्रवण सुविधा।';

        return view('public.devotionals.index', compact('items', 'type', 'pageTitle', 'pageDescription'));
    }

    /**
     * Display a listing of published Aartis.
     */
    public function aartis(Request $request): View
    {
        $query = DevotionalContent::published()
            ->aartis()
            ->with(['translations', 'language', 'media']);

        if ($request->filled('deity')) {
            $query->forDeity($request->input('deity'));
        }

        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('deity_name', 'like', "%{$term}%")
                    ->orWhere('author_name', 'like', "%{$term}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', "%{$term}%"));
            });
        }

        $items = $query->orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $type = 'aarti';
        $pageTitle = 'सम्पूर्ण आरती संग्रह (Shree Aarti Sangrah)';
        $pageDescription = 'नित्य वन्दना हेतु पावन आरती संग्रह, शुद्ध पाठ, लिरिक्स एवं ऑडियो श्रवण।';

        return view('public.devotionals.index', compact('items', 'type', 'pageTitle', 'pageDescription'));
    }

    /**
     * Display the specified Chalisa.
     */
    public function showChalisa(Request $request, string $localeOrSlug, ?string $slug = null): View
    {
        $slug = $slug ?? $localeOrSlug;

        return $this->showItem($request, $slug, 'chalisa');
    }

    /**
     * Display the specified Aarti.
     */
    public function showAarti(Request $request, string $localeOrSlug, ?string $slug = null): View
    {
        $slug = $slug ?? $localeOrSlug;

        return $this->showItem($request, $slug, 'aarti');
    }

    protected function showItem(Request $request, string $slug, string $type): View
    {
        $item = DevotionalContent::where('slug', $slug)
            ->where('type', $type)
            ->with(['translations', 'language', 'media'])
            ->firstOrFail();

        if (! $item->isPublished()) {
            $user = $request->user();
            if (! ($user && $user->is_admin)) {
                abort(404);
            }
        }

        $item->increment('view_count');

        // Related devotionals for cross-linking
        $related = DevotionalContent::published()
            ->where('id', '!=', $item->id)
            ->where(function ($q) use ($item) {
                if ($item->deity_name) {
                    $q->where('deity_name', $item->deity_name);
                } else {
                    $q->where('type', $item->type);
                }
            })
            ->with(['translations'])
            ->limit(4)
            ->get();

        return view('public.devotionals.show', compact('item', 'related'));
    }
}
