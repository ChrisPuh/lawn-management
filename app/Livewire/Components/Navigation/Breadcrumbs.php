<?php

declare(strict_types=1);

namespace App\Livewire\Components\Navigation;

use Livewire\Component;

final class Breadcrumbs extends Component
{
    public array $segments = [];

    public function mount(): void
    {
        $this->segments = $this->getSegments();
    }

    public function render()
    {
        return view('livewire.components.navigation.breadcrumbs');
    }

    private function getSegments(): array
    {
        $routeName = request()->route()?->getName() ?? request()->query('_route', '');
        $allConfig = config('navigation.breadcrumbs.segments');
        $configSegments = $allConfig[$routeName] ?? [];

        return $this->processSegments($configSegments, request()->route());
    }

    private function processSegments(array $segments, $route = null): array
    {
        return collect($segments)->map(function (array $segment) use ($route): array {
            if (str_contains((string) $segment['label'], ':')) {
                $paramName = str_replace(':', '', $segment['label']);
                $modelName = explode('_', $paramName)[0]; // z.B. 'lawn'

                // Hole das Model-Mapping aus der Config
                $modelClass = config("navigation.breadcrumbs.models.{$modelName}");

                if ($modelClass) {
                    $model = $route?->parameter($modelName) ??
                        $modelClass::find(request()->query($modelName));

                    $segment['label'] = $model->name ?? $segment['label'];
                }
            }

            return $segment;
        })->toArray();
    }
}
