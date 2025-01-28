<?php

declare(strict_types=1);

namespace App\Services\GitHub;

use App\Contracts\Services\IssueTrackerInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use RuntimeException;

final readonly class GitHubIssueService implements IssueTrackerInterface
{
    public function __construct(
        private string $token = '',
        private string $owner = '',
        private string $repo = '',
    ) {}

    public function createIssue(
        string $title,
        string $body,
        array $labels = [],
        array $assignees = [],
    ): Response {
        $formattedBody = $this->formatIssueBody($body, in_array('bug', $labels) ? 'bug' : 'feature');

        $url = "https://api.github.com/repos/{$this->getOwner()}/{$this->getRepo()}/issues";

        Log::debug('GitHub API Request', [
            'url' => $url,
            'owner' => $this->getOwner(),
            'repo' => $this->getRepo(),
        ]);

        $response = Http::withToken($this->getToken())
            ->withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->post($url, [
                'title' => $title,
                'body' => $formattedBody,
                'labels' => $labels,
                'assignees' => $assignees,
            ]);

        if (! $response->successful()) {
            Log::error('GitHub API Error', [
                'url' => $url,
                'status' => $response->status(),
                'error' => $response->body(),
            ]);

            throw new RuntimeException('Failed to create GitHub issue: '.$response->body());
        }

        return $response;
    }

    private function getToken(): string
    {
        return $this->token ?: config('services.github.token');
    }

    private function getOwner(): string
    {
        return trim($this->owner ?: config('services.github.owner'));
    }

    private function getRepo(): string
    {
        // Remove any owner prefix from repo name if present
        $repo = trim($this->repo ?: config('services.github.repo'));
        if (str_contains($repo, '/')) {
            $repo = explode('/', $repo)[1];
        }

        return $repo;
    }

    private function formatIssueBody(string $description, string $type): string
    {
        $userAgent = Request::userAgent() ?? 'Unknown';

        if ($type === 'bug') {
            return <<<MD
            **Beschreibung**
            {$description}

            **Reproduktion**
            1. Problem wurde über das Feedback-Formular gemeldet

            **System**
            - Browser: {$userAgent}

            **Zusätzlicher Kontext**
            Gemeldet über das Feedback-Formular
            MD;
        }

        return <<<MD
        **Problem**
        {$description}

        **Gewünschte Lösung**
        _Gemeldet über das Feedback-Formular - Details folgen_

        **Zusätzlicher Kontext**
        Gemeldet über das Feedback-Formular
        MD;
    }
}
