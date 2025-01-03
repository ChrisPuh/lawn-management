<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Lawn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LawnAerating>
 */
final class LawnAeratingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lawn_id' => Lawn::factory(),
            'aerated_on' => fake()->dateTimeBetween('-3 months', 'now'),
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }
}
