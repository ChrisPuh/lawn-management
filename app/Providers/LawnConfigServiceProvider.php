<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Lawn\ArchiveLawnImage;
use App\Actions\Lawn\DeleteLawnImage;
use App\Actions\Lawn\SaveLawnImage;
use App\Actions\LawnCare\CreateFertilizingAction;
use App\Actions\LawnCare\CreateLawnCareAction;
use App\Actions\LawnCare\CreateMowingAction;
use App\Actions\LawnCare\CreateWateringAction;
use App\Actions\LawnCare\LogLawnCareAction;
use App\Contracts\Lawn\DeleteLawnImageInterface;
use App\Contracts\Lawn\ImageArchiveInterface;
use App\Contracts\Lawn\SaveLawnImageInterface;
use App\Contracts\LawnCare\CreateLawnCareActionContract;
use App\Contracts\LawnCare\LogLawnCareActionContract;
use App\Contracts\Services\LawnCare\LawnCareQueryServiceContract;
use App\Services\LawnCare\LawnCareQueryService;
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
        $this->app->bind(LogLawnCareActionContract::class, LogLawnCareAction::class);
        $this->app->bind(CreateLawnCareActionContract::class, function ($app) {
            return new CreateLawnCareAction(
                $app->make(CreateMowingAction::class),
                $app->make(CreateFertilizingAction::class),
                $app->make(CreateWateringAction::class),
            );
        });
        $this->app->bind(LawnCareQueryServiceContract::class, LawnCareQueryService::class);


        $this->app->bind(ImageArchiveInterface::class, ArchiveLawnImage::class);
        $this->app->bind(SaveLawnImageInterface::class, SaveLawnImage::class);
        $this->app->bind(DeleteLawnImageInterface::class, DeleteLawnImage::class);

    }
}
