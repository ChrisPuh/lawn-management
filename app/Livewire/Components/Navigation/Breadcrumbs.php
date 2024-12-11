<?php

namespace App\Livewire\Components\Navigation;

use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Breadcrumbs extends Component
{
    public array $segments = [];

    public function mount()
    {
        $this->segments = $this->getSegments();
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

                $segment['label'] = $model?->name ?? $segment['label'];
            }

            return $segment;
        })->toArray();
    }


    public function render()
    {
        return view('livewire.components.navigation.breadcrumbs');
    }
}
