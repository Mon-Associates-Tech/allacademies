<?php

namespace App\Observers;

use App\Models\SchoolPayment;
use App\Models\StudentPaymentRecord;

class SchoolPaymentObserver
{
    public function updated(SchoolPayment $payment): void
    {
        // When a payment is marked as succeeded, update the student payment record
        if ($payment->isDirty('status') && $payment->status === 'succeeded') {
            $this->updateStudentPaymentRecord($payment);
        }
    }

    public function created(SchoolPayment $payment): void
    {
        // If payment is created as succeeded, update immediately
        if ($payment->status === 'succeeded') {
            $this->updateStudentPaymentRecord($payment);
        }
    }

    protected function updateStudentPaymentRecord(SchoolPayment $payment): void
    {
        // If payment is linked to a specific student payment record
        if ($payment->student_payment_record_id) {
            $record = StudentPaymentRecord::find($payment->student_payment_record_id);
            if ($record) {
                $record->addPayment($payment->amount);
                return;
            }
        }

        // Otherwise, try to find matching student payment record
        if ($payment->student_id) {
            $record = StudentPaymentRecord::where('student_id', $payment->student_id)
                ->where('school_id', $payment->school_id)
                ->where('payment_type', $payment->payment_type)
                ->where('status', '!=', 'paid')
                ->where('waived', false)
                ->orderBy('due_date', 'asc')
                ->first();

            if ($record) {
                $record->addPayment($payment->amount);
                
                // Link the payment to this record for future reference
                $payment->update(['student_payment_record_id' => $record->id]);
            }
        }
    }
}
