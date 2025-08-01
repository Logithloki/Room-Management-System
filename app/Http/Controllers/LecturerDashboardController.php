<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LecturerDashboardController extends Controller
{
    /**
     * Show the lecturer dashboard.
     */
    public function index(): View
    {
        return view('lecturer.dashboard');
    }
}
