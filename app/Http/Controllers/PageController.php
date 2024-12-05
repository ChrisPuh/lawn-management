<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show the welcome page.
     */
    public function welcome(): View
    {
        return view('welcome');
    }

    /**
     * Show the about page.
     */
    public function about(): View
    {
        return view('about');
    }

    /**
     * Show the features page.
     */
    public function features(): View
    {
        return view('features');
    }

    /**
     * Show the privacy policy page.
     */
    public function privacy(): View
    {
        // TODO: Implement privacy page
        abort(404);
    }

    /**
     * Show the terms of service page.
     */
    public function terms(): View
    {
        // TODO: Implement terms page
        abort(404);
    }

    /**
     * Show the contact page.
     */
    public function contact(): View
    {
        // TODO: Implement contact page
        abort(404);
    }
}
