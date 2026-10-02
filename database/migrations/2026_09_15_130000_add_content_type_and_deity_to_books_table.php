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
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'content_type')) {
                $table->string('content_type', 30)->default('book')->after('slug')->index()
                    ->comment('Type of spiritual content: book, chalisa, aarti, stotra, mantra, etc.');
            }

            if (! Schema::hasColumn('books', 'deity')) {
                $table->string('deity', 50)->nullable()->after('content_type')->index()
                    ->comment('Divine entity or topic tag: hanuman, shiva, ram, krishna, etc.');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'deity')) {
                $table->dropColumn('deity');
            }
            if (Schema::hasColumn('books', 'content_type')) {
                $table->dropColumn('content_type');
            }
        });
    }
};
