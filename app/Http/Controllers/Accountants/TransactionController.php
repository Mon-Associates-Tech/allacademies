<?php

namespace App\Http\Controllers\Accountants;

use App\Http\Controllers\Controller;
use App\Models\SchoolPayment;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = getSchoolId();

        $query = SchoolPayment::where('school_id', $schoolId)
            ->with(['student', 'payer', 'academicGroup', 'academicLevel']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'like', '%'.$request->search.'%')
                    ->orWhereHas('student', function ($sq) use ($request) {
                        $sq->where('name', 'like', '%'.$request->search.'%');
                    });
            });
        }

        $transactions = $query->latest()->paginate(20);

        return view('accountant.transactions.index', compact('transactions'));
    }

    public function show(SchoolPayment $payment)
    {
        $payment->load(['student', 'payer', 'academicGroup', 'academicLevel', 'academicYear', 'academicPeriod']);

        return view('accountant.transactions.show', compact('payment'));
    }

    public function export(Request $request)
    {
        $schoolId = getSchoolId();

        $query = SchoolPayment::where('school_id', $schoolId)
            ->with(['student', 'payer', 'academicGroup', 'academicLevel']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->get();

        // For now, return success message
        // In production, you would generate actual export file
        return back()->with('success', 'Export functionality coming soon');
    }
}
