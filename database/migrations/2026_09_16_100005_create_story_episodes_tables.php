<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('story_episode_translations');
        Schema::dropIfExists('story_episodes');

        Schema::create('story_episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_series_id')->constrained('story_series')->cascadeOnDelete();
            $table->unsignedInteger('episode_number');
            $table->string('narration_type', 30)->default('tts')->index(); // tts, uploaded, none
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('external_audio_url', 500)->nullable();
            $table->string('status', 30)->default('draft')->index(); // draft, pending_review, published, archived
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('listen_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['story_series_id', 'episode_number'], 'story_ep_num_unique');
        });

        Schema::create('story_episode_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_episode_id')->constrained('story_episodes')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('title', 255);
            $table->string('slug', 150)->index();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->timestamps();

            $table->unique(['story_episode_id', 'locale'], 'story_ep_loc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_episode_translations');
        Schema::dropIfExists('story_episodes');
    }
};
