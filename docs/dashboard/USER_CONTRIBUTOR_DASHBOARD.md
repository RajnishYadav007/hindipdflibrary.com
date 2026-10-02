# Unified Reader & Contributor Dashboard Documentation

**Project:** Hindi PDF Library (hindipdflibrary.com)  
**Stack:** Laravel 13, Livewire 4, Filament 5, Tailwind CSS, Alpine.js, MySQL 8  
**Route:** /dashboard  
**Authorization:** Authenticated Users (uth middleware)

---

## 1. Architectural Vision & Scope

The /dashboard route serves as the unified Reader + Contributor portal for hindipdflibrary.com. Community members can discover and read Hindi literature while also contributing rare public-domain works, audio recordings, devotional literature, and folk stories.

### 6 Core Contribution Formats Supported
1. **Books (पुस्तकें - PDF):** Rare Hindi manuscripts, books, scriptures, and literature with validated PDF upload.
2. **Audiobooks (ऑडियोबुक्स):** Spoken narratives, chapters, narration speed, and audio streaming.
3. **Chalisa (श्री चालीसा):** Sacred verse hymns, devotional chaupais, and dohas.
4. **Aarti (आरती संग्रह):** Devotional prayers, traditional hymns, and morning/evening aartis.
5. **Children's Stories (बाल साहित्य):** Educational, moral, and folklore stories categorized by age group (3-5, 6-8, 9-12, ll).
6. **General / Folk Stories (कथा एवं लोककथा):** Mythological, regional, and serial episodic stories.

---

## 2. Moderation Workflow & Public Attribution

1. **Default Status (pending_review):**
   - Direct publishing is strictly prohibited. Every user submission defaults to pending_review (or draft for user books).
   - Only admin / editorial approval transitions content to published.

2. **Public Contributor Attribution:**
   - When content is approved and published, the public page (/books/{slug}, /audiobooks/{slug}, /chalisa/{slug}, /aarti/{slug}, /stories/{slug}) prominently showcases the contributor badge:
     `lade
     <x-contributor-badge :user=->submitter />
     `
     Rendering: **योगदानकर्ता / प्रस्तोता: {Public Display Name}**
   - If content is not yet published, no contributor attribution or private data is exposed publicly.

3. **Privacy & Security Enforcement:**
   - Submitter email, raw IDs, and internal moderation notes are never leaked to the frontend.
   - Editorial guidance is only displayed to the author when status is ejected or evision_required via the $model->user_facing_feedback accessor.

4. **Multi-Tenant Ownership Defense:**
   - Users can only delete their own submissions in draft, pending_review, or evision_required.
   - Attempting to delete another user's submission returns **403 Forbidden**.
   - Attempting to delete an already **published** submission returns **403 Forbidden** (defense against vandalism of live library content).

---

## 3. Dashboard Features & Layout

### 3.1. Executive KPI Summary Cards
Calculated using pure aggregate SQL count queries (zero N+1):
- **कुल प्रस्तुतियां (Total Submissions):** Books + Audiobooks + Devotionals + Stories.
- **समीक्षाधीन (Under Review):** Submissions currently awaiting editorial review.
- **वेबसाइट पर प्रकाशित (Live Published):** Submissions accepted and live with public attribution.
- **पसंदीदा (Favorites Saved):** Saved books in personal reading list.

### 3.2. Quick Contribution Grid (6 Cards)
Direct entry points to dedicated creation forms:
- + नई पुस्तक जोड़ें -> oute('dashboard.books.create')
- + ऑडियोबुक जोड़ें -> oute('dashboard.submissions.audiobook.create')
- + चालीसा जोड़ें -> oute('dashboard.submissions.chalisa.create')
- + आरती जोड़ें -> oute('dashboard.submissions.aarti.create')
- + बाल कहानी जोड़ें -> oute('dashboard.submissions.story.create', ['type' => 'children'])
- + कथा व लोककथा जोड़ें -> oute('dashboard.submissions.story.create', ['type' => 'general'])

### 3.3. Multi-Format Submissions Repository
Filterable table tab powered by Alpine.js (subFilter: 'all' | 'books' | 'audiobooks' | 'devotionals' | 'stories'):
- Comprehensive columns: Title, Contributor/Author/Deity, Review Status Pill, Submission Date, Actions.
- Non-published items allow safe deletion with confirmation.
- Published items provide direct live preview links (View Live →).
- Editorial feedback callout on items requiring revision or rejected.

### 3.4. Saved Favorites & Reader Reviews
- Dedicated grid of book cards for quick reading and downloading.
- Review management tracking star ratings and community review status.

### 3.5. Contributor Attribution & Profile Preview Tab
- Real-time preview of how the user's public contributor badge appears on live book/audio/story pages.
- Direct shortcut to oute('profile') to manage public display name.
- Guidelines regarding copyright, public domain, and scan/audio quality.

---

## 4. Automated Test Coverage

Verified via 	ests/Feature/User/UserDashboardTest.php (19 tests, 68 assertions):
- 	est_guest_is_redirected_to_login_from_dashboard
- 	est_authenticated_user_can_view_dashboard_with_kpis_and_contributor_badge
- 	est_dashboard_scopes_submissions_strictly_to_authenticated_user
- 	est_user_can_view_add_book_form
- 	est_user_can_submit_a_new_book_with_new_author
- 	est_user_can_submit_book_with_pdf_upload
- 	est_user_can_delete_their_pending_draft_book
- 	est_user_cannot_delete_published_book (403 Defense)
- 	est_user_cannot_delete_another_users_book (403 Defense)
- 	est_user_can_delete_their_pending_audiobook
- 	est_user_cannot_delete_published_audiobook (403 Defense)
- 	est_user_cannot_delete_another_users_audiobook (403 Defense)
- 	est_user_can_delete_their_pending_devotional
- 	est_user_cannot_delete_published_devotional (403 Defense)
- 	est_user_cannot_delete_another_users_devotional (403 Defense)
- 	est_user_can_submit_story_and_delete_pending_story
- 	est_user_cannot_delete_published_story_or_another_users_story (403 Defense)
- 	est_public_pages_display_contributor_badge_when_published (Privacy check)
- 	est_user_can_toggle_and_remove_favorites
