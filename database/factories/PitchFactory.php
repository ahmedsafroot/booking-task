<?php

namespace Database\Factories;

use App\Models\Stadium;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pitch>
 */
class PitchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stadium_id' => Stadium::factory(),
            'name' => 'Pitch ' . fake()->unique()->word(),
        ];
    }
}
