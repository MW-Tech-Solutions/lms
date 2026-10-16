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
        // 1. Add approval_status to users table
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'approval_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('approval_status', ['APPROVED', 'PENDING', 'UNAPPROVED', 'REJECTED'])
                    ->default('APPROVED')
                    ->after('is_active');
            });
        }

        // 2. Create live_events table
        if (!Schema::hasTable('live_events')) {
            Schema::create('live_events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->foreignId('category_id')->nullable()->constrained('course_categories')->nullOnDelete();
                $table->string('level_badge')->default('Cambridge Live Masterclass');
                $table->text('description')->nullable();
                $table->dateTime('event_date');
                $table->integer('duration_minutes')->default(60);
                $table->string('instructor_name')->default('Cambridge Lead Tutor');
                $table->string('instructor_role')->default('Senior Examiner & Mentor');
                $table->string('instructor_avatar')->nullable();
                $table->string('meeting_link')->nullable();
                $table->integer('max_capacity')->default(100);
                $table->integer('reserved_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Create event_reservations table
        if (!Schema::hasTable('event_reservations')) {
            Schema::create('event_reservations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('live_events')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->enum('status', ['PENDING', 'CONFIRMED', 'REJECTED'])->default('PENDING');
                $table->timestamp('approval_email_sent_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['event_id', 'status']);
            });
        }

        // 4. Create faqs table
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('category')->default('General');
                $table->string('question');
                $table->text('answer');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        // 5. Create newsletter_subscribers table
        if (!Schema::hasTable('newsletter_subscribers')) {
            Schema::create('newsletter_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('level_interest')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('subscribed_at')->nullable();
                $table->timestamps();
            });
        }

        // 6. Add public_preview_content to courses table
        if (Schema::hasTable('courses') && !Schema::hasColumn('courses', 'public_preview_content')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->text('public_preview_content')->nullable()->after('description');
            });
        }

        // 7. Add public_preview_content to course_resources table
        if (Schema::hasTable('course_resources') && !Schema::hasColumn('course_resources', 'public_preview_content')) {
            Schema::table('course_resources', function (Blueprint $table) {
                $table->text('public_preview_content')->nullable()->after('intro');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('course_resources') && Schema::hasColumn('course_resources', 'public_preview_content')) {
            Schema::table('course_resources', function (Blueprint $table) {
                $table->dropColumn('public_preview_content');
            });
        }

        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'public_preview_content')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('public_preview_content');
            });
        }

        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('event_reservations');
        Schema::dropIfExists('live_events');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'approval_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('approval_status');
            });
        }
    }
};
