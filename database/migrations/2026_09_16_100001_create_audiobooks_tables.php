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
        Schema::create('audiobooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->string('slug', 150)->unique();
            $table->string('narrator', 150)->nullable()->index();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('status', 30)->default('draft')->index(); // draft, pending_review, published, archived
            $table->string('visibility', 20)->default('public')->index(); // public, private, unlisted
            $table->string('submission_type', 30)->default('editorial')->index(); // editorial, user_submission
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('source_type', 50)->nullable();
            $table->string('source_url', 500)->nullable();
            $table->string('external_audio_url', 500)->nullable();
            $table->string('copyright_status', 50)->nullable();
            $table->string('license_type', 100)->nullable();
            $table->string('rights_holder', 255)->nullable();
            $table->text('permission_notes')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_free')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('play_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('audiobook_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audiobook_id')->constrained('audiobooks')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->timestamps();

            $table->unique(['audiobook_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiobook_translations');
        Schema::dropIfExists('audiobooks');
    }
};
