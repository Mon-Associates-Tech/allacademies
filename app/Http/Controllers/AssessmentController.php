<?php

namespace App\Http\Controllers;

use App\Exports\AssessmentResultExport;
use App\Models\Assessment;
use App\Models\AssessmentResponse;
use Barryvdh\DomPDF\PDF;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Resources\AssessmentResource;
use App\Http\Resources\AssessmentCollection;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Exception;

class AssessmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Assessment::class, 'assessment');
    }

    public function index(Request $request): AssessmentCollection
    {
        /* @var Assessment|Builder $query; */
        $query = Assessment::with('student', 'book');

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        if ($request->has('min_score')) {
            $query->where('score', '>=', $request->min_score);
        }

        if ($request->has('max_score')) {
            $query->where('score', '<=', $request->max_score);
        }

        return new AssessmentCollection($query->paginate());
    }

    public function store(Request $request): AssessmentResource
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'score' => 'required|numeric|min:0|max:100',
            'comments' => 'nullable|string',
        ]);

        $assessment = Assessment::create($validated);

        return new AssessmentResource($assessment->load('student', 'book'));
    }

    public function show(Assessment $assessment)
    {
        return new AssessmentResource($assessment->load('student', 'book'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'score' => 'sometimes|required|numeric|min:0|max:100',
            'comments' => 'nullable|string',
        ]);

        $assessment->update($validated);

        return new AssessmentResource($assessment->load('student', 'book'));
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();

        return response()->noContent();
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export($id)
    {
        $response = AssessmentResponse::where('assessment_id', $id)->firstOrFail();

        $data = collect($response->data['questions']);

        if (request('format') === 'csv') {
            return Excel::download(new AssessmentResultExport($data), "assessment_{$id}.csv");
        }

        $pdf = Pdf::loadView('exports.assessment-result-pdf', [
            'questions' => $data,
            'total' => $response->data['total_score'],
            'max' => $response->data['max_score'],
            'percent' => $response->data['percentage_score']
        ]);

        return $pdf->download("assessment_{$id}.pdf");
    }
}
