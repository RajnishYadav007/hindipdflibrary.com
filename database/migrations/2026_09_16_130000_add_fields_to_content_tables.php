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
        if (Schema::hasTable('audiobooks') && ! Schema::hasColumn('audiobooks', 'author_name')) {
            Schema::table('audiobooks', function (Blueprint $table) {
                $table->string('author_name', 150)->nullable()->after('narrator');
            });
        }

        if (Schema::hasTable('story_series')) {
            Schema::table('story_series', function (Blueprint $table) {
                if (! Schema::hasColumn('story_series', 'story_type')) {
                    $table->string('story_type', 30)->default('children')->index()->after('slug');
                }
                if (! Schema::hasColumn('story_series', 'classification')) {
                    $table->string('classification', 50)->nullable()->after('story_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('audiobooks') && Schema::hasColumn('audiobooks', 'author_name')) {
            Schema::table('audiobooks', function (Blueprint $table) {
                $table->dropColumn('author_name');
            });
        }

        if (Schema::hasTable('story_series')) {
            Schema::table('story_series', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('story_series', 'story_type')) {
                    $columnsToDrop[] = 'story_type';
                }
                if (Schema::hasColumn('story_series', 'classification')) {
                    $columnsToDrop[] = 'classification';
                }
                if (! empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
