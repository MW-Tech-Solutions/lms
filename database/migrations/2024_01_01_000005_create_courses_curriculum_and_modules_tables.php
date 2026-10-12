<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('parent_id')->nullable()->constrained('course_categories')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('course_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->enum('target_audience', ['CORPORATE', 'PUBLIC', 'BOTH'])->default('BOTH');
            $table->enum('format', ['WEEKLY', 'TOPICS', 'SELF_PACED'])->default('SELF_PACED');
            $table->unsignedInteger('passing_grade')->default(70);
            $table->unsignedInteger('recertification_days')->nullable(); // e.g. 365 for annual compliance
            $table->unsignedInteger('price_cents')->default(0);
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['target_audience', 'is_published']);
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('drip_days_after_enrollment')->default(0);
            $table->timestamps();

            $table->index(['course_id', 'sort_order']);
        });

        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('course_sections')->cascadeOnDelete();
            $table->string('module_type', 32); // All Moodle activities and resources (LESSON, QUIZ, ASSIGNMENT, FORUM, H5P, SCORM, WORKSHOP, CHAT, GLOSSARY, WIKI, CHOICE, FEEDBACK, PAGE, BOOK, FILE, FOLDER, URL, LABEL, etc.)
            $table->unsignedBigInteger('instance_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();

            $table->index(['section_id', 'sort_order']);
            $table->index(['module_type', 'instance_id']);
        });

        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('prerequisite_course_id')->constrained('courses')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'prerequisite_course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_categories');
    }
};
