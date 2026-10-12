<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password123'),
            'user_type' => fake()->randomElement(['EMPLOYEE', 'STUDENT']),
            'employee_id' => 'EMP-' . fake()->unique()->numberBetween(10000, 99999),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function corporate(): static
    {
        return $this->state(fn () => [
            'user_type' => 'EMPLOYEE',
            'employee_id' => 'EMP-' . fake()->unique()->numberBetween(10000, 99999),
        ]);
    }

    public function student(): static
    {
        return $this->state(fn () => [
            'user_type' => 'STUDENT',
            'employee_id' => null,
            'department_id' => null,
        ]);
    }
}
