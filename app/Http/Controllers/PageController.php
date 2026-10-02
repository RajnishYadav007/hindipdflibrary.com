<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * About the Library.
     */
    public function about(): View
    {
        $isHindi = app()->getLocale() === 'hi';
        $title = $isHindi
            ? 'परिचय एवं उद्देश्य — हिन्दी पीडीएफ पुस्तकालय (About Us)'
            : 'About the Library — Hindi PDF Library';

        return view('public.pages.about', compact('title'));
    }

    /**
     * Help & Frequently Asked Questions (FAQs).
     */
    public function help(): View
    {
        $isHindi = app()->getLocale() === 'hi';
        $title = $isHindi
            ? 'सहायता एवं अक्सर पूछे जाने वाले प्रश्न (Help & FAQs)'
            : 'Help & FAQs — Hindi PDF Library';

        return view('public.pages.help', compact('title'));
    }

    /**
     * Contact Staff / Support.
     */
    public function contact(): View
    {
        $isHindi = app()->getLocale() === 'hi';
        $title = $isHindi
            ? 'संपर्क करें — हिन्दी पीडीएफ पुस्तकालय (Contact Us)'
            : 'Contact Staff — Hindi PDF Library';

        return view('public.pages.contact', compact('title'));
    }

    /**
     * Process contact form submission.
     */
    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $isHindi = app()->getLocale() === 'hi';
        $msg = $isHindi
            ? 'धन्यवाद! आपका संदेश सफलतापूर्वक भेज दिया गया है। हमारी टीम जल्द ही आपसे संपर्क करेगी।'
            : 'Thank you! Your message has been successfully received. Our library staff will respond shortly.';

        return back()->with('status', $msg);
    }

    /**
     * Suggest a Book for digitization or cataloging.
     */
    public function suggestBook(): View
    {
        $isHindi = app()->getLocale() === 'hi';
        $title = $isHindi
            ? 'पुस्तक का सुझाव दें (Suggest a Book)'
            : 'Suggest a Book — Hindi PDF Library';

        return view('public.pages.suggest-book', compact('title'));
    }

    /**
     * Process book suggestion submission.
     */
    public function submitSuggestBook(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publication_year' => ['nullable', 'digits:4'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        $isHindi = app()->getLocale() === 'hi';
        $msg = $isHindi
            ? 'धन्यवाद! आपका पुस्तक सुझाव प्राप्त हो गया है। हमारी संपादन टीम इसकी समीक्षा करेगी।'
            : 'Thank you! Your book suggestion has been submitted. Our curation team will review it.';

        return back()->with('status', $msg);
    }

    /**
     * Privacy Policy.
     */
    public function privacy(): View
    {
        $isHindi = app()->getLocale() === 'hi';
        $title = $isHindi
            ? 'गोपनीयता नीति (Privacy Policy)'
            : 'Privacy Policy — Hindi PDF Library';

        return view('public.pages.privacy', compact('title'));
    }

    /**
     * Terms of Service.
     */
    public function terms(): View
    {
        $isHindi = app()->getLocale() === 'hi';
        $title = $isHindi
            ? 'सेवा की शर्तें (Terms of Service)'
            : 'Terms of Service — Hindi PDF Library';

        return view('public.pages.terms', compact('title'));
    }

    /**
     * Copyright & DMCA Policy.
     */
    public function copyright(): View
    {
        $isHindi = app()->getLocale() === 'hi';
        $title = $isHindi
            ? 'कॉपीराइट नीति एवं डीएमसीए (Copyright & DMCA)'
            : 'Copyright & DMCA Policy — Hindi PDF Library';

        return view('public.pages.copyright', compact('title'));
    }

    /**
     * Public Domain Statement.
     */
    public function publicDomain(): View
    {
        $isHindi = app()->getLocale() === 'hi';
        $title = $isHindi
            ? 'सार्वजनिक डोमेन नीति (Public Domain Statement)'
            : 'Public Domain Statement — Hindi PDF Library';

        return view('public.pages.public-domain', compact('title'));
    }
}
