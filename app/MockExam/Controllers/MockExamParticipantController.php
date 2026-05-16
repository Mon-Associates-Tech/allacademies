<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamConfiguredParticipant;
use App\MockExam\Services\MockExamParticipantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MockExamParticipantController extends Controller
{
    public function __construct(
        private readonly MockExamParticipantService $participantService
    ) {}

    /** Add a single configured participant. */
    public function store(Request $request, MockExam $mockExam): RedirectResponse
    {
        $this->ensureOwner($mockExam);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255'],
            'unique_code' => ['nullable', 'string', 'max:64'],
        ]);

        $this->participantService->registerConfiguredParticipant($mockExam, $data);

        return back()->with('success', "Participant {$data['email']} added.");
    }

    /** Import participants from a CSV file. */
    public function import(Request $request, MockExam $mockExam): RedirectResponse
    {
        $this->ensureOwner($mockExam);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $path   = $request->file('csv_file')->getRealPath();
        $result = $this->participantService->importFromCsv($mockExam, $path);

        return back()->with(
            'success',
            "Import complete: {$result['imported']} added, {$result['skipped']} skipped."
        );
    }

    /** Toggle active flag or remove a configured participant. */
    public function destroy(MockExam $mockExam, MockExamConfiguredParticipant $participant): RedirectResponse
    {
        $this->ensureOwner($mockExam);
        abort_unless($participant->mock_exam_id === $mockExam->id, 404);

        $participant->delete();

        return back()->with('success', 'Participant removed.');
    }

    private function ensureOwner(MockExam $exam): void
    {
        abort_unless($exam->user_id === auth()->id(), 403);
    }
}
