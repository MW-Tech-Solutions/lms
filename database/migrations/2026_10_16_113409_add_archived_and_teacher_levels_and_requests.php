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
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_archived')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_archived')->default(false)->after('is_active');
            });
        }

        // Assigned teaching levels (many-to-many relationship with course_categories)
        if (!Schema::hasTable('teacher_level_assignments')) {
            Schema::create('teacher_level_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained('course_categories')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'category_id']);
            });
        }

        // Teacher requests to add additional Cambridge level(s)
        if (!Schema::hasTable('teacher_level_requests')) {
            Schema::create('teacher_level_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained('course_categories')->cascadeOnDelete();
                $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
                $table->text('request_notes')->nullable();
                $table->text('admin_feedback')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_level_requests');
        Schema::dropIfExists('teacher_level_assignments');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_archived')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_archived');
            });
        }
    }
};
