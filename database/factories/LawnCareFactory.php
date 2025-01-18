<?php

declare(strict_types=1);

namespace Database\Factories;

use App\DataObjects\LawnCare\FertilizingData;
use App\DataObjects\LawnCare\MowingData;
use App\DataObjects\LawnCare\WateringData;
use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LawnCare>
 */
final class LawnCareFactory extends Factory
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
            'created_by_id' => User::factory(),
            'type' => $this->faker->randomElement(LawnCareType::cases()),
            'notes' => $this->faker->optional()->paragraph(),
            'performed_at' => $this->faker->dateTimeBetween('-1 month'),
            'scheduled_for' => null,
            'completed_at' => null,
        ];
    }

    public function scheduled(): self
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_for' => $this->faker->dateTimeBetween('now', '+2 weeks'),
            'performed_at' => null,
            'completed_at' => null,
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'performed_at' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            'completed_at' => $this->faker->dateTimeBetween('-1 day'),
            'scheduled_for' => null,
        ]);
    }

    public function mowing(?float $height = null): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => LawnCareType::MOW,
            'performed_at' => $this->faker->dateTimeBetween('-1 month'),
            'care_data' => new MowingData(
                height_mm: $height ?? $this->faker->randomFloat(1, 25, 60),
                pattern: $this->faker->randomElement(MowingPattern::cases()),
                collected: $this->faker->boolean(),
                blade_condition: $this->faker->randomElement(BladeCondition::cases()),
                duration_minutes: $this->faker->numberBetween(20, 120),
            ),
        ]);
    }

    public function fertilizing(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => LawnCareType::FERTILIZE,
            'performed_at' => $this->faker->dateTimeBetween('-1 month'),
            'care_data' => new FertilizingData(
                product_name: $this->faker->randomElement(['Premium Rasendünger', 'Organic Choice', 'Spring Boost']),
                amount_per_sqm: $this->faker->randomFloat(1, 20, 50),
                nutrients: [
                    'nutrient_n' => $this->faker->randomFloat(1, 10, 30),
                    'nutrient_p' => $this->faker->randomFloat(1, 5, 15),
                    'nutrient_k' => $this->faker->randomFloat(1, 5, 20),
                ],
                watered: $this->faker->boolean(),
                temperature_celsius: $this->faker->optional()->randomFloat(1, 10, 30),
                weather_condition: $this->faker->optional()->randomElement(WeatherCondition::cases()),
            ),
        ]);
    }

    public function watering(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => LawnCareType::WATER,
            'performed_at' => $this->faker->dateTimeBetween('-1 month'),
            'care_data' => new WateringData(
                amount_liters: $this->faker->randomFloat(1, 100, 1000),
                duration_minutes: $this->faker->numberBetween(15, 120),
                method: $this->faker->randomElement(WateringMethod::cases()),
                temperature_celsius: $this->faker->optional()->randomFloat(1, 15, 35),
                weather_condition: $this->faker->optional()->randomElement(WeatherCondition::cases()),
                time_of_day: $this->faker->optional()->randomElement(TimeOfDay::cases()),
            ),
        ]);
    }
}
