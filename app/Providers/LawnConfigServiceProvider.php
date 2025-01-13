<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class LawnConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/lawn.php',
            'lawn'
        );
    }

    public function register(): void
    {
        // Any additional registration logic
    }
}
