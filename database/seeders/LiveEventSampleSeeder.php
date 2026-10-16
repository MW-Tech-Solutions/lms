<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CourseCategory;
use App\Models\LiveEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LiveEventSampleSeeder extends Seeder
{
    public function run(): void
    {
        $category = CourseCategory::first();

        $events = [
            [
                'title' => 'Cambridge IGCSE Math (0580): Extended Paper 4 Clinic',
                'level_badge' => 'IGCSE (Years 10–11)',
                'description' => 'Live step-by-step solutions to challenging algebraic and trigonometric questions with senior examiners.',
                'event_date' => now()->addDays(5)->setHour(16)->setMinute(0),
                'duration_minutes' => 60,
                'instructor_name' => 'Darren Treadway',
                'instructor_role' => 'Senior Cambridge Examiner',
                'meeting_link' => 'https://zoom.us/j/cambridge-math-0580',
                'max_capacity' => 100,
                'reserved_count' => 12,
                'is_active' => true,
            ],
            [
                'title' => 'Pure Mathematics 1 (9709): Calculus & Coordinate Geometry Intensive',
                'level_badge' => 'AS Level (Year 12)',
                'description' => 'Targeting differentiation, integration, and trigonometric identities in the official 2026 syllabus.',
                'event_date' => now()->addDays(12)->setHour(17)->setMinute(30),
                'duration_minutes' => 75,
                'instructor_name' => 'Tammi Allison',
                'instructor_role' => 'A-Level Specialist Tutor',
                'meeting_link' => 'https://zoom.us/j/cambridge-pure1-9709',
                'max_capacity' => 80,
                'reserved_count' => 8,
                'is_active' => true,
            ],
            [
                'title' => 'Cambridge Physics (9702): Paper 5 Practical Planning & Analysis',
                'level_badge' => 'A Level (Year 13)',
                'description' => 'Mastering experimental design, uncertainty propagation, and data analysis for Paper 5.',
                'event_date' => now()->addDays(20)->setHour(15)->setMinute(0),
                'duration_minutes' => 90,
                'instructor_name' => 'Micheal Cabot',
                'instructor_role' => 'Physics Examiner',
                'meeting_link' => 'https://zoom.us/j/cambridge-phys-9702',
                'max_capacity' => 60,
                'reserved_count' => 15,
                'is_active' => true,
            ],
            [
                'title' => 'Primary Checkpoint English (0844): Fiction & Non-Fiction Analysis',
                'level_badge' => 'Primary Checkpoint (Years 1–6)',
                'description' => 'Exam reading strategies, comprehension techniques, and creative writing structures.',
                'event_date' => now()->addDays(28)->setHour(14)->setMinute(0),
                'duration_minutes' => 60,
                'instructor_name' => 'Sofia Bennett',
                'instructor_role' => 'Primary Curriculum Lead',
                'meeting_link' => 'https://zoom.us/j/cambridge-primary-0844',
                'max_capacity' => 50,
                'reserved_count' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($events as $item) {
            LiveEvent::firstOrCreate(
                ['title' => $item['title']],
                array_merge($item, [
                    'slug' => Str::slug($item['title']),
                    'category_id' => $category?->id,
                ])
            );
        }
    }
}
