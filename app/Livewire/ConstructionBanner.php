<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

final class ConstructionBanner extends Component
{
    private const COOKIE_NAME = 'construction_banner_hidden';

    private const COOKIE_DURATION = 60 * 24 * 7; // 7 Tage in Minuten

    public bool $show = true;

    public function mount(): void
    {
        // Prüfe zuerst Cookie, dann Session
        $this->show = ! (
            Cookie::has(self::COOKIE_NAME) ||
            session()->has(self::COOKIE_NAME)
        );
    }

    public function hideBanner(): void
    {
        // Setze sowohl Cookie als auch Session
        Cookie::queue(self::COOKIE_NAME, true, self::COOKIE_DURATION);
        session()->put(self::COOKIE_NAME, true);

        $this->show = false;
    }

    public function render(): View
    {
        return view('livewire.construction-banner');
    }
}
