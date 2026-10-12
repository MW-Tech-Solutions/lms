<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quiz>
 */
class QuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4) . ' Assessment',
            'time_limit_minutes' => fake()->randomElement([15, 30, 45, 60]),
            'shuffle_questions' => true,
            'max_attempts' => fake()->randomElement([1, 2, 3]),
            'passing_percentage' => 75,
            'anti_cheat_flags' => [
                'blur_detection' => true,
                'block_copy_paste' => true,
                'webcam_snapshots' => true,
            ],
        ];
    }
}
