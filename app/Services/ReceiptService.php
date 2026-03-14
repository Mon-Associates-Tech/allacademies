<?php

namespace App\Services;

use App\Models\SchoolPayment;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptService
{
    public function generateReceipt(SchoolPayment $payment)
    {
        $data = [
            'payment' => $payment,
            'school' => $payment->school,
            'student' => $payment->student,
            'generated_at' => now(),
        ];

        return Pdf::loadView('accountant.reports.pdf.receipt', $data)
            ->setPaper('a4', 'portrait');
    }

    public function generateBulkReceipts(array $paymentIds)
    {
        $payments = SchoolPayment::whereIn('id', $paymentIds)
            ->with(['school', 'student.user', 'payer'])
            ->get();

        $data = [
            'payments' => $payments,
            'school' => $payments->first()?->school,
            'generated_at' => now(),
        ];

        return Pdf::loadView('accountant.reports.pdf.bulk-receipts', $data)
            ->setPaper('a4', 'portrait');
    }
}