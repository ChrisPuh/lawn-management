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
        // Base path for lawn related files
        'base_path' => env('LAWN_STORAGE_PATH', 'lawns'),

        // Temporary files settings
        'temp' => [
            'path' => env('LAWN_TEMP_PATH', 'private/livewire-tmp'),
            'retention_hours' => env('LAWN_TEMP_RETENTION_HOURS', 24),
            'cleanup_enabled' => env('LAWN_TEMP_CLEANUP_ENABLED', true),
            'disk' => env('LAWN_TEMP_DISK', 'local'),
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
        'max_width' => env('LAWN_IMAGE_MAX_WIDTH', 1200),
        'quality' => env('LAWN_IMAGE_QUALITY', 80),
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'max_file_size' => env('LAWN_IMAGE_MAX_SIZE', 5120), // in KB
    ],
];
