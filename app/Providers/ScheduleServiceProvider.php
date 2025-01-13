<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

final class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->booted(function (): void {
            $schedule = $this->app->make(Schedule::class);

            // Optional: add logging
            $schedule->command('app:cleanup-temp-files')
                ->daily()
                ->appendOutputTo(storage_path('logs/temp-cleanup.log'));
        });
    }
}
