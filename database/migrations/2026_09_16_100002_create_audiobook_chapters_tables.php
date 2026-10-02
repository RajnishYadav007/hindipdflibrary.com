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
        Schema::dropIfExists('audiobook_chapter_translations');
        Schema::dropIfExists('audiobook_chapters');

        Schema::create('audiobook_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audiobook_id')->constrained('audiobooks')->cascadeOnDelete();
            $table->unsignedInteger('chapter_number');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('external_audio_url', 500)->nullable();
            $table->string('status', 30)->default('draft')->index(); // draft, published, archived
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['audiobook_id', 'chapter_number'], 'ab_chapter_num_unique');
        });

        Schema::create('audiobook_chapter_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audiobook_chapter_id')->constrained('audiobook_chapters')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['audiobook_chapter_id', 'locale'], 'ab_chapter_loc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiobook_chapter_translations');
        Schema::dropIfExists('audiobook_chapters');
    }
};
