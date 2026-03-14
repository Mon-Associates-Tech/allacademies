<?php

namespace App\Http\Controllers\Accountants;

use App\Http\Controllers\Controller;
use App\Models\SchoolPayment;

class AccountantDashboardController extends Controller
{
    public function index()
    {
        $schoolId = getSchoolId();

        $stats = [
            'total_payments' => SchoolPayment::where('school_id', $schoolId)->count(),
            'total_amount' => SchoolPayment::where('school_id', $schoolId)->where('status', 'succeeded')->sum('amount'),
            'pending_payments' => SchoolPayment::where('school_id', $schoolId)->where('status', 'pending')->count(),
            'this_month' => SchoolPayment::where('school_id', $schoolId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', 'succeeded')
                ->sum('amount'),
            'failed_payments' => SchoolPayment::where('school_id', $schoolId)->where('status', 'failed')->count(),
            'today_amount' => SchoolPayment::where('school_id', $schoolId)
                ->whereDate('created_at', today())
                ->where('status', 'succeeded')
                ->sum('amount'),
        ];

        $recentPayments = SchoolPayment::where('school_id', $schoolId)
            ->with(['student.user', 'payer'])
            ->latest()
            ->take(10)
            ->get();

        return view('accountants.dashboard', compact('stats', 'recentPayments'));
    }
}
