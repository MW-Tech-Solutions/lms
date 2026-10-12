<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->catchPhrase();
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->randomNumber(4),
            'target_audience' => fake()->randomElement(['CORPORATE', 'PUBLIC', 'BOTH']),
            'format' => fake()->randomElement(['SELF_PACED', 'WEEKLY', 'TOPICS']),
            'passing_grade' => fake()->randomElement([70, 75, 80, 85]),
            'recertification_days' => fake()->randomElement([null, 180, 365]),
            'price_cents' => fake()->randomElement([0, 2900, 4900, 9900]),
            'currency' => 'USD',
            'is_published' => true,
        ];
    }

    public function corporate(): static
    {
        return $this->state(fn () => [
            'target_audience' => 'CORPORATE',
            'recertification_days' => 365,
            'price_cents' => 0,
        ]);
    }

    public function publicPaid(): static
    {
        return $this->state(fn () => [
            'target_audience' => 'PUBLIC',
            'recertification_days' => null,
            'price_cents' => 4900,
        ]);
    }
}
