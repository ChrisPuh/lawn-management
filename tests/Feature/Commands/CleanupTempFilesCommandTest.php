<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use App\Console\Commands\CleanupTempFilesCommand;

// Helper function to create test image
function createTestImage(): string
{
    $stubsDir = __DIR__.'/../../Stubs/Commands';
    if (! is_dir($stubsDir)) {
        mkdir($stubsDir, 0777, true);
    }

    $testImage = $stubsDir.'/cleanup-test-image.jpg';

    // Create a small test image
    $image = imagecreatetruecolor(100, 100);
    $bgColor = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $bgColor);

    // Add some text
    $textColor = imagecolorallocate($image, 0, 0, 0);
    imagestring($image, 5, 10, 40, 'Test Image', $textColor);

    // Save as JPG
    imagejpeg($image, $testImage, 90);
    imagedestroy($image);

    return $testImage;
}

beforeEach(function () {
    // Ensure we're using a test temp directory
    config(['lawn.storage.temp.path' => 'testing/temp/cleanup']);
    config(['lawn.storage.temp.cleanup_enabled' => true]);

    // Create test directory
    $tempPath = storage_path('app/testing/temp/cleanup');
    if (! is_dir($tempPath)) {
        mkdir($tempPath, 0777, true);
    }

    // Create test image if needed
    $this->testImagePath = createTestImage();
});

afterEach(function () {
    $tempPath = storage_path('app/testing/temp/cleanup');
    if (is_dir($tempPath)) {
        array_map('unlink', glob("$tempPath/*.*"));
        rmdir($tempPath);
        @rmdir(dirname($tempPath)); // Remove parent if empty
    }
});

describe('CleanupTempFilesCommand', function () {

    beforeEach(function () {
        $this->app->singleton('CleanupTempFilesCommand', function ($app) {
            return new CleanupTempFilesCommand;
        });
    });

    test('command can be instantiated', function () {
        $command = $this->app->make('CleanupTempFilesCommand');
        expect($command)->toBeInstanceOf(CleanupTempFilesCommand::class);
    });

    describe('cleanup functionality', function () {
        beforeEach(function () {
            config(['lawn.storage.temp.cleanup_enabled' => true]);
        });

        test('it deletes old files', function () {
            $tempPath = storage_path('app/testing/temp/cleanup');

            // Old file (2 days ago)
            $oldFile = $tempPath.'/old_test.jpg';
            copy($this->testImagePath, $oldFile);
            touch($oldFile, time() - (48 * 3600));

            // Recent file (1 hour ago)
            $recentFile = $tempPath.'/recent_test.jpg';
            copy($this->testImagePath, $recentFile);
            touch($recentFile, time() - (1 * 3600));

            // Run command
            $this->artisan(CleanupTempFilesCommand::class)
                ->expectsOutput('Cleanup completed:')
                ->expectsOutput(' - Deleted files: 1')
                ->expectsOutput(' - Retention period: 1 days')
                ->assertSuccessful();

            // Ensure files system catches up
            clearstatcache();

            // Assert file states
            expect(file_exists($oldFile))->toBeFalse()
                ->and(file_exists($recentFile))->toBeTrue();
        });

        test('it respects cleanup_enabled config', function () {
            $tempPath = storage_path('app/testing/temp/cleanup');
            $testFile = $tempPath.'/old_test.jpg';
            copy($this->testImagePath, $testFile);
            touch($testFile, time() - (48 * 3600));

            // Disable cleanup
            config(['lawn.storage.temp.cleanup_enabled' => false]);

            // Run command without force
            $this->artisan(CleanupTempFilesCommand::class)
                ->expectsOutput('Temp file cleanup is disabled in configuration.')
                ->expectsOutput('Use --force to run anyway.')
                ->assertSuccessful();

            clearstatcache();
            expect(file_exists($testFile))->toBeTrue();

            // Run command with force
            $this->artisan(CleanupTempFilesCommand::class, ['--force' => true])
                ->expectsOutput('Cleanup completed:')
                ->assertSuccessful();

            clearstatcache();
            expect(file_exists($testFile))->toBeFalse();
        });

        test('it handles non-existent directory gracefully', function () {
            config(['lawn.storage.temp.path' => 'non/existent/path']);

            $this->artisan(CleanupTempFilesCommand::class)
                ->expectsOutput('No temp directory found at: '.storage_path('app/non/existent/path'))
                ->assertSuccessful();
        });

        test('it respects retention period from config', function () {
            // Set retention to 12 hours
            config(['lawn.storage.temp.retention_hours' => 12]);

            $tempPath = storage_path('app/testing/temp/cleanup');

            // Create file from 13 hours ago
            $oldFile = $tempPath.'/old_test.jpg';
            copy($this->testImagePath, $oldFile);
            touch($oldFile, time() - (13 * 3600));

            // Create file from 11 hours ago
            $recentFile = $tempPath.'/recent_test.jpg';
            copy($this->testImagePath, $recentFile);
            touch($recentFile, time() - (11 * 3600));

            $this->artisan(CleanupTempFilesCommand::class)
                ->expectsOutput('Cleanup completed:')
                ->expectsOutput(' - Deleted files: 1')
                ->expectsOutput(' - Retention period: 12 hours')
                ->assertSuccessful();

            clearstatcache();
            expect(file_exists($oldFile))->toBeFalse()
                ->and(file_exists($recentFile))->toBeTrue();
        });
    });
});
