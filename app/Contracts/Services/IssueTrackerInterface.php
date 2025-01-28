<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use Illuminate\Http\Client\Response;

interface IssueTrackerInterface
{
    /**
     * Create a new issue
     *
     * @param string $title The issue title
     * @param string $body The issue description/body
     * @param array<string> $labels Labels to be applied to the issue
     * @param array<string> $assignees Users to be assigned to the issue
     * @return Response
     */
    public function createIssue(
        string $title,
        string $body,
        array  $labels = [],
        array  $assignees = [],
    ): Response;
}
