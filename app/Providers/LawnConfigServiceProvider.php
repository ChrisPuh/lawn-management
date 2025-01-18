<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Lawn\ArchiveLawnImage;
use App\Actions\Lawn\DeleteLawnImage;
use App\Actions\Lawn\SaveLawnImage;
use App\Contracts\Lawn\DeleteLawnImageInterface;
use App\Contracts\Lawn\ImageArchiveInterface;
use App\Contracts\Lawn\SaveLawnImageInterface;
use Illuminate\Support\ServiceProvider;

final class LawnConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(
            base_path('config/Lawn.php'),
            'lawn'
        );
    }

    public function register(): void
    {
        $this->app->bind(ImageArchiveInterface::class, ArchiveLawnImage::class);
        $this->app->bind(SaveLawnImageInterface::class, SaveLawnImage::class);
        $this->app->bind(DeleteLawnImageInterface::class, DeleteLawnImage::class);

    }
}
