<?php

namespace App\Http\Controllers;

use App\Models\SchoolPayment;
use App\Models\SchoolFee;
use App\Models\Student;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminPaymentController extends Controller
{
    protected function authorizeAdmin()
    {
        $role = Auth::user()->role ?? null;
        if (! in_array($role, ['admin', 'accountant'])) {
            abort(403);
        }
    }

    // Manually create a SchoolPayment (admin/accountant)
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'status' => 'required|in:pending,succeeded,failed',
            'payment_type' => 'nullable|string',
            'description' => 'nullable|string|max:500',
            'currency' => 'nullable|string|max:10',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        $payment = SchoolPayment::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'payment_type' => $validated['payment_type'] ?? 'other',
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'GHS',
            'payer_type' => 'other',
            'payer_name' => Auth::user()->name,
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // Log the manual creation
        UserActivityService::logResourceCreate(Auth::user(), $payment, ['manual' => true]);

        return back()->with('success', 'Manual payment record created.');
    }

    // Update payment status (mark as paid/succeeded or failed)
    public function updateStatus(Request $request, $id)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'model' => 'required|in:school_fee,school_payment',
            'status' => 'required|in:pending,succeeded,failed',
        ]);

        $model = $validated['model'];
        $status = $validated['status'];

        if ($model === 'school_fee') {
            $payment = SchoolFee::findOrFail($id);
        } else {
            $payment = SchoolPayment::findOrFail($id);
        }

        $oldStatus = $payment->status;
        $payment->status = $status;
        if ($status === 'succeeded') {
            $payment->paid_at = now();
        }
        $payment->save();

        // If SchoolPayment linked to StudentPaymentRecord, apply payment when marking succeeded
        if ($model === 'school_payment' && $status === 'succeeded' && $payment->student_payment_record_id) {
            try {
                $record = \App\Models\StudentPaymentRecord::find($payment->student_payment_record_id);
                if ($record) {
                    $record->addPayment((float) $payment->amount);
                }
            } catch (\Exception $e) {
                Log::error('Failed applying manual status change to StudentPaymentRecord: '.$e->getMessage());
            }
        }

        // Log the manual intervention
        UserActivityService::logResourceUpdate(Auth::user(), $payment, ['old_status' => $oldStatus, 'new_status' => $status]);

        return back()->with('success', 'Payment status updated.');
    }
}
