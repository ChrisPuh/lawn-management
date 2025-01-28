<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lawn;

use App\Models\Lawn;
use Illuminate\Contracts\View\View;

final class LawnShowController
{
    public function __invoke(Lawn $lawn): View
    {
        return view('lawn.show', [
            'lawn' => $lawn,
            'title' => 'Rasenfläche Details',
        ]);
    }
}
