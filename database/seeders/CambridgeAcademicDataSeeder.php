<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CambridgeExamSeries;
use App\Models\CambridgePaperComponent;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class CambridgeAcademicDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Official Cambridge Examination Series (2023 - 2026)
        $seriesData = [
            ['year' => 2026, 'season' => 'M/J', 'name' => 'May/June 2026', 'start_date' => '2026-05-01', 'end_date' => '2026-06-12'],
            ['year' => 2026, 'season' => 'F/M', 'name' => 'Feb/March 2026', 'start_date' => '2026-02-15', 'end_date' => '2026-03-20'],
            ['year' => 2025, 'season' => 'O/N', 'name' => 'Oct/Nov 2025', 'start_date' => '2025-10-01', 'end_date' => '2025-11-15'],
            ['year' => 2025, 'season' => 'M/J', 'name' => 'May/June 2025', 'start_date' => '2025-05-01', 'end_date' => '2025-06-12'],
            ['year' => 2025, 'season' => 'F/M', 'name' => 'Feb/March 2025', 'start_date' => '2025-02-15', 'end_date' => '2025-03-20'],
            ['year' => 2024, 'season' => 'O/N', 'name' => 'Oct/Nov 2024', 'start_date' => '2024-10-01', 'end_date' => '2024-11-15'],
            ['year' => 2024, 'season' => 'M/J', 'name' => 'May/June 2024', 'start_date' => '2024-05-01', 'end_date' => '2024-06-12'],
            ['year' => 2024, 'season' => 'F/M', 'name' => 'Feb/March 2024', 'start_date' => '2024-02-15', 'end_date' => '2024-03-20'],
            ['year' => 2023, 'season' => 'O/N', 'name' => 'Oct/Nov 2023', 'start_date' => '2023-10-01', 'end_date' => '2023-11-15'],
            ['year' => 2023, 'season' => 'M/J', 'name' => 'May/June 2023', 'start_date' => '2023-05-01', 'end_date' => '2023-06-12'],
            ['year' => 2023, 'season' => 'F/M', 'name' => 'Feb/March 2023', 'start_date' => '2023-02-15', 'end_date' => '2023-03-20'],
        ];

        foreach ($seriesData as $s) {
            CambridgeExamSeries::updateOrCreate(
                ['year' => $s['year'], 'season' => $s['season']],
                [
                    'name' => $s['name'],
                    'start_date' => $s['start_date'],
                    'end_date' => $s['end_date'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Seed Cambridge Components for Mathematics (9709)
        $mathCourse = Course::where('slug', 'cambridge-a-level-mathematics-9709')->first();
        if ($mathCourse) {
            $components = [
                ['code' => 'P1', 'name' => 'Paper 1: Pure Mathematics 1', 'default_max_score' => 75, 'sort_order' => 1],
                ['code' => 'P3', 'name' => 'Paper 3: Pure Mathematics 3', 'default_max_score' => 75, 'sort_order' => 2],
                ['code' => 'P4', 'name' => 'Paper 4: Mechanics', 'default_max_score' => 50, 'sort_order' => 3],
                ['code' => 'P5', 'name' => 'Paper 5: Probability & Statistics 1', 'default_max_score' => 50, 'sort_order' => 4],
            ];

            foreach ($components as $c) {
                CambridgePaperComponent::updateOrCreate(
                    ['course_id' => $mathCourse->id, 'code' => $c['code']],
                    [
                        'name' => $c['name'],
                        'default_max_score' => $c['default_max_score'],
                        'sort_order' => $c['sort_order'],
                    ]
                );
            }
        }

        // 3. Seed Subscription Plans (3-Month & 12-Month in GBP) across the 5 Assessment Levels
        $levels = [
            ['slug' => 'primary-checkpoint', 'name' => 'Primary Checkpoint (Years 1–6)', 'price_3m' => 3500, 'price_12m' => 9500],
            ['slug' => 'lower-secondary', 'name' => 'Lower Secondary (Years 7–9)', 'price_3m' => 4000, 'price_12m' => 11000],
            ['slug' => 'igcse', 'name' => 'Cambridge IGCSE (Years 10–11)', 'price_3m' => 4500, 'price_12m' => 12500],
            ['slug' => 'as-level', 'name' => 'Cambridge AS Level (Year 12)', 'price_3m' => 5000, 'price_12m' => 14000],
            ['slug' => 'a-level', 'name' => 'Cambridge A Level (Year 13)', 'price_3m' => 5500, 'price_12m' => 15500],
        ];

        foreach ($levels as $l) {
            $cat = CourseCategory::where('slug', $l['slug'])->first();
            $catId = $cat ? $cat->id : null;

            // 3-Month Plan
            SubscriptionPlan::updateOrCreate(
                ['slug' => $l['slug'] . '-3-months'],
                [
                    'category_id' => $catId,
                    'name' => $l['name'] . ' — 3 Months Access Pass',
                    'duration_term' => '3_MONTHS',
                    'price_pence' => $l['price_3m'],
                    'currency' => 'GBP',
                    'features' => [
                        'Full access to all topical question packs',
                        'Step-by-step worked solutions & mark schemes',
                        'Esther Math Matrix past-paper tracker & performance analytics',
                        'Spaced repetition flashcards with 90% mastery tracking',
                        'Official Cambridge session exam countdown timer',
                    ],
                    'is_active' => true,
                ]
            );

            // 12-Month Plan
            SubscriptionPlan::updateOrCreate(
                ['slug' => $l['slug'] . '-12-months'],
                [
                    'category_id' => $catId,
                    'name' => $l['name'] . ' — 12 Months Annual Pass',
                    'duration_term' => '12_MONTHS',
                    'price_pence' => $l['price_12m'],
                    'currency' => 'GBP',
                    'features' => [
                        'Full 1-year unlimited access to all subjects & syllabuses',
                        'Step-by-step worked solutions & KaTeX mathematical notation',
                        'Esther Math Matrix past-paper tracker & retake history',
                        'Parent Portal integration & real-time guardian oversight',
                        'Exam countdown timer & priority resource request queue',
                    ],
                    'is_active' => true,
                ]
            );
        }
    }
}
