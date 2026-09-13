<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs">
    @guest
        <!-- Guest Callout -->
        <div class="text-center py-4">
            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-brand-800 mb-3">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">
                Rate and Review this Book (समीक्षा व रेटिंग दें)
            </h3>
            <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                Sign in to your library account to submit a 1–5 star rating and share your literary reflection with other readers.
            </p>
            <div class="mt-4 flex items-center justify-center gap-3">
                <a 
                    href="{{ route('login') }}" 
                    class="inline-flex items-center justify-center rounded-lg bg-brand-700 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-brand-800 transition"
                >
                    Sign In to Rate &amp; Review
                </a>
                <a 
                    href="{{ route('register') }}" 
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition"
                >
                    Create Free Account
                </a>
            </div>
        </div>
    @else
        <!-- Success Alert -->
        @if (session()->has('review_success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-800 flex items-start gap-2.5">
                <svg class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <p class="font-semibold">{{ session('review_success') }}</p>
                </div>
            </div>
        @endif

        <!-- Existing Review Status Header if already submitted -->
        @if ($existingStatus && ! $isEditing && ! $submitted)
            <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-900">Your Rating &amp; Review</span>
                        @if ($existingStatus === 'approved')
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-semibold text-emerald-800 border border-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                Published
                            </span>
                        @elseif ($existingStatus === 'pending')
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-semibold text-amber-800 border border-amber-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                Pending Moderation
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-semibold text-rose-800 border border-rose-200">
                                Status: {{ ucfirst($existingStatus) }}
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <x-rating-stars :rating="$rating" :showCount="false" size="sm" />
                        @if ($title)
                            <span class="text-xs font-semibold text-slate-700 truncate max-w-xs">"{{ $title }}"</span>
                        @endif
                    </div>
                    @if ($body)
                        <p class="mt-1 text-xs text-slate-600 italic clamp-2">
                            {{ $body }}
                        </p>
                    @endif
                </div>

                <button 
                    type="button" 
                    wire:click="toggleEdit" 
                    class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition self-start sm:self-center shrink-0"
                >
                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                    </svg>
                    Edit Review
                </button>
            </div>
        @else
            <!-- Submission / Edit Form -->
            <form wire:submit="submit" class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ $existingStatus ? 'Update Your Rating & Review' : 'Write a Review & Rate (समीक्षा व रेटिंग)' }}
                    </h3>
                    @if ($isEditing)
                        <button type="button" wire:click="toggleEdit" class="text-xs text-slate-500 hover:text-slate-800">
                            Cancel
                        </button>
                    @endif
                </div>

                <!-- Star Rating Selector -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Your Rating (आपकी रेटिंग) <span class="text-brand-700">*</span>
                    </label>
                    <div class="flex items-center gap-1.5" role="radiogroup" aria-label="Star rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <button 
                                type="button" 
                                wire:click="setRating({{ $i }})"
                                class="p-1 text-2xl transition transform hover:scale-110 focus:outline-hidden"
                                role="radio"
                                aria-checked="{{ $rating === $i ? 'true' : 'false' }}"
                                aria-label="{{ $i }} out of 5 stars"
                            >
                                @if ($i <= $rating)
                                    <span class="text-amber-400">★</span>
                                @else
                                    <span class="text-slate-200 hover:text-amber-300">★</span>
                                @endif
                            </button>
                        @endfor
                        <span class="ms-2 text-xs font-bold text-slate-700">
                            {{ $rating }} / 5 {{ $rating === 5 ? 'Masterpiece' : ($rating === 4 ? 'Very Good' : ($rating === 3 ? 'Good' : ($rating === 2 ? 'Fair' : 'Poor'))) }}
                        </span>
                    </div>
                    @error('rating') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Review Title -->
                <div>
                    <label for="review-title" class="block text-xs font-semibold text-slate-700 mb-1">
                        Review Headline / Summary (शीर्षक - Optional)
                    </label>
                    <input 
                        type="text" 
                        id="review-title" 
                        wire:model="title" 
                        placeholder="e.g., कालजयी यथार्थवादी उपन्यास, हृदयस्पर्शी रचना"
                        maxlength="150"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-brand-600 focus:outline-hidden focus:ring-2 focus:ring-brand-600/20"
                    />
                    @error('title') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Review Body -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="review-body" class="block text-xs font-semibold text-slate-700">
                            Detailed Review (विस्तृत समीक्षा - Optional)
                        </label>
                        <span class="text-[10px] text-slate-400 font-mono">
                            Max 2000 characters
                        </span>
                    </div>
                    <textarea 
                        id="review-body" 
                        wire:model="body" 
                        rows="4" 
                        placeholder="Share your thoughts on the plot, characterization, themes, and literary style..."
                        maxlength="2000"
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-brand-600 focus:outline-hidden focus:ring-2 focus:ring-brand-600/20"
                    ></textarea>
                    @error('body') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Submit Button & Note -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                    <p class="text-[11px] text-slate-500">
                        <svg class="inline h-3.5 w-3.5 text-slate-400 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        All submitted reviews are checked by moderators before appearing publicly.
                    </p>

                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-700 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-brand-800 transition focus-visible:outline-2 focus-visible:outline-brand-700 disabled:opacity-50 shrink-0"
                    >
                        <span wire:loading.remove>
                            {{ $existingStatus ? 'Update Review' : 'Submit Review' }}
                        </span>
                        <span wire:loading class="inline-flex items-center gap-1">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        @endif
    @endguest
</div>
