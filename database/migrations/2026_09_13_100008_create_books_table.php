<?php

use App\Enums\BookStatus;
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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 150)->unique();
            $table->string('status', 20)->default(BookStatus::Draft->value)->index();
            $table->unsignedSmallInteger('publication_year')->nullable()->index();
            $table->string('publisher', 150)->nullable();
            $table->string('isbn', 20)->nullable()->index();
            $table->string('isbn13', 20)->nullable()->index();
            $table->unsignedSmallInteger('pages')->nullable();
            $table->string('edition', 50)->nullable();
            $table->foreignId('original_language_id')->nullable()->constrained('languages')->nullOnDelete();

            // Denormalized ratings and engagement counters (canonical source: reviews)
            $table->decimal('rating_average', 3, 2)->default(0.00)->index();
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);

            // Legal & Source tracking foundation
            $table->string('source_url', 500)->nullable();
            $table->string('license_type', 100)->nullable();
            $table->string('rights_holder', 255)->nullable();
            $table->string('copyright_status', 50)->nullable();
            $table->text('permission_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
