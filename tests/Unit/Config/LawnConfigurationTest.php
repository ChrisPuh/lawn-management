<?php

declare(strict_types=1);

uses()
    ->group('lawn-configuration')
    ->beforeEach(function () {
        // Ensure the configuration is loaded
        if (! config('lawn')) {
            $this->markTestSkipped('Lawn configuration is not loaded');
        }
    });

describe('Lawn Configuration Comprehensive Tests', function () {
    describe('Storage Configuration Tests', function () {
        test('base storage path is configured', function () {
            $basePath = config('lawn.storage.base_path');

            expect($basePath)
                ->toBeString()
                ->and(strlen($basePath))->toBeGreaterThan(0)
                ->and($basePath)->toBe('lawns');
        });
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
                ->and($retentionHours)->toBeLessThan(168) // max 1 week
                ->and($retentionHours)->toBe(24);
        });

        test('cleanup can be enabled or disabled', function () {
            $cleanupEnabled = config('lawn.storage.temp.cleanup_enabled');

            expect($cleanupEnabled)
                ->toBeBool()
                ->toBeFalse();
        });

        test('temp storage disk is configured', function () {
            $disk = config('lawn.storage.temp.disk');

            expect($disk)
                ->toBeString()
                ->and(in_array($disk, ['local', 'public', 's3']))->toBeTrue()
                ->and($disk)->toBe('local');
        });
    });

    describe('Archive Configuration Tests', function () {
        test('archive can be enabled or disabled', function () {
            $archiveEnabled = config('lawn.storage.archive.enabled');

            expect($archiveEnabled)
                ->toBeBool()
                ->toBeTrue();
        });

        test('archive path is configured', function () {
            $archivePath = config('lawn.storage.archive.path');

            expect($archivePath)
                ->toBeString()
                ->and(strlen($archivePath))->toBeGreaterThan(0)
                ->and($archivePath)->toBe('archive');
        });

        test('archive retention months have valid configuration', function () {
            $retentionMonths = config('lawn.storage.archive.retention_months');

            expect($retentionMonths)
                ->toBeInt()
                ->and($retentionMonths)->toBeGreaterThan(0)
                ->and($retentionMonths)->toBeLessThanOrEqual(12)
                ->and($retentionMonths)->toBe(3);
        });

        test('archive storage disk is configured', function () {
            $disk = config('lawn.storage.archive.disk');

            expect($disk)
                ->toBeString()
                ->and(in_array($disk, ['local', 'public', 's3']))->toBeTrue()
                ->and($disk)->toBe('local');
        });
    });

    describe('Image Configuration Tests', function () {
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

    describe('Configuration Type Safety', function () {
        test('all configuration keys have expected types', function () {
            $config = config('lawn');

            // Storage configuration type checks
            expect($config['storage']['base_path'])->toBeString();
            expect($config['storage']['temp']['path'])->toBeString();
            expect($config['storage']['temp']['retention_hours'])->toBeInt();
            expect($config['storage']['temp']['cleanup_enabled'])->toBeBool();
            expect($config['storage']['temp']['disk'])->toBeString();

            // Archive configuration type checks
            expect($config['storage']['archive']['enabled'])->toBeBool();
            expect($config['storage']['archive']['path'])->toBeString();
            expect($config['storage']['archive']['retention_months'])->toBeInt();
            expect($config['storage']['archive']['disk'])->toBeString();

            // Image configuration type checks
            expect($config['images']['max_width'])->toBeInt();
            expect($config['images']['quality'])->toBeInt();
            expect($config['images']['allowed_types'])->toBeArray();
            expect($config['images']['max_file_size'])->toBeInt();
        });
    });

    describe('Allowed Image Types Validation', function () {
        test('allowed image types are lowercase', function () {
            $allowedTypes = config('lawn.images.allowed_types');

            $allLowercase = array_reduce($allowedTypes, function ($carry, $type) {
                return $carry && $type === strtolower($type);
            }, true);

            expect($allLowercase)->toBeTrue();
        });
    });

    describe('Configuration Immutability', function () {
        test('configuration remains unchanged after multiple reads', function () {
            $firstRead = config('lawn');
            $secondRead = config('lawn');

            expect($firstRead)->toBe($secondRead);
        });
    });
});
