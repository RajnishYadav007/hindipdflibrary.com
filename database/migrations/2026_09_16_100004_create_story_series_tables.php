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
        Schema::dropIfExists('story_series_translations');
        Schema::dropIfExists('story_series');

        Schema::create('story_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->string('slug', 150)->unique();
            $table->string('age_group', 20)->default('all')->index(); // '3-5', '6-8', '9-12', 'all'
            $table->string('status', 30)->default('draft')->index(); // draft, pending_review, published, archived
            $table->boolean('is_featured')->default(false)->index();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('copyright_status', 50)->nullable();
            $table->string('license_type', 100)->nullable();
            $table->string('rights_holder', 255)->nullable();
            $table->text('permission_notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('story_series_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_series_id')->constrained('story_series')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->timestamps();

            $table->unique(['story_series_id', 'locale'], 'story_series_loc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_series_translations');
        Schema::dropIfExists('story_series');
    }
};
