<?php

namespace App\Observers;

use App\Models\SchoolPaymentStructure;
use App\Models\StudentPaymentRecord;
use Illuminate\Support\Facades\Log;

class SchoolPaymentStructureObserver
{
    /**
     * Handle the SchoolPaymentStructure "created" event.
     */
    public function created(SchoolPaymentStructure $structure): void
    {
        if ($structure->is_active) {
            $this->createStudentPaymentRecords($structure);
        }
    }

    /**
     * Handle the SchoolPaymentStructure "updated" event.
     */
    public function updated(SchoolPaymentStructure $structure): void
    {
        // If structure was just activated, create records
        if ($structure->is_active && $structure->wasChanged('is_active')) {
            $this->createStudentPaymentRecords($structure);
        }
    }

    /**
     * Create student payment records for all applicable students
     */
    protected function createStudentPaymentRecords(SchoolPaymentStructure $structure): void
    {
        try {
            $students = $structure->getApplicableStudents();
            
            Log::info('Creating student payment records', [
                'structure_id' => $structure->id,
                'structure_name' => $structure->name,
                'student_count' => $students->count(),
            ]);

            foreach ($students as $student) {
                // Check if record already exists
                $exists = StudentPaymentRecord::where('school_id', $structure->school_id)
                    ->where('student_id', $student->id)
                    ->where('payment_structure_id', $structure->id)
                    ->exists();

                if (!$exists) {
                    StudentPaymentRecord::create([
                        'school_id' => $structure->school_id,
                        'student_id' => $student->id,
                        'payment_structure_id' => $structure->id,
                        'academic_year_id' => $structure->academic_year_id,
                        'academic_period_id' => $structure->academic_period_id,
                        'payment_type' => $structure->payment_type,
                        'description' => $structure->name,
                        'total_amount' => $structure->amount,
                        'amount_paid' => 0,
                        'amount_remaining' => $structure->amount,
                        'currency' => $structure->currency ?? 'GHS',
                        'due_date' => $structure->due_date,
                        'status' => 'unpaid',
                        'is_custom' => false,
                    ]);
                }
            }

            Log::info('Student payment records created successfully', [
                'structure_id' => $structure->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create student payment records', [
                'structure_id' => $structure->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
