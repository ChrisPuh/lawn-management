<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LawnImageType;
use App\Models\Lawn;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class LawnImageFactory extends Factory
{
    public function definition(): array
    {
        $lawn = Lawn::factory()->create();
        $imageableTypes = [
            Lawn::class,
        ];
        $imageableType = fake()->randomElement($imageableTypes);

        // Generate a unique filename using the actual lawn ID
        $filename = sprintf(
            'lawns/%d/images/test_%s.jpg',
            $lawn->id,
            uniqid()
        );

        // Ensure directory exists (for testing)
        Storage::fake('public');
        Storage::disk('public')->makeDirectory(dirname($filename));

        // Store a fake image file
        Storage::disk('public')->put(
            $filename,
            UploadedFile::fake()->image('test.jpg')->getContent()
        );

        return [
            'lawn_id' => $lawn->id,
            'image_path' => $filename,
            'imageable_type' => $imageableType,
            'imageable_id' => $lawn->id,  // Use the same lawn as imageable
            'type' => fake()->randomElement(LawnImageType::cases()),
            'description' => null,
            'archived_at' => null,
            'delete_after' => null,
        ];
    }

    public function forLawn(Lawn $lawn): static
    {
        return $this->state(fn(array $attributes) => [
            'lawn_id' => $lawn->id,
            'imageable_type' => Lawn::class,
            'imageable_id' => $lawn->id,
            'image_path' => sprintf(
                'lawns/%d/images/test_%s.jpg',
                $lawn->id,
                uniqid()
            ),
        ]);
    }

    public function before(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => LawnImageType::BEFORE,
        ]);
    }

    public function after(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => LawnImageType::AFTER,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'archived_at' => now()->subMonths(4),
            'delete_after' => now()->subMonth(),
        ]);
    }

    public function withoutPath(): static
    {
        return $this->state(fn(array $attributes) => [
            'image_path' => null,
        ]);
    }
}
