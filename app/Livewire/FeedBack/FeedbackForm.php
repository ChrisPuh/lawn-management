<?php

declare(strict_types=1);

namespace App\Livewire\FeedBack;

use App\Contracts\Services\IssueTrackerInterface;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

final class FeedbackForm extends Component
{
    public string $type = 'bug';

    public string $title = '';

    public string $description = '';

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:bug,feature,improvement'],
            'title' => ['required', 'string', 'min:5', 'max:100'],
            'description' => ['required', 'string', 'min:20', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Bitte geben Sie einen Titel an.',
            'title.min' => 'Der Titel muss mindestens 5 Zeichen lang sein.',
            'description.required' => 'Bitte beschreiben Sie das Problem oder Ihre Idee.',
            'description.min' => 'Die Beschreibung muss mindestens 20 Zeichen lang sein.',
        ];
    }

    public function submit(IssueTrackerInterface $issueTracker): void
    {
        $validated = $this->validate();

        try {
            $labels = match ($validated['type']) {
                'bug' => ['bug', 'user-reported'],
                'feature' => ['enhancement', 'user-requested'],
                'improvement' => ['improvement', 'user-requested'],
                default => ['user-reported'],
            };

            $issueTracker->createIssue(
                title: $validated['title'],
                body: $this->formatDescription($validated['description']),
                labels: $labels,
            );

            $this->reset(['title', 'description']);

            session()->flash('feedback-success', 'Vielen Dank für Ihr Feedback! Wir werden uns darum kümmern.');

        } catch (Exception $e) {
            Log::error('Failed to create issue', [
                'error' => $e->getMessage(),
                'feedback' => $validated,
            ]);

            session()->flash('feedback-error', 'Es gab ein Problem beim Speichern Ihres Feedbacks. Bitte versuchen Sie es später erneut.');
        }
    }

    public function render(): View
    {
        return view('livewire.feed-back.feedback-form');
    }

    private function formatDescription(string $description): string
    {
        $userEmail = auth()->user()?->email ?? 'Anonymous';

        return implode("\n\n", [
            '## User Feedback',
            $description,
            '---',
            'Submitted by: '.$userEmail,
        ]);
    }
}
