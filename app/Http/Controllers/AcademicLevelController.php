<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicLevelRequest;
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

    public function create()
    {
        return view('academic-levels.create');
    }

    public function store(AcademicLevelRequest $request)
    {
        AcademicLevel::query()->create($request->validated());

        return redirect()->route('academic-levels.index');
    }
}
