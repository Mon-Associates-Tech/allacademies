<?php

namespace App\Http\Controllers;

use App\Models\AcademicLevel;
use Illuminate\Http\Request;

class AcademicLevelController extends Controller
{
    public function index()
    {
        $academicLevels = AcademicLevel::all();

        return view('academic-levels.index', [
            'academicLevels' => $academicLevels,
        ]);
    }
}
