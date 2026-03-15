<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImportExportService;
use Illuminate\Http\Request;

class UserImportController extends Controller
{
    protected ImportExportService $importService;

    public function __construct(ImportExportService $importService)
    {
        $this->importService = $importService;
    }

    public function import(Request $request, string $role)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $schoolId = $request->school_id ?? auth()->user()->school_id;

        $result = $this->importService->performImport(
            $request->file('file'),
            $role,
            ['default_school_id' => $schoolId]
        );

        if ($result['success']) {
            $stats = $result['stats'];
            $message = "Import completed successfully! Imported: {$stats['imported']}, Skipped: {$stats['skipped']}, Errors: {$stats['errors']}";

            if ($stats['errors'] > 0 && ! empty($stats['error_details'])) {
                $errorMessages = collect($stats['error_details'])->take(5)->map(function ($error) {
                    return 'Row '.json_encode($error['row']).': '.$error['error'];
                })->implode('; ');
                $message .= " Details: {$errorMessages}";
            }

            return redirect()->back()->with('message', $message);
        }

        return redirect()->back()->with('error', 'Import failed: '.$result['message']);
    }
}
