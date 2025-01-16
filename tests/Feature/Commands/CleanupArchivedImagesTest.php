<?php

declare(strict_types=1);

use App\Console\Commands\CleanupArchivedImages;
use App\Models\LawnImage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

describe('Cleanup Archived Images Command', function () {
    beforeEach(function () {
        Storage::fake('public');
        Config::set('lawn.storage.archive.enabled', true);
    });

    it('skips cleanup when archiving is disabled', function () {
        Config::set('lawn.storage.archive.enabled', false);

        $this->artisan('lawn:cleanup-archived-images')
            ->expectsOutput('Image archiving is disabled. Skipping cleanup.')
            ->assertExitCode(0);
    });

    it('handles no images to cleanup', function () {
        $this->artisan('lawn:cleanup-archived-images')
            ->expectsOutput('No images to cleanup.')
            ->assertExitCode(0);
    });

    it('cleans up archived images', function () {
        // Create some archived images past their delete_after date
        $imagesToDelete = LawnImage::factory()->count(5)->create([
            'archived_at' => now()->subMonths(4),
            'delete_after' => now()->subMonth(),
            'image_path' => 'test/image.jpg'
        ]);

        // Create some images that should not be deleted
        LawnImage::factory()->count(3)->create([
            'archived_at' => null,
            'delete_after' => null
        ]);

        // Simulate file existence
        Storage::disk('public')->put('test/image.jpg', 'dummy content');

        $this->artisan('lawn:cleanup-archived-images')
            ->expectsOutputToContain('Found 5 images to cleanup')
            ->expectsOutputToContain('Cleaned up 5 archived images.')
            ->assertExitCode(0);

        // Assert images are deleted
        foreach ($imagesToDelete as $image) {
            $this->assertDatabaseMissing('lawn_images', ['id' => $image->id]);
            Storage::disk('public')->assertMissing('test/image.jpg');
        }
    });

    it('handles partial failures during cleanup', function () {
        // Create a valid archived image
        $validImage = LawnImage::factory()->archived()->create();

        // Create an archived image without a valid file path
        $invalidImage = LawnImage::factory()->archived()->create([
            'image_path' => null
        ]);

        $this->artisan('lawn:cleanup-archived-images')
            ->expectsOutputToContain('Found 2 images to cleanup')
            ->expectsOutputToContain('1 images failed to cleanup.')
            ->assertExitCode(1);

        // Assert valid image is deleted
        $this->assertDatabaseMissing('lawn_images', ['id' => $validImage->id]);

        // Assert invalid image remains
        $this->assertDatabaseHas('lawn_images', ['id' => $invalidImage->id]);
    });
});
