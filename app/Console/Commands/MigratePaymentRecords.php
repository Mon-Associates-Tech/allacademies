<?php

namespace App\Console\Commands;

use App\Models\AcademicFeeStructure;
use App\Models\SchoolPayment;
use App\Models\SchoolPaymentStructure;
use App\Models\StudentPaymentRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigratePaymentRecords extends Command
{
    protected $signature = 'payments:migrate {--dry-run : Run without making changes}';
    protected $description = 'Migrate existing payment structures and transactions to student payment records';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }

        $this->info('Starting payment records migration...');
        
        // Step 1: Create records from AcademicFeeStructures
        $this->info('Step 1: Creating records from academic fee structures...');
        $this->migrateFromAcademicFeeStructures($dryRun);
        
        // Step 2: Create records from SchoolPaymentStructures
        $this->info('Step 2: Creating records from school payment structures...');
        $this->migrateFromStructures($dryRun);
        
        // Step 3: Update records with existing SchoolPayment transactions
        $this->info('Step 3: Updating records with existing transactions...');
        $this->migrateTransactions($dryRun);
        
        $this->info('Migration completed!');
    }

    protected function migrateFromAcademicFeeStructures($dryRun)
    {
        $structures = AcademicFeeStructure::with(['academicGroup', 'academicLevel'])->get();

        $this->info("Found {$structures->count()} academic fee structures");
        
        $bar = $this->output->createProgressBar($structures->count());
        $created = 0;
        $skipped = 0;

        foreach ($structures as $structure) {
            $query = \App\Models\Student::where('school_id', $structure->school_id)
                ->where('status', 'active');

            if ($structure->academic_group_id) {
                $query->where('academic_group_id', $structure->academic_group_id);
            }

            if ($structure->academic_level_id) {
                $query->where('academic_level_id', $structure->academic_level_id);
            }

            $students = $query->get();
            
            foreach ($students as $student) {
                $exists = StudentPaymentRecord::where('school_id', $structure->school_id)
                    ->where('student_id', $student->id)
                    ->where('academic_period_id', $structure->current_term_id)
                    ->where('payment_type', 'tuition')
                    ->exists();

                if (!$exists) {
                    if (!$dryRun) {
                        StudentPaymentRecord::create([
                            'school_id' => $structure->school_id,
                            'student_id' => $student->id,
                            'payment_structure_id' => null,
                            'academic_year_id' => null,
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
                    $created++;
                } else {
                    $skipped++;
                }
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Created: {$created} records");
        $this->info("Skipped (already exists): {$skipped} records");
    }

    protected function migrateFromStructures($dryRun)
    {
        $structures = SchoolPaymentStructure::with(['academicGroup', 'academicLevel'])
            ->where('is_active', true)
            ->get();

        $this->info("Found {$structures->count()} active payment structures");
        
        $bar = $this->output->createProgressBar($structures->count());
        $created = 0;
        $skipped = 0;

        foreach ($structures as $structure) {
            $students = $structure->getApplicableStudents();
            
            foreach ($students as $student) {
                $exists = StudentPaymentRecord::where('school_id', $structure->school_id)
                    ->where('student_id', $student->id)
                    ->where('payment_structure_id', $structure->id)
                    ->exists();

                if (!$exists) {
                    if (!$dryRun) {
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
                    $created++;
                } else {
                    $skipped++;
                }
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Created: {$created} records");
        $this->info("Skipped (already exists): {$skipped} records");
    }

    protected function migrateTransactions($dryRun)
    {
        // Get all succeeded payments
        $payments = SchoolPayment::where('status', 'succeeded')
            ->with(['student'])
            ->get();

        $this->info("Found {$payments->count()} succeeded transactions");
        
        $bar = $this->output->createProgressBar($payments->count());
        $updated = 0;
        $notFound = 0;

        foreach ($payments as $payment) {
            if (!$payment->student_id) {
                $notFound++;
                $bar->advance();
                continue;
            }

            // Find matching payment record
            $record = StudentPaymentRecord::where('school_id', $payment->school_id)
                ->where('student_id', $payment->student_id)
                ->where('payment_type', $payment->payment_type)
                ->where(function($q) use ($payment) {
                    $q->where('academic_year_id', $payment->academic_year_id)
                      ->orWhereNull('academic_year_id');
                })
                ->where(function($q) use ($payment) {
                    $q->where('academic_period_id', $payment->academic_period_id)
                      ->orWhereNull('academic_period_id');
                })
                ->whereColumn('amount_remaining', '>', DB::raw('0')) // Has outstanding balance
                ->first();

            if ($record) {
                if (!$dryRun) {
                    // Update the payment record
                    $record->addPayment($payment->amount);
                    
                    // Link the transaction to the record
                    $payment->update(['student_payment_record_id' => $record->id]);
                }
                $updated++;
            } else {
                // Create a custom payment record for this transaction
                if (!$dryRun) {
                    $newRecord = StudentPaymentRecord::create([
                        'school_id' => $payment->school_id,
                        'student_id' => $payment->student_id,
                        'academic_year_id' => $payment->academic_year_id,
                        'academic_period_id' => $payment->academic_period_id,
                        'payment_type' => $payment->payment_type,
                        'description' => 'Migrated from transaction: ' . $payment->reference,
                        'total_amount' => $payment->amount,
                        'amount_paid' => $payment->amount,
                        'amount_remaining' => 0,
                        'currency' => $payment->currency ?? 'GHS',
                        'status' => 'paid',
                        'is_custom' => true,
                    ]);
                    
                    // Link the transaction
                    $payment->update(['student_payment_record_id' => $newRecord->id]);
                }
                $updated++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Updated/Created: {$updated} records");
        $this->info("Not found (no student): {$notFound} transactions");
    }
}
