<?php

namespace Tests\Feature\ContentSystems;

use App\Models\Audiobook;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationAndAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected Language $language;

    protected function setUp(): void
    {
        parent::setUp();
        $this->language = Language::create(['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी']);
    }

    public function test_user_audiobook_submission_defaults_to_pending_review_and_requires_copyright(): void
    {
        $user = User::factory()->create();

        // 1. Without copyright declaration, submission must fail
        $invalidResponse = $this->actingAs($user)->post('/dashboard/submissions/audiobook', [
            'title' => 'अवैध बिना कॉपीराइट सबमिशन',
            'language_id' => $this->language->id,
        ]);
        $invalidResponse->assertSessionHasErrors(['copyright_declared']);

        // 2. Valid submission defaults to pending_review
        $response = $this->actingAs($user)->post('/dashboard/submissions/audiobook', [
            'title' => 'मेरी पहली प्रस्तुत ऑडियोबुक',
            'language_id' => $this->language->id,
            'narrator' => 'परीक्षक',
            'copyright_declared' => '1',
        ]);

        $response->assertRedirect(route('dashboard.submissions.index'));

        $audiobook = Audiobook::where('submitted_by', $user->id)->first();
        $this->assertNotNull($audiobook);
        $this->assertEquals('pending_review', $audiobook->status);
        $this->assertEquals('user_submission', $audiobook->submission_type);

        // 3. Must NOT be visible to the public until approved
        $publicResponse = $this->get("/hi/audiobooks/{$audiobook->slug}");
        $publicResponse->assertStatus(404);
    }

    public function test_user_cannot_view_other_users_pending_submissions_in_dashboard(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $abA = Audiobook::create([
            'slug' => 'user-a-submission',
            'language_id' => $this->language->id,
            'status' => 'pending_review',
            'submitted_by' => $userA->id,
        ]);
        $abA->translations()->create(['locale' => 'hi', 'title' => 'यूजर ए की गुप्त सबमिशन']);

        $abB = Audiobook::create([
            'slug' => 'user-b-submission',
            'language_id' => $this->language->id,
            'status' => 'pending_review',
            'submitted_by' => $userB->id,
        ]);
        $abB->translations()->create(['locale' => 'hi', 'title' => 'यूजर बी की निजी सबमिशन']);

        // User A dashboard
        $responseA = $this->actingAs($userA)->get('/dashboard/submissions');
        $responseA->assertStatus(200);
        $responseA->assertSee('यूजर ए की गुप्त सबमिशन');
        $responseA->assertDontSee('यूजर बी की निजी सबमिशन'); // Isolation confirmed

        // User B dashboard
        $responseB = $this->actingAs($userB)->get('/dashboard/submissions');
        $responseB->assertStatus(200);
        $responseB->assertSee('यूजर बी की निजी सबमिशन');
        $responseB->assertDontSee('यूजर ए की गुप्त सबमिशन'); // Isolation confirmed
    }
}
