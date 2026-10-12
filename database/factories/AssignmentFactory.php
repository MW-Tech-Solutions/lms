<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Assignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3) . ' Case Study',
            'instructions' => fake()->paragraphs(2, true),
            'max_file_size_mb' => 20,
            'allowed_mime_types' => 'pdf,docx,zip',
            'due_date' => now()->addWeeks(2),
            'max_points' => 100,
            'rubric_json' => [
                'criteria' => [
                    ['title' => 'Technical Rigor', 'points' => 50],
                    ['title' => 'Compliance Documentation', 'points' => 50],
                ]
            ],
        ];
    }
}
