<?php

declare(strict_types=1);

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Console\Commands\CleanupArchivedImages;
use App\Models\Lawn;
use App\Models\LawnImage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command;

beforeEach(function (): void {
    // Configure archive settings for testing
    Config::set('lawn.storage.archive', [
        'enabled' => true,
        'path' => 'archive',
        'retention_months' => 3,
        'disk' => 'public',
    ]);

    // Ensure clean storage
    Storage::fake('public');

    // Create a lawn for testing
    $this->lawn = Lawn::factory()->create();
});

describe('CleanupArchivedImages Command - Basic Functionality', function (): void {
    test('skips cleanup when archiving is disabled', function (): void {
        // Disable archiving
        Config::set('lawn.storage.archive.enabled', false);

        // Run the command
        $this->artisan(CleanupArchivedImages::class)
            ->expectsOutput('Image archiving is disabled. Skipping cleanup.')
            ->assertExitCode(Command::SUCCESS);
    });

    test('handles scenario with no images to cleanup', function (): void {
        // Ensure no archived images exist
        LawnImage::query()->delete();

        // Run the command
        $this->artisan(CleanupArchivedImages::class)
            ->expectsOutput('No images to cleanup.')
            ->assertExitCode(Command::SUCCESS);
    });
});

describe('CleanupArchivedImages Command - Image Deletion', function (): void {
    test('successfully cleans up archived images', function (): void {
        // Create some archived images (some to delete, some to keep)
        $imagesToDelete = LawnImage::factory()
            ->count(5)
            ->archived()
            ->create([
                'lawn_id' => $this->lawn->id,
                'delete_after' => now()->subDay(), // Already past deletion date
                'archived_at' => now()->subDays(2),
            ]);

        $imagesToKeep = LawnImage::factory()
            ->count(3)
            ->archived()
            ->create([
                'lawn_id' => $this->lawn->id,
                'delete_after' => now()->addDays(10), // Not yet ready for deletion
                'archived_at' => now()->subDays(1),
            ]);

        // Ensure the images are stored
        foreach ($imagesToDelete as $image) {
            Storage::disk('public')->put(
                $image->image_path,
                'dummy image content'
            );
        }

        // Run the command
        $this->artisan(CleanupArchivedImages::class)
            ->expectsOutputToContain('Found 5 images to cleanup')
            ->expectsOutputToContain('Cleaned up 5 archived images.')
            ->assertExitCode(Command::SUCCESS);

        // Assert database state
        foreach ($imagesToDelete as $image) {
            $this->assertDatabaseMissing('lawn_images', ['id' => $image->id]);
            Storage::disk('public')->assertMissing($image->image_path);
        }

        // Ensure other images are untouched
        foreach ($imagesToKeep as $image) {
            $this->assertDatabaseHas('lawn_images', ['id' => $image->id]);
            Storage::disk('public')->assertExists($image->image_path);
        }
    });
});

describe('CleanupArchivedImages Command - Error Handling', function (): void {
    test('handles partial failures during cleanup', function (): void {
        // Create some archived images
        $imagesToDelete = LawnImage::factory()
            ->count(5)
            ->archived()
            ->create([
                'lawn_id' => $this->lawn->id,
                'delete_after' => now()->subDay(),
                'archived_at' => now()->subDays(2),
            ]);

        // Create an image that will fail to delete
        $failingImage = $imagesToDelete[0];
        DB::table('lawn_images')
            ->where('id', $failingImage->id)
            ->update(['image_path' => '']); // Set to empty string to simulate failure

        // Capture log messages
        $loggedErrors = [];
        Log::listen(function ($level, $message, $context) use (&$loggedErrors): void {
            if ($level === 'error' && $message === 'Failed to cleanup image') {
                $loggedErrors[] = $context;
            }
        });

        // Run the command
        $this->artisan(CleanupArchivedImages::class)
            ->expectsOutputToContain('Found 5 images to cleanup')
            ->expectsOutputToContain('Failed to cleanup image')
            ->expectsOutputToContain('Cleaned up 4 archived images.')
            ->assertExitCode(Command::FAILURE);

        // Verify error logging
        expect($loggedErrors)->toHaveCount(1);
        expect($loggedErrors[0])->toHaveKey('image_id', $failingImage->id);
    });
})->todo('implement when archive image is working');

describe('CleanupArchivedImages Command - Configuration Options', function (): void {
    test('respects chunk size option', function (): void {
        // Create a large number of images to test chunking
        $images = LawnImage::factory()
            ->count(50)
            ->archived()
            ->create([
                'lawn_id' => $this->lawn->id,
                'delete_after' => now()->subDay(),
                'archived_at' => now()->subDays(2),
            ]);

        // Run the command with custom chunk size
        $this->artisan(CleanupArchivedImages::class, ['--chunk' => 10])
            ->expectsOutputToContain('Found 50 images to cleanup')
            ->expectsOutputToContain('Cleaned up 50 archived images.')
            ->assertExitCode(Command::SUCCESS);

        // Assert all images are cleaned up
        $this->assertDatabaseCount('lawn_images', 0);
    })->todo('implement when archive image is working');

    test('allows disabling progress bar', function (): void {
        // Create some archived images
        LawnImage::factory()
            ->count(5)
            ->archived()
            ->create([
                'lawn_id' => $this->lawn->id,
                'delete_after' => now()->subDay(),
                'archived_at' => now()->subDays(2),
            ]);

        // Run the command with no-progress option
        $this->artisan(CleanupArchivedImages::class, ['--no-progress' => true])
            ->expectsOutputToContain('Found 5 images to cleanup')
            ->expectsOutputToContain('Cleaned up 5 archived images.')
            ->assertExitCode(Command::SUCCESS);
    });
});
