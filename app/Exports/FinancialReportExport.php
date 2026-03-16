<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class FinancialReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    private string $reportType;
    private array $data;

    public function __construct(string $reportType, array $data)
    {
        $this->reportType = $reportType;
        $this->data = $data;
    }

    public function collection()
    {
        return match ($this->reportType) {
            'payment_summary' => collect($this->data['payments']),
            'student_payments' => $this->getStudentPaymentsCollection(),
            'outstanding_payments' => collect($this->data['pending_payments'])->merge($this->data['failed_payments']),
            'revenue' => collect($this->data['by_month'])->map(fn($item, $month) => (object)['month' => $month, ...$item]),
            'financial_aid' => collect($this->data['programs']),
            'custom' => collect($this->data['payments']),
            default => collect([]),
        };
    }

    public function headings(): array
    {
        return match ($this->reportType) {
            'payment_summary' => ['Reference', 'Student', 'Amount', 'Type', 'Status', 'Date', 'Period'],
            'student_payments' => ['Student ID', 'Student Name', 'Total Paid', 'Total Pending', 'Payment Count'],
            'outstanding_payments' => ['Reference', 'Student', 'Amount', 'Type', 'Status', 'Date'],
            'revenue' => ['Month', 'Payment Count', 'Total Amount'],
            'financial_aid' => ['Program Name', 'Type', 'Amount', 'Beneficiaries', 'Status'],
            'custom' => ['Reference', 'Student', 'Amount', 'Type', 'Status', 'Date', 'Payer'],
            default => [],
        };
    }

    public function map($row): array
    {
        return match ($this->reportType) {
            'payment_summary' => [
                $row->reference,
                $row->student?->user?->name ?? 'N/A',
                number_format($row->amount, 2),
                ucfirst($row->payment_type),
                ucfirst($row->status),
                $row->created_at->format('Y-m-d'),
                $row->academicPeriod?->name ?? 'N/A',
            ],
            'student_payments' => [
                $row['student']->student_id ?? 'N/A',
                $row['student']->user?->name ?? 'N/A',
                number_format($row['total_paid'], 2),
                number_format($row['total_pending'], 2),
                $row['payment_count'],
            ],
            'outstanding_payments' => [
                $row->reference,
                $row->student?->user?->name ?? 'N/A',
                number_format($row->amount, 2),
                ucfirst($row->payment_type),
                ucfirst($row->status),
                $row->created_at->format('Y-m-d'),
            ],
            'revenue' => [
                $row->month,
                $row->count,
                number_format($row->total, 2),
            ],
            'financial_aid' => [
                $row->name,
                ucfirst($row->type),
                number_format($row->amount, 2),
                $row->beneficiaries->count(),
                ucfirst($row->status),
            ],
            'custom' => [
                $row->reference,
                $row->student?->user?->name ?? 'N/A',
                number_format($row->amount, 2),
                ucfirst($row->payment_type),
                ucfirst($row->status),
                $row->created_at->format('Y-m-d'),
                $row->getPayerDisplayName(),
            ],
            default => [],
        };
    }

    public function title(): string
    {
        return ucwords(str_replace('_', ' ', $this->reportType));
    }

    private function getStudentPaymentsCollection()
    {
        return collect($this->data['students'])->map(function ($studentData) {
            return (object) $studentData;
        });
    }
}