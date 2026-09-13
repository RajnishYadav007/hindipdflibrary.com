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
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable');
            $table->string('locale', 10)->default('en')->index();
            $table->string('canonical_url', 500)->nullable();
            $table->string('robots', 50)->default('index, follow');
            $table->string('og_image', 500)->nullable();
            $table->string('schema_type', 50)->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();

            $table->unique(['seoable_type', 'seoable_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_meta');
    }
};
