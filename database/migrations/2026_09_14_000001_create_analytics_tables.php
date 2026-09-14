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
        // 1. Raw Analytics Events Table
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name', 64)->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('visitor_id', 64)->nullable()->index();
            $table->string('session_id', 64)->nullable()->index();
            $table->unsignedBigInteger('book_id')->nullable()->index();
            $table->unsignedBigInteger('author_id')->nullable()->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->string('search_query', 255)->nullable();
            $table->unsignedInteger('search_result_count')->nullable();
            $table->string('source', 32)->nullable()->index();
            $table->string('country', 8)->default('unknown')->index();
            $table->string('device_type', 16)->default('unknown')->index();
            $table->string('browser', 32)->nullable();
            $table->string('operating_system', 32)->nullable();
            $table->string('url_path', 512)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->index();

            // Composite performance indexes
            $table->index(['event_name', 'occurred_at'], 'idx_events_name_time');
            $table->index(['book_id', 'event_name', 'occurred_at'], 'idx_events_book_name_time');
            $table->index(['occurred_at', 'source'], 'idx_events_time_source');
        });

        // 2. Aggregated Daily Analytics Table
        Schema::create('analytics_daily', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('event_name', 64)->index();
            $table->unsignedBigInteger('book_id')->nullable()->index();
            $table->string('source', 32)->default('other');
            $table->string('country', 8)->default('unknown');
            $table->string('device_type', 16)->default('unknown');
            $table->unsignedInteger('event_count')->default(0);
            $table->unsignedInteger('unique_visitors')->default(0);
            $table->unsignedInteger('unique_sessions')->default(0);
            $table->timestamps();

            // Composite index for rapid dashboard querying
            $table->index(['date', 'event_name'], 'idx_daily_date_event');
            $table->index(['date', 'book_id'], 'idx_daily_date_book');
            $table->index(['event_name', 'book_id'], 'idx_daily_event_book');
        });

        // 3. Aggregated Daily Search Analytics Table
        Schema::create('analytics_search_daily', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('search_query', 255)->index();
            $table->string('locale', 8)->default('en');
            $table->unsignedInteger('search_count')->default(0);
            $table->unsignedInteger('zero_result_count')->default(0);
            $table->unsignedInteger('click_count')->default(0);
            $table->timestamps();

            $table->index(['date', 'search_query'], 'idx_search_daily_date_query');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_search_daily');
        Schema::dropIfExists('analytics_daily');
        Schema::dropIfExists('analytics_events');
    }
};
