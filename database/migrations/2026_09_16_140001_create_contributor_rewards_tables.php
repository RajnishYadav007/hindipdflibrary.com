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
        // 1. Contributor Points Ledger (Append-only immutable event store)
        Schema::create('contributor_points_ledger', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key', 64)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('points'); // Signed: positive for awards, negative for deductions/revocations
            $table->string('event_type', 50); // content_published, content_revoked, referral_bonus, admin_adjustment, etc.
            $table->string('content_type', 100)->nullable();
            $table->unsignedBigInteger('content_id')->nullable();
            $table->string('reference_id', 100)->nullable()->index();
            $table->string('description', 255);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['content_type', 'content_id']);
        });

        // 2. Referrals tracking
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('status', 30)->default('pending'); // pending, qualified, rejected, revoked
            $table->timestamp('qualified_at')->nullable();
            $table->string('qualifying_content_type', 100)->nullable();
            $table->unsignedBigInteger('qualifying_content_id')->nullable();
            $table->timestamps();

            $table->index(['referrer_id', 'status']);
        });

        // 3. User cached aggregates & referral link
        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_points')->default(0)->after('is_admin')->index();
            $table->string('contributor_level', 50)->default('reader')->after('total_points')->index();
            $table->string('referral_code', 16)->nullable()->unique()->after('contributor_level');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referred_by', 'referral_code', 'contributor_level', 'total_points']);
        });

        Schema::dropIfExists('referrals');
        Schema::dropIfExists('contributor_points_ledger');
    }
};
