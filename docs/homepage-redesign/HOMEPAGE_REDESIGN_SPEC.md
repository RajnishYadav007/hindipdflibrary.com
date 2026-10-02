# Homepage Redesign Specification & Architecture

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Design Reference:** Mobile (`media_1789461360426.jpg`) and Desktop (`media_1789461311144.jpg`)  
**Status:** Completed & Production-Ready  
**Tech Stack:** Laravel 13, Blade, Tailwind CSS v4, Alpine.js, Web Speech Synthesis  

---

## 1. Executive Summary
The homepage of Hindi PDF Library has been completely redesigned from the ground up into a digital reading and audiobook platform. It closely follows the reference visual language:
- **Palette:** Warm ivory background (`#fcfaf7`), deep navy/slate accents (`#0f172a`), terracotta/warm orange primary accent (`#c2410c` / `orange-600`), and muted gold badges.
- **Iconography:** Clean, modern outline monochrome icons (1.5–2px stroke). **All cartoon/emoji icons have been eliminated.**
- **Audio Experience:** Integrated in-page audiobook player card with active track selection, animated waveform visualizer, playback speed cycle (`1.0x`, `1.2x`, `1.5x`), 15s skip controls, and direct integration with the Web Speech engine.

---

## 2. Information Architecture & Section Breakdown

### 2.1. Top Header & Navigation (`<x-header />`)
- **Terracotta Brand Badge:** Rounded-xl logo with open book motif, primary title in Devanagari (`हिंदी पीडीएफ लाइब्रेरी`), subtitle (`Hindi PDF Library • मुफ़्त ज्ञान और साहित्य`).
- **Prominent Centered Search:** Rounded-2xl search bar with search icon and terracotta `खोजें` button.
- **Header Controls:** Language switcher (`हिन्दी / English`), dark mode toggle, and Login/Register CTA.
- **Sub-Nav Navigation Strip:** Dedicated horizontal secondary navigation row linking to all 8 core destinations:
  - `होम`
  - `पुस्तकें`
  - `ऑडियो बुक्स`
  - `चालीसा व आरती`
  - `बाल कहानियाँ`
  - `लेखक`
  - `श्रेणियाँ`
  - `हमारे बारे में`

### 2.2. Mobile Bottom Navigation Bar (`<x-mobile-bottom-nav />`)
- Fixed bottom dock (`bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-stone-200/90`).
- 5 distinct tabs matching mobile reference:
  1. `होम` (Home)
  2. `पुस्तकें` (Books)
  3. `ऑडियो` (Audio)
  4. `चालीसा` (Chalisas)
  5. `बाल साहित्य` (Kids)
- Hidden on desktop (`md:hidden`). Body has `pb-24 lg:pb-16` bottom clearance so content is never obscured.

### 2.3. Hero Section
- **Editorial Headline:** Bold serif Devanagari with terracotta wavy underline (`साहित्य और ज्ञान का पावन संगम — अब आपके हाथों में`).
- **Search Bar:** Universal search input with autocomplete capability and terracotta submit button.
- **High-Traffic Quick Chips:** `हनुमान चालीसा`, `श्रीमद्भगवद्गीता`, `पंचतंत्र`, `गोदान (प्रेमचंद)`, `वेद एवं पुराण`.
- **Kindle/Editorial Quote Box:** `"एक अच्छी पुस्तक हज़ार दोस्तों के बराबर होती है..." — डॉ. ए.पी.जे. अब्दुल कलाम`.
- **Layered 3D Books Composition:** Center book elevated + 2 tilted books (-6° and +6°) with glossy spine reflections and floating badges (`🎧 ऑडियोबुक्स उपलब्ध` and `⭐ 4.9/5`).

### 2.4. 4 Core Pillars Strip
- 4 clean interactive cards with outline icons:
  1. `मुफ़्त PDF पुस्तकें`
  2. `ऑडियो बुक्स व वाचन`
  3. `चालीसा, आरती व स्तोत्र`
  4. `बाल साहित्य व कहानियाँ`

### 2.5. Split Section: Featured Books + Audiobook Hub
- **Left (7 Columns): Featured Literature (विशेष पुस्तकें)**
  - 4 Alpine.js tabs: `लोकप्रिय`, `नवीनतम`, `सर्वाधिक पढ़ी गईं`, `संपादक की पसंद`.
  - 4 cards with 2:3 aspect ratio, rating badge, Devanagari title, author, and reading CTA.
- **Right (5 Columns): आज ही सुनना शुरू करें (Audiobook Hub)**
  - Kindle/Audible inspired dark slate card container.
  - Active audio track preview with book thumbnail.
  - Animated waveform equalizer visualizer.
  - Progress tracker with Devanagari numerals and time indicator.
  - Controls: 15s Rewind, prominent orange Play/Pause button, 15s Forward, and Speed toggle (`1.0x`, `1.2x`, `1.5x`).
  - Suggested tracklist from database where clicking any track immediately loads and plays it.

### 2.6. Children's Stories Section ("बच्चों की कहानियाँ")
- Age filter pills: `सभी उम्र`, `3-5 वर्ष`, `6-8 वर्ष`, `9-12 वर्ष`.
- Kid-friendly illustrated banner with emerald gradient matching `media_1789461360426.jpg`.
- Curated cards: Panchatantra, Akbar Birbal, Tenalirama, Hitopadesh with badges (`नीति कथा`, `हास्य व बुद्धि`) and 3D Reader buttons.

### 2.7. Categories Discovery ("Explore By Category")
- 8 responsive cards with clean outline monochrome SVGs and real database book counts.

### 2.8. Distinguished Authors ("Distinguished Authors")
- 6 circular portrait cards (Munshi Premchand, Rabindranath Tagore, Mahatma Gandhi, Jaishankar Prasad, Suryakant Tripathi Nirala, Mahadevi Varma).
- Clean terracotta ring border, book count pills, and author profile navigation.

### 2.9. Popular Books Rail ("सर्वाधिक लोकप्रिय पुस्तकें")
- Horizontal smooth-scrolling rail with left/right navigation arrows.

### 2.10. Multi-Device Banner ("कहीं भी पढ़ें, कहीं भी सुनें")
- Slate-900 dark banner with SVG responsive device mockup displaying Sanskrit shlokas and audio synchronization indicator.

### 2.11. 5-Column Benefits Strip & Footer
- Strip: `100% निःशुल्क एवं सुरक्षित`, `इंटरैक्टिव 3D फ्लिप रीडर`, `इन-पेज ऑडियो वाचन`, `तुरंत सुरक्षित PDF डाउनलोड`, `सांस्कृतिक धरोहर`.
- Footer: 5 columns (`Hindi PDF Library`, `कालजयी साहित्य व उपन्यास`, `पावन चालीसा व स्तोत्र`, `बाल साहित्य व कहानियाँ`, `परिचय एवं नीतियाँ`).

---

## 3. Verification & Compliance
- **PSR-12 Compliance:** Code formatted to Laravel standards.
- **Vite Build:** `npm run build` executed cleanly without errors.
- **Test Suite:** All 74 Public Feature tests pass.
- **Multilingual & SEO:** Complete Schema.org JSON-LD tags, canonical URLs, and hreflang tags preserved.