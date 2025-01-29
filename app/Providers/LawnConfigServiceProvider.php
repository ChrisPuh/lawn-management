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
use App\Actions\LawnCare\DeleteLawnCareAction;
use App\Actions\LawnCare\LogLawnCareAction;
use App\Actions\LawnCare\UpdateFertilizingAction;
use App\Actions\LawnCare\UpdateLawnCareAction;
use App\Actions\LawnCare\UpdateMowingAction;
use App\Actions\LawnCare\UpdateWateringAction;
use App\Contracts\Lawn\DeleteLawnImageInterface;
use App\Contracts\Lawn\ImageArchiveInterface;
use App\Contracts\Lawn\SaveLawnImageInterface;
use App\Contracts\LawnCare\CreateLawnCareActionContract;
use App\Contracts\LawnCare\DeleteLawnCareActionContract;
use App\Contracts\LawnCare\LogLawnCareActionContract;
use App\Contracts\LawnCare\UpdateLawnCareActionContract;
use App\Contracts\Services\LawnCare\LawnCareQueryServiceContract;
use App\Livewire\LawnCare\CareDetailsModal;
use App\Livewire\LawnCare\CreateCareModal;
use App\Livewire\LawnCare\DeleteLawnCare;
use App\Services\LawnCare\LawnCareQueryService;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class LawnConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(
            base_path('config/Lawn.php'),
            'lawn'
        );
        Livewire::component('lawn-care.care-details-modal', CareDetailsModal::class);
        Livewire::component('lawn-care.create-care-modal', CreateCareModal::class);
        Livewire::component('lawn-care.delete-lawn-care', DeleteLawnCare::class);

    }

    public function register(): void
    {
        // Logging and Create Actions
        $this->app->bind(LogLawnCareActionContract::class, LogLawnCareAction::class);
        $this->app->bind(CreateLawnCareActionContract::class, function ($app) {
            return new CreateLawnCareAction(
                $app->make(CreateMowingAction::class),
                $app->make(CreateFertilizingAction::class),
                $app->make(CreateWateringAction::class),
            );
        });

        // Update Actions
        $this->app->bind(UpdateLawnCareActionContract::class, function ($app) {
            return new UpdateLawnCareAction(
                $app->make(UpdateMowingAction::class),
                $app->make(UpdateFertilizingAction::class),
                $app->make(UpdateWateringAction::class),
            );
        });

        // Delete Actions
        $this->app->bind(DeleteLawnCareActionContract::class, DeleteLawnCareAction::class);

        // Other bindings
        $this->app->bind(LawnCareQueryServiceContract::class, LawnCareQueryService::class);
        $this->app->bind(ImageArchiveInterface::class, ArchiveLawnImage::class);
        $this->app->bind(SaveLawnImageInterface::class, SaveLawnImage::class);
        $this->app->bind(DeleteLawnImageInterface::class, DeleteLawnImage::class);
    }
}
