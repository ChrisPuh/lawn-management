<?php

declare(strict_types=1);

use App\Models\Lawn;
use App\Models\LawnImage;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    // Ensure we're in testing environment
    $this->app['env'] = 'testing';
});

describe('Clear Lawn Images Command', function (): void {
    it('can delete all lawn images', function (): void {
        // Create some test lawns and images
        $lawns = Lawn::factory()->count(3)->create();

        foreach ($lawns as $lawn) {
            LawnImage::factory()->count(2)->create([
                'lawn_id' => $lawn->id,
                'imageable_id' => $lawn->id,
                'imageable_type' => Lawn::class,
            ]);
        }

        // Ensure images are created
        expect(LawnImage::count())->toBe(6);

        // Run the command
        $this->artisan('lawn:clear-images', ['--force' => true])
            ->expectsOutput('Cleared 6 out of 6 lawn images.')
            ->assertSuccessful();

        // Check database
        expect(LawnImage::count())->toBe(0);

        // Check filesystem
        $publicDisk = Storage::disk('public');

        // Verify lawns directory is essentially empty
        $allFiles = $publicDisk->allFiles('lawns');

        // Only .gitignore or index.html might remain
        $relevantFiles = array_filter($allFiles, fn ($file): bool => ! in_array(basename($file), ['.gitignore', 'index.html']));

        expect($relevantFiles)->toBeEmpty('Lawns directory should be clean');
    });

    it('prevents running in production', function (): void {
        // Simulate production environment
        $this->app['env'] = 'production';

        $this->artisan('lawn:clear-images')
            ->expectsOutput('This command cannot be run in production!')
            ->assertFailed();
    });
});
