<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LawnCare;
use App\Models\LawnCareLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LawnCareLog>
 */
final class LawnCareLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lawn_care_id' => LawnCare::factory(),
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(['created', 'updated', 'completed']),
            'data' => [
                'type' => $this->faker->randomElement(['mow', 'fertilize', 'water']),
                'care_data' => [
                    'some_field' => $this->faker->word(),
                ],
            ],
        ];
    }

    public function created(): self
    {
        return $this->state([
            'action' => 'created',
        ]);
    }

    public function updated(array $changes = []): self
    {
        return $this->state([
            'action' => 'updated',
            'data' => [
                'changes' => $changes ?: [
                    'field' => [
                        'old' => $this->faker->word(),
                        'new' => $this->faker->word(),
                    ],
                ],
            ],
        ]);
    }

    public function completed(): self
    {
        return $this->state([
            'action' => 'completed',
            'data' => [
                'completed_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    public function forLawnCare(LawnCare $lawnCare): self
    {
        return $this->state([
            'lawn_care_id' => $lawnCare->id,
            'data' => [
                'type' => $lawnCare->type->value,
                'care_data' => $lawnCare->care_data,
            ],
        ]);
    }
}
