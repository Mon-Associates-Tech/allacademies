<?php

namespace App\Services;

use App\Models\SchoolPayment;
use App\Models\Student;
use App\Models\FinancialAid;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    public function getPaymentSummary(array $filters): array
    {
        $query = SchoolPayment::query()
            ->where('school_id', $filters['school_id']);

        $this->applyFilters($query, $filters);

        $payments = $query->with(['student.user', 'academicPeriod', 'academicLevel'])->get();

        $totalPayments = $payments->count();
        $successfulPayments = $payments->where('status', 'succeeded')->count();
        $totalAmount = $payments->where('status', 'succeeded')->sum('amount');
        
        $byPaymentType = $this->groupByPaymentType($payments);
        $byStatus = $payments->groupBy('status')->map(function ($group) use ($totalAmount) {
            $amount = $group->where('status', 'succeeded')->sum('amount');
            return [
                'count' => $group->count(),
                'amount' => $amount,
                'percentage' => $totalAmount > 0 ? ($amount / $totalAmount) * 100 : 0,
            ];
        })->toArray();
        
        // Add percentage to payment types
        foreach ($byPaymentType as $type => &$stats) {
            $stats['amount'] = $stats['total'];
            $stats['percentage'] = $totalAmount > 0 ? ($stats['total'] / $totalAmount) * 100 : 0;
        }

        return [
            // Raw data
            'total_payments' => $totalPayments,
            'successful_payments' => $successfulPayments,
            'pending_payments' => $payments->where('status', 'pending')->count(),
            'failed_payments' => $payments->where('status', 'failed')->count(),
            'total_amount' => $totalAmount,
            'pending_amount' => $payments->where('status', 'pending')->sum('amount'),
            'by_payment_type' => $byPaymentType,
            'by_period' => $this->groupByPeriod($payments),
            'by_level' => $this->groupByLevel($payments),
            'payments' => $payments,
            
            // PDF template data
            'summary_stats' => [
                'total_payments' => $totalPayments,
                'successful_payments' => $successfulPayments,
                'total_amount' => $totalAmount,
                'average_payment' => $successfulPayments > 0 ? $totalAmount / $successfulPayments : 0,
            ],
            'payment_by_type' => $byPaymentType,
            'payment_by_status' => $byStatus,
            'daily_trends' => $this->getDailyTrends($payments),
        ];
    }

    public function getStudentPayments(array $filters): array
    {
        $query = SchoolPayment::query()
            ->where('school_id', $filters['school_id']);

        if (isset($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        $this->applyFilters($query, $filters);

        $payments = $query->with(['student.user', 'academicPeriod', 'academicLevel', 'payer'])->get();

        $groupedByStudent = $payments->groupBy('student_id')->map(function ($studentPayments) {
            $student = $studentPayments->first()->student;
            return [
                'student' => $student,
                'total_paid' => $studentPayments->where('status', 'succeeded')->sum('amount'),
                'total_pending' => $studentPayments->where('status', 'pending')->sum('amount'),
                'payment_count' => $studentPayments->count(),
                'payments' => $studentPayments,
            ];
        });

        return [
            'students' => $groupedByStudent,
            'total_students' => $groupedByStudent->count(),
            'total_amount' => $payments->where('status', 'succeeded')->sum('amount'),
        ];
    }

    public function getOutstandingPayments(array $filters): array
    {
        $query = SchoolPayment::query()
            ->where('school_id', $filters['school_id'])
            ->whereIn('status', ['pending', 'failed']);

        $this->applyFilters($query, $filters);

        $payments = $query->with(['student.user', 'academicPeriod', 'academicLevel'])->get();

        return [
            'pending_payments' => $payments->where('status', 'pending'),
            'failed_payments' => $payments->where('status', 'failed'),
            'total_outstanding' => $payments->sum('amount'),
            'by_student' => $payments->groupBy('student_id'),
        ];
    }

    public function getRevenueReport(array $filters): array
    {
        $query = SchoolPayment::query()
            ->where('school_id', $filters['school_id'])
            ->where('status', 'succeeded');

        $this->applyFilters($query, $filters);

        $payments = $query->with(['academicPeriod'])->get();

        return [
            'total_revenue' => $payments->sum('amount'),
            'by_month' => $this->groupByMonth($payments),
            'by_payment_type' => $this->groupByPaymentType($payments),
            'by_period' => $this->groupByPeriod($payments),
            'average_payment' => $payments->avg('amount'),
            'highest_payment' => $payments->max('amount'),
            'lowest_payment' => $payments->min('amount'),
        ];
    }

    public function getFinancialAidReport(array $filters): array
    {
        $query = FinancialAid::query()
            ->where('school_id', $filters['school_id']);

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        $financialAids = $query->with(['beneficiaries.user'])->get();

        $paymentsWithAid = SchoolPayment::query()
            ->where('school_id', $filters['school_id'])
            ->whereNotNull('financial_aid_id')
            ->where('status', 'succeeded');

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $paymentsWithAid->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        $aidPayments = $paymentsWithAid->get();

        return [
            'total_programs' => $financialAids->count(),
            'total_beneficiaries' => $financialAids->sum(fn($aid) => $aid->beneficiaries->count()),
            'total_aid_amount' => $aidPayments->sum('amount'),
            'programs' => $financialAids,
            'aid_payments' => $aidPayments,
        ];
    }

    public function getCustomReport(array $filters): array
    {
        $query = SchoolPayment::query()
            ->where('school_id', $filters['school_id']);

        $this->applyFilters($query, $filters);

        $payments = $query->with(['student.user', 'academicPeriod', 'academicLevel', 'academicGroup', 'payer'])->get();

        return [
            'payments' => $payments,
            'summary' => [
                'total_count' => $payments->count(),
                'total_amount' => $payments->where('status', 'succeeded')->sum('amount'),
                'by_status' => $payments->groupBy('status')->map->count(),
                'by_type' => $this->groupByPaymentType($payments),
            ],
        ];
    }

    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['payment_type'])) {
            $query->where('payment_type', $filters['payment_type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['academic_period_id'])) {
            $query->where('academic_period_id', $filters['academic_period_id']);
        }

        if (isset($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (isset($filters['academic_level_id'])) {
            $query->where('academic_level_id', $filters['academic_level_id']);
        }

        if (isset($filters['academic_group_id'])) {
            $query->where('academic_group_id', $filters['academic_group_id']);
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
    }

    private function groupByPaymentType(Collection $payments): array
    {
        return $payments->groupBy('payment_type')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->where('status', 'succeeded')->sum('amount'),
            ];
        })->toArray();
    }

    private function groupByPeriod(Collection $payments): array
    {
        return $payments->groupBy('academic_period_id')->map(function ($group) {
            return [
                'period' => $group->first()->academicPeriod?->name ?? 'N/A',
                'count' => $group->count(),
                'total' => $group->where('status', 'succeeded')->sum('amount'),
            ];
        })->toArray();
    }

    private function groupByLevel(Collection $payments): array
    {
        return $payments->groupBy('academic_level_id')->map(function ($group) {
            return [
                'level' => $group->first()->academicLevel?->name ?? 'N/A',
                'count' => $group->count(),
                'total' => $group->where('status', 'succeeded')->sum('amount'),
            ];
        })->toArray();
    }

    private function groupByMonth(Collection $payments): array
    {
        return $payments->groupBy(function ($payment) {
            return $payment->created_at->format('Y-m');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        })->toArray();
    }
    
    private function getDailyTrends(Collection $payments): array
    {
        return $payments->groupBy(function ($payment) {
            return $payment->created_at->format('Y-m-d');
        })->map(function ($group, $date) {
            return [
                'date' => \Carbon\Carbon::parse($date)->format('M d, Y'),
                'count' => $group->count(),
                'amount' => $group->where('status', 'succeeded')->sum('amount'),
            ];
        })->sortBy('date')->values()->toArray();
    }
}
