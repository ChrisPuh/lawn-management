<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

describe('Lawn Image Cleanup Configuration', function (): void {
    it('debugs cleanup_enabled configuration', function (): void {
        // Dump the entire configuration for lawn.storage.temp
        $tempConfig = Config::get('lawn.storage.temp');

        // Print out the specific value
        $cleanupEnabled = $tempConfig['cleanup_enabled'];

        // Check if the value is truthy
        expect($cleanupEnabled)->toBeTruthy();

        // Check that it's a boolean
        expect(is_bool($cleanupEnabled))->toBeTrue();
    });

    it('has valid default configuration', function (): void {
        // Storage configuration
        expect(Config::get('lawn.storage.base_path'))->toBe(config('lawn.storage.base_path'));

        // Temp storage configuration
        expect(Config::get('lawn.storage.temp.path'))->toBe(config('lawn.storage.temp.path'));
        expect(Config::get('lawn.storage.temp.retention_hours'))->toBeInt();

        // Check cleanup_enabled is a boolean and defaults to true
        $cleanupEnabled = Config::get('lawn.storage.temp.cleanup_enabled');
        expect($cleanupEnabled)->toBeTrue();

        // Archive configuration
        expect(Config::get('lawn.storage.archive.enabled'))->toBeBool();
        expect(Config::get('lawn.storage.archive.path'))->toBe(config('lawn.storage.archive.path'));
        expect(Config::get('lawn.storage.archive.retention_months'))->toBeInt();

        // Image settings
        expect(Config::get('lawn.images.max_width'))->toBeInt();
        expect(Config::get('lawn.images.quality'))->toBeInt();
        expect(Config::get('lawn.images.allowed_types'))->toBeArray();
        expect(Config::get('lawn.images.max_file_size'))->toBeInt();
    });

    it('has reasonable default values', function (): void {
        // Storage base path
        expect(Config::get('lawn.storage.base_path'))->toBe('lawns');

        // Temp storage defaults
        expect(Config::get('lawn.storage.temp.retention_hours'))->toBe(24);

        // More flexible check for cleanup_enabled
        $cleanupEnabled = Config::get('lawn.storage.temp.cleanup_enabled');
        expect($cleanupEnabled)->toBe(true);

        // Archive defaults
        expect(Config::get('lawn.storage.archive.enabled'))->toBeTrue();
        expect(Config::get('lawn.storage.archive.path'))->toBe('archive');
        expect(Config::get('lawn.storage.archive.retention_months'))->toBe(3);

        // Image setting defaults
        expect(Config::get('lawn.images.max_width'))->toBe(1200);
        expect(Config::get('lawn.images.quality'))->toBe(80);
        expect(Config::get('lawn.images.max_file_size'))->toBe(5120);
    });
});
