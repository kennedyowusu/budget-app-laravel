<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'icon' => $this->faker->word,
            'description' => $this->faker->text,
            'is_active' => $this->faker->boolean,
            // 'user_id' => $this->faker->numberBetween(1, 5),
            'user_id' => User::factory(),
        ];
    }
}
