<?php

declare(strict_types=1);

namespace App\Livewire\Components\Navigation;

use Livewire\Component;

final class Breadcrumbs extends Component
{
    public array $segments = [];

    public function mount()
    {
        $this->segments = $this->getSegments();
    }

    public function render()
    {
        return view('livewire.components.navigation.breadcrumbs');
    }

    protected function getSegments(): array
    {
        $routeName = request()->route()?->getName() ?? request()->query('_route', '');
        $allConfig = config('navigation.breadcrumbs.segments');
        $configSegments = $allConfig[$routeName] ?? [];

        return $this->processSegments($configSegments, request()->route());
    }

    protected function processSegments(array $segments, $route = null): array
    {
        return collect($segments)->map(function ($segment) use ($route) {
            if (str_contains($segment['label'], ':')) {
                $paramName = str_replace(':', '', $segment['label']);
                $modelName = explode('_', $paramName)[0];

                $model = $route?->parameter($modelName) ??
                    app($modelName)->find(request()->query($modelName));

                $segment['label'] = $model && property_exists($model, 'name') ? $model->name : $segment['label'];
            }

            return $segment;
        })->toArray();
    }
}
