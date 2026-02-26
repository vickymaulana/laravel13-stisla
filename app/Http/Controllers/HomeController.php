<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Dashboard and blank page controller.
 */
class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        return view('home');
    }

    /**
     * Show the blank page template.
     */
    public function blank(): View
    {
        return view('layouts.blank-page');
    }
}
