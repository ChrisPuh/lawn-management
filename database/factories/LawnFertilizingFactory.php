<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Lawn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LawnFertilizing>
 */
final class LawnFertilizingFactory extends Factory
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
            'fertilized_on' => fake()->dateTimeBetween('-3 months', 'now'),
            'fertilizer_name' => fake()->randomElement([
                'NPK 15-15-15',
                'Organic Spring',
                'Iron Plus',
                'Summer Special',
                'Autumn Care',
                'Winter Guard',
            ]),
            'quantity' => fake()->randomFloat(2, 1, 5),
            'quantity_unit' => 'kg',
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }
}
