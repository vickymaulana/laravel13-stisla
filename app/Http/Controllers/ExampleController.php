<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Stisla template example pages controller.
 *
 * Each method renders a different Stisla component showcase page
 * so developers can preview available UI elements.
 */
class ExampleController extends Controller
{
    /**
     * Display the table example page.
     */
    public function table(): View
    {
        return view('layouts.table-example');
    }

    /**
     * Display the clock example page.
     */
    public function clock(): View
    {
        return view('layouts.clock-example');
    }

    /**
     * Display the chart example page.
     */
    public function chart(): View
    {
        return view('layouts.chart-example');
    }

    /**
     * Display the form example page.
     */
    public function form(): View
    {
        return view('layouts.form-example');
    }

    /**
     * Display the map example page.
     */
    public function map(): View
    {
        return view('layouts.map-example');
    }

    /**
     * Display the calendar example page.
     */
    public function calendar(): View
    {
        return view('layouts.calendar-example');
    }

    /**
     * Display the gallery example page.
     */
    public function gallery(): View
    {
        return view('layouts.gallery-example');
    }

    /**
     * Display the todo example page.
     */
    public function todo(): View
    {
        return view('layouts.todo-example');
    }

    /**
     * Display the contact example page.
     */
    public function contact(): View
    {
        return view('layouts.contact-example');
    }

    /**
     * Display the FAQ example page.
     */
    public function faq(): View
    {
        return view('layouts.faq-example');
    }

    /**
     * Display the news example page.
     */
    public function news(): View
    {
        return view('layouts.news-example');
    }

    /**
     * Display the about example page.
     */
    public function about(): View
    {
        return view('layouts.about-example');
    }
}
