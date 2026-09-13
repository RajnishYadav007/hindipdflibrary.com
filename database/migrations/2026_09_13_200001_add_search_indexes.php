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
        Schema::table('book_translations', function (Blueprint $table) {
            $table->index(['locale', 'title'], 'book_translations_locale_title_index');
        });

        Schema::table('author_translations', function (Blueprint $table) {
            $table->index(['locale', 'name'], 'author_translations_locale_name_index');
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->index(['locale', 'name'], 'category_translations_locale_name_index');
        });

        Schema::table('genre_translations', function (Blueprint $table) {
            $table->index(['locale', 'name'], 'genre_translations_locale_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genre_translations', function (Blueprint $table) {
            $table->dropIndex('genre_translations_locale_name_index');
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropIndex('category_translations_locale_name_index');
        });

        Schema::table('author_translations', function (Blueprint $table) {
            $table->dropIndex('author_translations_locale_name_index');
        });

        Schema::table('book_translations', function (Blueprint $table) {
            $table->dropIndex('book_translations_locale_title_index');
        });
    }
};
