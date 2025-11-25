<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DocsController extends Controller
{
    /**
     * Show API documentation
     */
    public function index(): View
    {
        return view('docs.index');
    }

    /**
     * Show Quick Start guide
     */
    public function quickStart(): View
    {
        return view('docs.quick-start');
    }

    /**
     * Show Refactoring report
     */
    public function refactoring(): View
    {
        return view('docs.refactoring');
    }
}
