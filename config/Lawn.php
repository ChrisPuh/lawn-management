<?php

declare(strict_types=1);

// config/lawn.php

return [
    /*
    |--------------------------------------------------------------------------
    | Storage Paths
    |--------------------------------------------------------------------------
    |
    | Define the storage paths for lawn-related files. The base_path defines
    | where all lawn files will be stored within the public storage.
    |
    */
    'storage' => [
        'base_path' => env('LAWN_STORAGE_PATH', 'lawns'),

        'temp' => [
            'path' => env('LAWN_TEMP_PATH', 'private/livewire-tmp'),
            'retention_hours' => (int) env('LAWN_TEMP_RETENTION_HOURS', 24),
            'cleanup_enabled' => (bool) env('LAWN_TEMP_CLEANUP_ENABLED', true),
            'disk' => env('LAWN_TEMP_DISK', 'local'),
        ],

        'archive' => [
            'enabled' => (bool) env('LAWN_ARCHIVE_ENABLED', true),
            'path' => env('LAWN_ARCHIVE_PATH', 'archive'),
            'retention_months' => (int) env('LAWN_ARCHIVE_RETENTION_MONTHS', 3),
            'disk' => env('LAWN_ARCHIVE_DISK', 'public'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Settings
    |--------------------------------------------------------------------------
    |
    | Configure the image processing settings. These settings affect how
    | uploaded images are processed and stored.
    |
    */
    'images' => [
        'max_width' => (int) env('LAWN_IMAGE_MAX_WIDTH', 1200),
        'quality' => (int) env('LAWN_IMAGE_QUALITY', 80),
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'max_file_size' => (int) env('LAWN_IMAGE_MAX_SIZE', 5120), // in KB
    ],
];
