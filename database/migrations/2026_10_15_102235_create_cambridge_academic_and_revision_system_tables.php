<?php

declare(strict_types=1);

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
        // 1. Cambridge Examination Series (e.g. Feb/March, May/June, Oct/Nov)
        if (!Schema::hasTable('cambridge_exam_series')) {
            Schema::create('cambridge_exam_series', function (Blueprint $table) {
                $table->id();
                $table->integer('year')->index(); // 2023, 2024, 2025, 2026
                $table->enum('season', ['F/M', 'M/J', 'O/N']); // Feb/March, May/June, Oct/Nov
                $table->string('name', 50); // e.g. "May/June 2026"
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['year', 'season']);
            });
        }

        // 2. Cambridge Components / Paper Variants (e.g. Pure 1, Pure 3, Mechanics, Stats 1)
        if (!Schema::hasTable('cambridge_paper_components')) {
            Schema::create('cambridge_paper_components', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->string('code', 20); // e.g. P1, P3, P4, P5
                $table->string('name', 100); // e.g. "Pure Mathematics 1"
                $table->integer('default_max_score')->default(75);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 3. Esther Math Past-Paper Revision Matrix & Student Attempts
        if (!Schema::hasTable('past_paper_attempts')) {
            Schema::create('past_paper_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->foreignId('series_id')->nullable()->constrained('cambridge_exam_series')->onDelete('set null');
                $table->foreignId('component_id')->nullable()->constrained('cambridge_paper_components')->onDelete('set null');
                $table->integer('year');
                $table->string('season', 10); // F/M, M/J, O/N
                $table->integer('variant')->default(1); // 1, 2, 3
                $table->decimal('score_obtained', 5, 2)->default(0);
                $table->decimal('max_score', 5, 2)->default(75);
                $table->decimal('percentage', 5, 2)->default(0);
                $table->enum('status_color', ['GREEN', 'AMBER', 'RED'])->default('RED');
                $table->text('notes')->nullable();
                $table->integer('attempt_number')->default(1);
                $table->boolean('is_latest')->default(true);
                $table->timestamps();

                $table->index(['user_id', 'course_id', 'year', 'season', 'variant']);
            });
        }

        // 4. Learning Reflections & Media Progress (3-State Reflection Workflow)
        if (!Schema::hasTable('learning_reflections')) {
            Schema::create('learning_reflections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->foreignId('module_id')->nullable()->constrained('course_modules')->onDelete('cascade');
                $table->enum('reflection_choice', [
                    'UNDERSTOOD',       // 1. Read/watched and understood (awards XP)
                    'NEED_RETURN',      // 2. Read/watched but need to return later (revision queue)
                    'NOT_UNDERSTOOD'    // 3. Read/watched but did not understand (flags teacher)
                ]);
                $table->decimal('watch_percentage', 5, 2)->default(100.00); // 90% threshold required for video/audio
                $table->boolean('is_flagged_for_teacher')->default(false);
                $table->text('student_comment')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'course_id', 'reflection_choice']);
            });
        }

        // 5. Spaced Repetition Flashcards & Deck Review Progress
        if (!Schema::hasTable('flashcard_decks')) {
            Schema::create('flashcard_decks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('flashcards')) {
            Schema::create('flashcards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('deck_id')->constrained('flashcard_decks')->onDelete('cascade');
                $table->text('front_prompt');
                $table->text('back_answer');
                $table->text('katex_formula')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('flashcard_reviews')) {
            Schema::create('flashcard_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('deck_id')->constrained('flashcard_decks')->onDelete('cascade');
                $table->foreignId('flashcard_id')->constrained('flashcards')->onDelete('cascade');
                $table->enum('state', ['KNOWN', 'STILL_LEARNING'])->default('STILL_LEARNING');
                $table->integer('review_count')->default(0);
                $table->timestamp('last_reviewed_at')->nullable();
                $table->date('next_review_date')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'flashcard_id']);
            });
        }

        // 6. Subscriptions & Paywall Entitlements (Per Assessment Level in GBP)
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->nullable()->constrained('course_categories')->onDelete('cascade');
                $table->string('name'); // e.g. "Cambridge IGCSE 3-Month Access Pass"
                $table->string('slug')->unique();
                $table->enum('duration_term', ['3_MONTHS', '12_MONTHS'])->default('3_MONTHS');
                $table->integer('price_pence'); // e.g. 4500 for £45.00
                $table->string('currency', 3)->default('GBP');
                $table->string('stripe_price_id')->nullable();
                $table->string('paypal_plan_id')->nullable();
                $table->json('features')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_subscriptions')) {
            Schema::create('user_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('cascade');
                $table->foreignId('category_id')->nullable()->constrained('course_categories')->onDelete('cascade');
                $table->enum('status', ['ACTIVE', 'PAST_DUE', 'CANCELED', 'EXPIRED'])->default('ACTIVE');
                $table->string('gateway', 20)->default('STRIPE'); // STRIPE, PAYPAL, MANUAL
                $table->string('gateway_subscription_id')->nullable()->index();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('canceled_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'category_id', 'status']);
            });
        }

        // 7. Parent & Guardian Portal Links & Invitation Codes
        if (!Schema::hasTable('parent_invites')) {
            Schema::create('parent_invites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->string('invite_code', 10)->unique(); // e.g. CMP-742K
                $table->string('guardian_email')->nullable();
                $table->timestamp('expires_at');
                $table->boolean('is_used')->default(false);
                $table->foreignId('used_by_parent_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('parent_students')) {
            Schema::create('parent_students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->string('relationship', 50)->default('Parent/Guardian');
                $table->boolean('can_view_tracker')->default(true);
                $table->boolean('can_view_billing')->default(true);
                $table->timestamps();

                $table->unique(['parent_id', 'student_id']);
            });
        }

        // 8. Gamification: XP Ledger, Daily Streaks, and Exam Countdowns
        if (!Schema::hasTable('user_xp_ledger')) {
            Schema::create('user_xp_ledger', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->integer('xp_amount');
                $table->string('activity_type', 50); // e.g. 'NOTE_READ', 'PAST_PAPER_ATTEMPT', 'QUIZ_PERFECT'
                $table->string('idempotency_key', 120)->unique(); // Prevents duplicate XP awards
                $table->string('description')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
            });
        }

        if (!Schema::hasTable('user_study_streaks')) {
            Schema::create('user_study_streaks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->integer('current_streak_days')->default(0);
                $table->integer('longest_streak_days')->default(0);
                $table->date('last_activity_date')->nullable();
                $table->string('timezone', 50)->default('Europe/London');
                $table->timestamps();

                $table->unique('user_id');
            });
        }

        if (!Schema::hasTable('student_exam_countdowns')) {
            Schema::create('student_exam_countdowns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('title'); // e.g. "Cambridge A-Level Mathematics Exam"
                $table->dateTime('target_datetime');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 9. Resource Request Form Submissions
        if (!Schema::hasTable('resource_requests')) {
            Schema::create('resource_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('level_name'); // Primary, Lower Secondary, IGCSE, AS Level, A Level
                $table->string('subject');
                $table->string('topic');
                $table->string('resource_type'); // Notes, Worked Solutions, Topical Pack, Flashcards, Video
                $table->text('details')->nullable();
                $table->enum('status', ['PENDING', 'IN_REVIEW', 'IN_PROGRESS', 'FULFILLED', 'REJECTED'])->default('PENDING');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_requests');
        Schema::dropIfExists('student_exam_countdowns');
        Schema::dropIfExists('user_study_streaks');
        Schema::dropIfExists('user_xp_ledger');
        Schema::dropIfExists('parent_students');
        Schema::dropIfExists('parent_invites');
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('flashcard_reviews');
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('flashcard_decks');
        Schema::dropIfExists('learning_reflections');
        Schema::dropIfExists('past_paper_attempts');
        Schema::dropIfExists('cambridge_paper_components');
        Schema::dropIfExists('cambridge_exam_series');
    }
};
