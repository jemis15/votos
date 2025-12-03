<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index() {
        $rooms = auth()->user()->events;

        return view('dashboard', compact('rooms'));
    }
}
