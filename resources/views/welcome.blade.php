@extends('layouts.base', ['title' => 'Hindi PDF Library — Classic & Contemporary Hindi Literature'])

@section('content')
<div class="space-y-16 pb-16">
    <!-- Hero / Discovery Banner -->
    <section class="relative overflow-hidden bg-gradient-to-b from-amber-50/70 via-slate-50 to-slate-50 border-b border-slate-200/80 py-12 sm:py-20">
        <x-ui.container size="default" class="text-center">
            <!-- Trust Badge -->
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-100/80 px-3 py-1 text-xs font-semibold text-brand-900 mb-6 border border-amber-200/80 shadow-2xs">
                <svg class="h-3.5 w-3.5 text-brand-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
                <span>डिजिटल हिन्दी पुस्तकालय धरोहर • Free & Legal Digital Library</span>
            </div>

            <!-- Main Heading -->
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight sm:leading-tight max-w-3xl mx-auto">
                Discover, Read &amp; Study Classic Hindi Literature
            </h1>
            
            <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                हजारों कालजयी उपन्यास, कहानियाँ, काव्य संग्रह और वैचारिक पुस्तकें निःशुल्क पढ़ें और सुरक्षित पीडीएफ डाउनलोड करें।
            </p>

            <!-- Centered Discovery Search Box -->
            <div class="mt-8 max-w-xl mx-auto">
                <x-search-box size="large" placeholder="Search books, authors (मुंशी प्रेमचंद, गोदान, कबीर...)" />
                <div class="mt-2.5 flex flex-wrap items-center justify-center gap-2 text-xs text-slate-500">
                    <span class="font-medium text-slate-400">Popular:</span>
                    <a href="#browse-books" class="hover:text-brand-700 underline underline-offset-2">गोदान (Godaan)</a>
                    <span>•</span>
                    <a href="#browse-books" class="hover:text-brand-700 underline underline-offset-2">मधुशाला</a>
                    <span>•</span>
                    <a href="#browse-books" class="hover:text-brand-700 underline underline-offset-2">प्रेमचंद</a>
                    <span>•</span>
                    <a href="#browse-books" class="hover:text-brand-700 underline underline-offset-2">रश्मिरथी</a>
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- Curated / Sample Featured Books -->
    <section id="browse-books">
        <x-ui.container size="default">
            <x-ui.section-header 
                title="Featured Literature (प्रमुख पुस्तकें)"
                subtitle="Curated cultural works, classic fiction, and digital archive collections."
                actionLabel="Explore All Books"
                actionUrl="#all-books"
            />

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                <!-- Sample Book Card 1 -->
                <x-book-card 
                    title="गोदान (Godan)"
                    author="मुंशी प्रेमचंद"
                    category="उपन्यास (Novel)"
                    language="hi"
                    year="1936"
                    rating="4.8"
                    ratingCount="428"
                />

                <!-- Sample Book Card 2 -->
                <x-book-card 
                    title="गबन (Gaban)"
                    author="मुंशी प्रेमचंद"
                    category="उपन्यास (Novel)"
                    language="hi"
                    year="1931"
                    rating="4.6"
                    ratingCount="215"
                />

                <!-- Sample Book Card 3 -->
                <x-book-card 
                    title="मधुशाला (Madhushala)"
                    author="हरिवंश राय बच्चन"
                    category="काव्य (Poetry)"
                    language="hi"
                    year="1935"
                    rating="4.9"
                    ratingCount="560"
                />

                <!-- Sample Book Card 4 -->
                <x-book-card 
                    title="चंद्रकांता (Chandrakanta)"
                    author="देवकीनंदन खत्री"
                    category="तिलिस्म (Fantasy)"
                    language="hi"
                    year="1888"
                    rating="4.7"
                    ratingCount="310"
                />

                <!-- Sample Book Card 5 -->
                <x-book-card 
                    title="रश्मिरथी (Rashmirathi)"
                    author="रामधारी सिंह 'दिनकर'"
                    category="खंडकाव्य (Poetry)"
                    language="hi"
                    year="1952"
                    rating="4.9"
                    ratingCount="640"
                />

                <!-- Sample Book Card 6 -->
                <x-book-card 
                    title="गुनाहों का देवता"
                    author="धर्मवीर भारती"
                    category="उपन्यास (Novel)"
                    language="hi"
                    year="1949"
                    rating="4.8"
                    ratingCount="820"
                />
            </div>
        </x-ui.container>
    </section>

    <!-- Author Showcase -->
    <section id="browse-authors">
        <x-ui.container size="default">
            <x-ui.section-header 
                title="Distinguished Authors (प्रतिष्ठित लेखक)"
                subtitle="Explore legendary literary figures of Hindi, Sanskrit, and regional literature."
                actionLabel="View All Authors"
                actionUrl="#all-authors"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-author-card 
                    name="मुंशी प्रेमचंद (Premchand)"
                    bookCount="42"
                    bio="उपन्यास सम्राट, कालजयी हिन्दी एवं उर्दू साहित्यकार (1880–1936)."
                />

                <x-author-card 
                    name="रामधारी सिंह 'दिनकर'"
                    bookCount="28"
                    bio="राष्ट्रकवि, ओजस्वी रचनाकार एवं ज्ञानपीठ पुरस्कार से सम्मानित (1908–1974)."
                />

                <x-author-card 
                    name="महादेवी वर्मा (Mahadevi Varma)"
                    bookCount="24"
                    bio="छायावाद की प्रमुख कवयित्री, ज्ञानपीठ पुरस्कार विजेता (1907–1987)."
                />

                <x-author-card 
                    name="जयशंकर प्रसाद (Prasad)"
                    bookCount="31"
                    bio="छायावादी युग के प्रवर्तक, कामायनी के रचयिता और महान नाटककार (1889–1937)."
                />
            </div>
        </x-ui.container>
    </section>

    <!-- Category Taxonomies -->
    <section id="browse-categories">
        <x-ui.container size="default">
            <x-ui.section-header 
                title="Explore By Category (श्रेणी अनुसार खोजें)"
                subtitle="Find literature categorized by literary forms, academic subjects, and disciplines."
                actionLabel="All Categories"
                actionUrl="#all-categories"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <x-category-card 
                    name="उपन्यास (Novels)"
                    description="Classic narratives, social realism, and modern prose literature."
                    bookCount="350"
                />

                <x-category-card 
                    name="काव्य व कविता (Poetry)"
                    description="Ancient dohas, Chhayavad, progressive and contemporary verses."
                    bookCount="280"
                />

                <x-category-card 
                    name="इतिहास व संस्कृति (History)"
                    description="Indian heritage, archaeological research, and historical documentation."
                    bookCount="195"
                />

                <x-category-card 
                    name="दर्शन व आध्यात्म (Philosophy)"
                    description="Upanishads, spiritual commentaries, ethics, and Vedanta texts."
                    bookCount="140"
                />
            </div>
        </x-ui.container>
    </section>

    <!-- Cultural Trust & Mission Callout -->
    <section>
        <x-ui.container size="default">
            <div class="rounded-2xl border border-amber-200/80 bg-gradient-to-r from-amber-50/80 via-white to-amber-50/50 p-6 sm:p-10 shadow-xs">
                <div class="max-w-3xl">
                    <div class="flex items-center gap-2 text-brand-800 font-semibold text-xs tracking-wider uppercase mb-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                        Public Domain & Open Knowledge Commitment
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900">
                        Preserving India's Written Cultural Heritage in the Digital Age
                    </h2>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Hindi PDF Library is dedicated to cataloging and providing free, accessible digital editions of public-domain cultural treasures. Every title is audited for copyright status to ensure open, safe educational exploration for students, scholars, and readers across the globe.
                    </p>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <x-ui.button href="#browse-books" variant="primary" size="sm">
                            Explore Public Library
                        </x-ui.button>
                        <x-ui.button href="#copyright" variant="outline" size="sm">
                            Rights & Public Domain Policy
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>
</div>
@endsection
