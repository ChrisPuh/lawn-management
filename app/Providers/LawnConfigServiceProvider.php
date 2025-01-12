<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LawnConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/lawn.php',
            'lawn'
        );
    }

    public function register(): void
    {
        // Any additional registration logic
    }
}
