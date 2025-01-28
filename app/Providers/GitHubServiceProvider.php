<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Services\IssueTrackerInterface;
use App\Services\GitHub\GitHubIssueService;
use Illuminate\Support\ServiceProvider;

final class GitHubServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(IssueTrackerInterface::class, function ($app) {
            return new GitHubIssueService(
                token: config('services.github.token'),
                owner: config('services.github.owner'),
                repo: config('services.github.repo')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
