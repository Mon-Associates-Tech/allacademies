<?php

namespace App\Http\Controllers\Center;

use App\ExaminationHub\Services\BankExportService;
use App\ExaminationHub\Services\BankImportService;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use Illuminate\Http\Request;

/**
 * BankExportImportController — export and import academic structure and question bank.
 *
 * Only available on central instances (APP_ROLE=central).
 */
class BankExportImportController
{
    /**
     * Show the bank export/import page.
     */
    public function index(Request $request)
    {
        $groups = AcademicGroup::orderBy('name')->get();
        $levels = AcademicLevel::orderBy('name')->get();
        $subjects = AcademicSubject::orderBy('name')->get();

        return view('center-sync.bank.index', compact('groups', 'levels', 'subjects'));
    }

    /**
     * Export selected academic structure and question bank.
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:group,level,subject',
            'id' => 'required_if:type,group,level,subject|integer|exists:' . $this->getTableForType($request->type) . ',id',
        ]);

        $exportService = app(BankExportService::class);

        try {
            $bundle = $exportService->export($validated['type'], $validated['id']);

            $filename = 'bank-export-' . $validated['type'] . '-' . $validated['id'] . '.json';

            return response()
                ->download($bundle, $filename)
                ->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Import an academic structure and question bank bundle.
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:json|max:10240',
        ]);

        $importService = app(BankImportService::class);

        try {
            $file = $request->file('file');
            $content = file_get_contents($file->getRealPath());

            $result = $importService->import($content);

            $message = 'Import successful. ';
            $message .= $result['groups_imported'] . ' groups, ';
            $message .= $result['levels_imported'] . ' levels, ';
            $message .= $result['subjects_imported'] . ' subjects, ';
            $message .= $result['questions_imported'] . ' questions imported.';

            return redirect()
                ->back()
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the table name for the given type.
     */
    private function getTableForType(string $type): string
    {
        return match ($type) {
            'group' => 'academic_groups',
            'level' => 'academic_levels',
            'subject' => 'academic_subjects',
        };
    }
}
