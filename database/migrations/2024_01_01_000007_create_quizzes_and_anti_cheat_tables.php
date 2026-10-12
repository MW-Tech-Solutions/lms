<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('question_banks')->cascadeOnDelete();
            $table->enum('type', ['SINGLE_CHOICE', 'MULTI_CHOICE', 'TRUE_FALSE', 'SHORT_ANSWER', 'FILL_BLANK', 'ESSAY']);
            $table->text('question_text');
            $table->unsignedInteger('points')->default(1);
            $table->json('metadata_json')->nullable();
            $table->timestamps();

            $table->index(['bank_id', 'type']);
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('sort_order')->default(0);

            $table->index(['question_id', 'sort_order']);
        });

        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedInteger('time_limit_minutes')->default(30);
            $table->boolean('shuffle_questions')->default(false);
            $table->unsignedInteger('max_attempts')->default(1);
            $table->unsignedInteger('passing_percentage')->default(70);
            $table->json('anti_cheat_flags')->nullable(); // e.g. {"blur_detection": true, "block_copy_paste": true, "webcam_snapshots": true}
            $table->timestamps();
        });

        // Pivot table between quiz and questions if questions are pooled from banks
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);

            $table->unique(['quiz_id', 'question_id']);
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->enum('state', ['IN_PROGRESS', 'SUBMITTED', 'GRADED'])->default('IN_PROGRESS');
            $table->decimal('score_obtained', 5, 2)->nullable();
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['quiz_id', 'user_id']);
            $table->index('state');
        });

        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->json('given_answer_json')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->decimal('awarded_points', 5, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
        });

        Schema::create('anti_cheat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->enum('event_type', ['TAB_SWITCH', 'COPY_ATTEMPT', 'DEVICE_DISCONNECTED', 'WEBCAM_FLAG']);
            $table->text('snapshot_url')->nullable();
            $table->timestamp('recorded_at')->useCurrent();

            $table->index(['attempt_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anti_cheat_logs');
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_banks');
    }
};
