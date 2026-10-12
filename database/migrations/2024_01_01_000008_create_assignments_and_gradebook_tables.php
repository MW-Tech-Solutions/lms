<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('instructions');
            $table->unsignedInteger('max_file_size_mb')->default(20);
            $table->string('allowed_mime_types')->default('pdf,docx,zip');
            $table->timestamp('due_date')->nullable();
            $table->unsignedInteger('max_points')->default(100);
            $table->json('rubric_json')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('submission_text')->nullable();
            $table->json('file_paths_json')->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['SUBMITTED', 'GRADED', 'RESUBMIT_REQUESTED'])->default('SUBMITTED');
            $table->timestamps();

            $table->index(['assignment_id', 'user_id']);
            $table->index('status');
        });

        Schema::create('grade_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('weight_percentage', 5, 2); // e.g., 40.00 for 40%
            $table->unsignedInteger('drop_lowest_count')->default(0);
            $table->timestamps();

            $table->index('course_id');
        });

        Schema::create('grade_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('grade_categories')->nullOnDelete();
            $table->enum('item_type', ['QUIZ', 'ASSIGNMENT', 'MANUAL']);
            $table->unsignedBigInteger('item_id')->nullable();
            $table->decimal('max_score', 5, 2)->default(100.00);
            $table->timestamps();

            $table->index(['course_id', 'category_id']);
            $table->index(['item_type', 'item_id']);
        });

        Schema::create('grade_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_item_id')->constrained('grade_items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('final_score', 5, 2)->default(0.00);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->unique(['grade_item_id', 'user_id']);
            $table->index(['user_id', 'is_locked']);
        });

        Schema::create('grade_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_grade_id')->constrained('grade_grades')->cascadeOnDelete();
            $table->decimal('old_score', 5, 2)->nullable();
            $table->decimal('new_score', 5, 2);
            $table->foreignId('modified_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('justification');
            $table->timestamps();

            $table->index('grade_grade_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_histories');
        Schema::dropIfExists('grade_grades');
        Schema::dropIfExists('grade_items');
        Schema::dropIfExists('grade_categories');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
    }
};
