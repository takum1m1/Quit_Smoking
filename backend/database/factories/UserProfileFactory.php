<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'display_name' => $this->faker->firstName(),
            'daily_cigarettes' => $this->faker->numberBetween(1, 60),
            'pack_cost' => $this->faker->numberBetween(400, 3000),
            'quit_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
