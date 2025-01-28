<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

final class PageController extends Controller
{
    public function welcome(): View
    {
        return view('landing.welcome');
    }

    public function about(): View
    {
        return view('landing.about');
    }

    public function features(): View
    {
        return view('landing.features');
    }

    public function privacy(): View
    {
        return view('landing.privacy');
    }

    public function terms(): View
    {
        return view('landing.terms');
    }

    public function contact(): View
    {
        return view('landing.contact');
    }

    public function cookiePolicy(): View
    {
        return view('cookie-policy', [
            'title' => 'Cookie-Richtlinie',
        ]);
    }
}
