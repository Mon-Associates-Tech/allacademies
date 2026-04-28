<?php

namespace App\Observers;

use App\Models\AcademicFeeStructure;
use App\Models\Student;
use App\Models\StudentPaymentRecord;
use Illuminate\Support\Facades\Log;

class AcademicFeeStructureObserver
{
    /**
     * Handle the AcademicFeeStructure "created" event.
     */
    public function created(AcademicFeeStructure $structure): void
    {
        $this->createStudentPaymentRecords($structure);
    }

    /**
     * Handle the AcademicFeeStructure "updated" event.
     */
    public function updated(AcademicFeeStructure $structure): void
    {
        // If amount or due date changed, update existing records
        if ($structure->wasChanged(['amount', 'due_date'])) {
            $this->updateExistingRecords($structure);
        }
    }

    /**
     * Create student payment records for all applicable students
     */
    protected function createStudentPaymentRecords(AcademicFeeStructure $structure): void
    {
        try {
            $students = $this->getApplicableStudents($structure);
            
            Log::info('Creating student payment records from academic fee structure', [
                'structure_id' => $structure->id,
                'academic_group_id' => $structure->academic_group_id,
                'academic_level_id' => $structure->academic_level_id,
                'student_count' => $students->count(),
            ]);

            foreach ($students as $student) {
                // Check if record already exists for this student, term, and group/level
                $exists = StudentPaymentRecord::where('school_id', $structure->school_id)
                    ->where('student_id', $student->id)
                    ->where('academic_period_id', $structure->current_term_id)
                    ->where('payment_type', 'tuition') // Academic fee structures are typically tuition
                    ->exists();

                if (!$exists) {
                    StudentPaymentRecord::create([
                        'school_id' => $structure->school_id,
                        'student_id' => $student->id,
                        'payment_structure_id' => null, // No link to SchoolPaymentStructure
                        'academic_year_id' => null, // Could be derived from term if needed
                        'academic_period_id' => $structure->current_term_id,
                        'payment_type' => 'tuition',
                        'description' => 'School Fees - ' . ($structure->academicGroup->name ?? '') . ' - ' . ($structure->academicLevel->name ?? ''),
                        'total_amount' => $structure->amount,
                        'amount_paid' => 0,
                        'amount_remaining' => $structure->amount,
                        'currency' => 'GHS',
                        'due_date' => $structure->due_date,
                        'status' => 'unpaid',
                        'is_custom' => false,
                        'metadata' => [
                            'academic_fee_structure_id' => $structure->id,
                            'payment_method' => $structure->payment_method,
                        ],
                    ]);
                }
            }

            Log::info('Student payment records created successfully from academic fee structure', [
                'structure_id' => $structure->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create student payment records from academic fee structure', [
                'structure_id' => $structure->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Update existing payment records when structure is modified
     */
    protected function updateExistingRecords(AcademicFeeStructure $structure): void
    {
        try {
            $records = StudentPaymentRecord::where('school_id', $structure->school_id)
                ->where('academic_period_id', $structure->current_term_id)
                ->where('payment_type', 'tuition')
                ->whereJsonContains('metadata->academic_fee_structure_id', $structure->id)
                ->get();

            foreach ($records as $record) {
                // Only update if not yet paid
                if ($record->amount_paid == 0) {
                    $record->update([
                        'total_amount' => $structure->amount,
                        'amount_remaining' => $structure->amount,
                        'due_date' => $structure->due_date,
                    ]);
                } else {
                    // If partially paid, adjust remaining amount
                    $newRemaining = $structure->amount - $record->amount_paid;
                    $record->update([
                        'total_amount' => $structure->amount,
                        'amount_remaining' => max(0, $newRemaining),
                        'due_date' => $structure->due_date,
                    ]);
                    $record->updatePaymentStatus();
                }
            }

            Log::info('Updated existing payment records from academic fee structure', [
                'structure_id' => $structure->id,
                'records_updated' => $records->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update existing payment records', [
                'structure_id' => $structure->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get applicable students based on academic group and level
     */
    protected function getApplicableStudents(AcademicFeeStructure $structure)
    {
        $query = Student::where('school_id', $structure->school_id)
            ->where('status', 'active');

        if ($structure->academic_group_id) {
            $query->where('academic_group_id', $structure->academic_group_id);
        }

        if ($structure->academic_level_id) {
            $query->where('academic_level_id', $structure->academic_level_id);
        }

        return $query->get();
    }
}
