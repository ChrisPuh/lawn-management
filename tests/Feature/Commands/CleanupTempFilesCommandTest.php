<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

// Helper function to create temp files
function createTempFile(string $path, string $content, int $ageInHours): string
{
    File::makeDirectory(dirname($path), 0755, true, true);
    file_put_contents($path, $content);
    touch($path, now()->subHours($ageInHours)->timestamp);

    return $path;
}

// Setup before each test
beforeEach(function (): void {
    // Configure test temp directory
    $tempPath = storage_path('app/testing/temp/cleanup');
    Config::set('lawn.storage.temp.path', 'testing/temp/cleanup');
    Config::set('lawn.storage.temp.cleanup_enabled', true);
    Config::set('lawn.storage.temp.retention_hours', 24);

    // Ensure temp directory exists
    File::makeDirectory($tempPath, 0755, true, true);

    // Store path for use in tests
    $this->tempPath = $tempPath;
});

// Cleanup after each test
afterEach(function (): void {
    // Clean up temp directory
    File::deleteDirectory($this->tempPath);
});

describe('CleanupTempFilesCommand Pest Tests', function (): void {
    test('it requires confirmed force option when cleanup is disabled', function (): void {
        // Disable cleanup
        Config::set('lawn.storage.temp.cleanup_enabled', false);

        // Run command without force
        $this->artisan('app:cleanup-temp-files')
            ->expectsOutput('Temp file cleanup is disabled in configuration.')
            ->expectsOutput('Use --force to run anyway.')
            ->assertExitCode(0);
    });

    test('it cleans up files older than retention period', function (): void {
        // Create test files with different ages
        $oldFile = createTempFile(
            $this->tempPath.'/old_file.txt',
            'old content',
            25
        );
        $recentFile = createTempFile(
            $this->tempPath.'/recent_file.txt',
            'recent content',
            23
        );

        // Run cleanup command
        $this->artisan('app:cleanup-temp-files')
            ->expectsOutput('Cleanup completed:')
            ->expectsOutputToContain('Deleted files: 1')
            ->assertExitCode(0);

        // Assert file states
        expect(file_exists($oldFile))->toBeFalse();
        expect(file_exists($recentFile))->toBeTrue();
    });

    test('it handles empty directory gracefully', function (): void {
        // Ensure temp directory is empty
        File::cleanDirectory($this->tempPath);

        // Run cleanup command
        $this->artisan('app:cleanup-temp-files')
            ->expectsOutput('Cleanup completed:')
            ->expectsOutputToContain('Deleted files: 0')
            ->assertExitCode(0);
    });

    test('it works with different retention periods', function (): void {
        // Set different retention period
        Config::set('lawn.storage.temp.retention_hours', 12);

        // Create files at different ages
        $oldFile = createTempFile(
            $this->tempPath.'/old_file.txt',
            'old content',
            13
        );
        $midFile = createTempFile(
            $this->tempPath.'/mid_file.txt',
            'mid content',
            12
        );
        $recentFile = createTempFile(
            $this->tempPath.'/recent_file.txt',
            'recent content',
            11
        );

        // Run cleanup command
        $this->artisan('app:cleanup-temp-files')
            ->expectsOutput('Cleanup completed:')
            ->expectsOutputToContain('Deleted files: 2')
            ->assertExitCode(0);

        // Assert file states
        expect(file_exists($oldFile))->toBeFalse('Old file should be deleted');
        expect(file_exists($midFile))->toBeFalse('Mid-age file should be deleted');
        expect(file_exists($recentFile))->toBeTrue('Recent file should remain');
    });

    test('it supports force option when cleanup is disabled', function (): void {
        // Disable cleanup
        Config::set('lawn.storage.temp.cleanup_enabled', false);

        // Create an old file
        $oldFile = createTempFile(
            $this->tempPath.'/old_file.txt',
            'old content',
            25
        );

        // Run command with force
        $this->artisan('app:cleanup-temp-files', ['--force' => true])
            ->expectsOutput('Cleanup completed:')
            ->expectsOutputToContain('Deleted files: 1')
            ->assertExitCode(0);

        // Assert file is deleted
        expect(file_exists($oldFile))->toBeFalse();
    });
});
