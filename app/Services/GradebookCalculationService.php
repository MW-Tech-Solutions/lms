<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\GradeCategory;
use App\Models\GradeGrade;
use App\Models\GradeItem;
use App\Models\User;
use Illuminate\Support\Collection;

class GradebookCalculationService
{
    /**
     * Compute the weighted final grade percentage for a student in a specific course.
     * Takes into account category weight percentages and drop_lowest_count rules.
     *
     * @return array{
     *     final_percentage: float,
     *     letter_grade: string,
     *     is_passing: bool,
     *     category_breakdown: array<int, array{
     *         name: string,
     *         weight: float,
     *         raw_score: float,
     *         weighted_contribution: float,
     *         dropped_count: int
     *     }>
     * }
     */
    public function calculateCourseGrade(User $user, Course $course): array
    {
        $categories = GradeCategory::with(['items.grades' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])->where('course_id', $course->id)->get();

        $totalWeightedPercentage = 0.0;
        $totalWeightApplied = 0.0;
        $breakdown = [];

        foreach ($categories as $cat) {
            $scores = collect();

            foreach ($cat->items as $item) {
                $grade = $item->grades->first();
                if ($grade !== null && $item->max_score > 0) {
                    $percentage = min(100.0, max(0.0, ($grade->final_score / $item->max_score) * 100.0));
                    $scores->push($percentage);
                }
            }

            $droppedCount = 0;
            if ($cat->drop_lowest_count > 0 && $scores->count() > $cat->drop_lowest_count) {
                $scores = $scores->sort()->values();
                $scores = $scores->slice((int) $cat->drop_lowest_count);
                $droppedCount = (int) $cat->drop_lowest_count;
            }

            $categoryAverage = $scores->isNotEmpty() ? (float) $scores->average() : 0.0;
            $weight = (float) $cat->weight_percentage;
            $weightedContribution = ($categoryAverage * $weight) / 100.0;

            $totalWeightedPercentage += $weightedContribution;
            $totalWeightApplied += $weight;

            $breakdown[$cat->id] = [
                'name' => $cat->name,
                'weight' => $weight,
                'raw_score' => round($categoryAverage, 2),
                'weighted_contribution' => round($weightedContribution, 2),
                'dropped_count' => $droppedCount,
            ];
        }

        // Normalize if total weights don't sum to exactly 100%
        $finalPercentage = $totalWeightApplied > 0
            ? ($totalWeightedPercentage / ($totalWeightApplied / 100.0))
            : $totalWeightedPercentage;

        $finalPercentage = round(min(100.0, max(0.0, $finalPercentage)), 2);
        $letterGrade = $this->determineLetterGrade($finalPercentage);
        $isPassing = $finalPercentage >= (float) $course->passing_grade;

        return [
            'final_percentage' => $finalPercentage,
            'letter_grade' => $letterGrade,
            'is_passing' => $isPassing,
            'category_breakdown' => $breakdown,
        ];
    }

    /**
     * Map numeric score percentage to standard academic letter grade.
     */
    public function determineLetterGrade(float $score): string
    {
        return match (true) {
            $score >= 93.0 => 'A',
            $score >= 90.0 => 'A-',
            $score >= 87.0 => 'B+',
            $score >= 83.0 => 'B',
            $score >= 80.0 => 'B-',
            $score >= 77.0 => 'C+',
            $score >= 73.0 => 'C',
            $score >= 70.0 => 'C-',
            $score >= 60.0 => 'D',
            default => 'F',
        };
    }
}
