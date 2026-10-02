<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Book;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageRedesignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::firstOrCreate(['code' => 'hi'], ['name' => 'Hindi', 'native_name' => 'हिन्दी', 'direction' => 'ltr']);
        Language::firstOrCreate(['code' => 'en'], ['name' => 'English', 'native_name' => 'English', 'direction' => 'ltr']);
    }

    public function test_homepage_renders_all_four_content_pillars(): void
    {
        $response = $this->get('/hi');
        $response->assertOk();

        // 4 Pillars check
        $response->assertSee('मुफ़्त PDF पुस्तकें');
        $response->assertSee('ऑडियो बुक्स व वाचन');
        $response->assertSee('चालीसा, आरती व स्तोत्र');
        $response->assertSee('बाल साहित्य व कहानियाँ');

        // Navigation header checks
        $response->assertSee('ऑडियो बुक्स');
        $response->assertSee('चालीसा व आरती');
        $response->assertSee('बाल कहानियाँ');

        // Value propositions check
        $response->assertSee('100% निःशुल्क एवं सुरक्षित');
        $response->assertSee('इंटरैक्टिव 3D फ्लिप रीडर');
        $response->assertSee('इन-पेज ऑडियो वाचन');
        $response->assertSee('तुरंत सुरक्षित PDF डाउनलोड');
    }

    public function test_homepage_displays_audio_play_triggers(): void
    {
        // Create an audiobook
        $book = Book::create([
            'slug' => 'test-audio-book',
            'content_type' => 'chalisa',
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
        ]);
        $book->translations()->create([
            'locale' => 'hi',
            'title' => 'परीक्षण ऑडियो पुस्तक',
            'lyrics' => 'परीक्षण ऑडियो बोल...',
        ]);

        $response = $this->get('/hi');
        $response->assertOk();
        $response->assertSee('play-book-audio');
    }

    public function test_homepage_footer_contains_topic_silo_columns(): void
    {
        $response = $this->get('/hi');
        $response->assertOk();

        $response->assertSee('पावन चालीसा व स्तोत्र');
        $response->assertSee('कालजयी साहित्य व उपन्यास');
        $response->assertSee('बाल साहित्य व कहानियाँ');
        $response->assertSee('परिचय एवं नीतियाँ');
    }
}
