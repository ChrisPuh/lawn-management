<?php

declare(strict_types=1);

describe('Lawn Configuration', function () {
    beforeEach(function () {
        // Ensure the configuration is loaded
        if (!config('lawn')) {
            $this->markTestSkipped('Lawn configuration is not loaded');
        }
    });

    test('configuration is loaded', function () {
        expect(config('lawn'))->not()->toBeNull();
    });

    // Storage Configuration Tests
    describe('Storage Configuration', function () {
        test('base path configuration is set correctly', function () {
            $basePath = config('lawn.storage.base_path');

            expect($basePath)
                ->toBeString()
                ->and(strlen($basePath))->toBeGreaterThan(0)
                ->and($basePath)->toBe('lawns');
        });

        describe('Temporary Files Configuration', function () {
            test('temp path is configured', function () {
                $tempPath = config('lawn.storage.temp.path');

                expect($tempPath)
                    ->toBeString()
                    ->and(strlen($tempPath))->toBeGreaterThan(0)
                    ->and($tempPath)->toBe('private/livewire-tmp');
            });

            test('retention hours have valid configuration', function () {
                $retentionHours = config('lawn.storage.temp.retention_hours');

                expect($retentionHours)
                    ->toBeInt()
                    ->and($retentionHours)->toBeGreaterThan(0)
                    ->and($retentionHours)->toBeLessThan(168)
                    ->and($retentionHours)->toBe(24);
            });

            test('cleanup can be enabled or disabled', function () {
                $cleanupEnabled = config('lawn.storage.temp.cleanup_enabled');

                expect($cleanupEnabled)
                    ->toBeBool()
                    ->toBeTrue();
            });

            test('temp storage disk is configured', function () {
                $disk = config('lawn.storage.temp.disk');

                expect($disk)
                    ->toBeString()
                    ->and(in_array($disk, ['local', 'public', 's3']))->toBeTrue()
                    ->and($disk)->toBe('local');
            });
        });
    });

    // Image Configuration Tests
    describe('Image Configuration', function () {
        test('max width is configured correctly', function () {
            $maxWidth = config('lawn.images.max_width');

            expect($maxWidth)
                ->toBeInt()
                ->and($maxWidth)->toBeGreaterThan(0)
                ->and($maxWidth)->toBeLessThan(5000)
                ->and($maxWidth)->toBe(1200);
        });

        test('image quality is within valid range', function () {
            $quality = config('lawn.images.quality');

            expect($quality)
                ->toBeInt()
                ->and($quality)->toBeGreaterThanOrEqual(1)
                ->and($quality)->toBeLessThanOrEqual(100)
                ->and($quality)->toBe(80);
        });

        test('allowed image types are configured', function () {
            $allowedTypes = config('lawn.images.allowed_types');

            expect($allowedTypes)
                ->toBeArray()
                ->and($allowedTypes)->not()->toBeEmpty()
                // Manually check each type is a string
                ->and(array_map(fn($type) => is_string($type), $allowedTypes))->toBe(array_fill(0, count($allowedTypes), true))
                ->and($allowedTypes)->toBe(['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });

        test('max file size is reasonable', function () {
            $maxFileSize = config('lawn.images.max_file_size');

            expect($maxFileSize)
                ->toBeInt()
                ->and($maxFileSize)->toBeGreaterThan(0)
                ->and($maxFileSize)->toBeLessThan(20480)
                ->and($maxFileSize)->toBe(5120);
        });
    });
});
