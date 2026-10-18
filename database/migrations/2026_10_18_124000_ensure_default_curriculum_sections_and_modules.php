<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $courses = Course::whereDoesntHave('sections.modules')->get();

        foreach ($courses as $course) {
            $section = $course->sections()->first();
            if (!$section) {
                $section = CourseSection::create([
                    'course_id' => $course->id,
                    'title' => 'Core Curriculum & Syllabus Framework',
                    'sort_order' => 0,
                ]);
            }

            // Module 1: Video Lecture
            CourseModule::firstOrCreate(
                [
                    'section_id' => $section->id,
                    'module_type' => CourseModule::TYPE_VIDEO,
                ],
                [
                    'sort_order' => 0,
                    'is_mandatory' => true,
                ]
            );

            // Module 2: In-Depth Study Notes / Reading Pack
            CourseModule::firstOrCreate(
                [
                    'section_id' => $section->id,
                    'module_type' => CourseModule::TYPE_PAGE,
                ],
                [
                    'sort_order' => 1,
                    'is_mandatory' => false,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive rollback needed for initialized placeholder modules
    }
};
