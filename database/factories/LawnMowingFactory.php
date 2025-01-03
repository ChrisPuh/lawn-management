<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LawnMowing>
 */
final class LawnMowingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lawn_id' => \App\Models\Lawn::factory(),
            'mowed_on' => $this->faker->date(),
            'cutting_height' => $this->faker->randomElement(['3 cm', '4 cm', '5 cm']),
        ];
    }
}
