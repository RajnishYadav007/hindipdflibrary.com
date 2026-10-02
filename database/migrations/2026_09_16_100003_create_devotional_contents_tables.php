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
        Schema::dropIfExists('devotional_content_translations');
        Schema::dropIfExists('devotional_contents');

        Schema::create('devotional_contents', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->index(); // chalisa, aarti
            $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->string('slug', 150)->unique();
            $table->string('deity_name', 100)->nullable()->index();
            $table->string('author_name', 150)->nullable();
            $table->string('status', 30)->default('draft')->index(); // draft, pending_review, published, archived
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('copyright_status', 50)->nullable();
            $table->string('license_type', 100)->nullable();
            $table->string('rights_holder', 255)->nullable();
            $table->text('permission_notes')->nullable();
            $table->string('source_url', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('listen_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('devotional_content_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devotional_content_id')->constrained('devotional_contents')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->longText('content')->nullable(); // Formatted stanzas, chaupais, dohas, lyrics
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->timestamps();

            $table->unique(['devotional_content_id', 'locale'], 'dev_content_loc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devotional_content_translations');
        Schema::dropIfExists('devotional_contents');
    }
};
