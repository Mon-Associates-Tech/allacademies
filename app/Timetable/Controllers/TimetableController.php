<?php
// app/Timetable/Controllers/TimetableController.php

namespace App\Timetable\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class TimetableController extends Controller
{
    public function index(): View
    {
        return view('timetable.index');
    }

    public function rooms(): View
    {
        return view('timetable.rooms');
    }

    public function timeSlots(): View
    {
        return view('timetable.time-slots');
    }
}
