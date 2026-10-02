<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audiobook_chapters', function (Blueprint $table) {
            $table->string('source_file_name', 255)->nullable()->after('external_audio_url');
            $table->string('mime_type', 50)->nullable()->after('source_file_name');
        });
    }

    public function down(): void
    {
        Schema::table('audiobook_chapters', function (Blueprint $table) {
            $table->dropColumn(['source_file_name', 'mime_type']);
        });
    }
};
