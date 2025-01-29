<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class LawnCareIcon extends Component
{
    public function __construct(
        public string $path,
        public ?string $class = null,
    ) {}

    public function render(): View
    {
        return view('components.lawn-care-icon');
    }
}
