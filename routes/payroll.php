<?php

use App\Http\Controllers\Payroll\PayrollAuditController;
use App\Http\Controllers\Payroll\PayrollEntryController;
use App\Http\Controllers\Payroll\PayrollRoleController;
use App\Http\Controllers\Payroll\PayrollRunController;
use App\Http\Controllers\Payroll\PayrollScheduleController;
use App\Http\Controllers\Payroll\PayrollUtilityController;
use App\Http\Controllers\Payroll\PayslipController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', App\Http\Middleware\EnsurePayrollAccess::class])
    ->prefix('payroll')
    ->name('payroll.')
    ->group(function () {
        
        // Payroll Entries
        Route::resource('entries', PayrollEntryController::class);
        Route::post('entries/{entry}/bank-account', [PayrollEntryController::class, 'storeBankAccount'])
            ->name('entries.bank-account.store');
        Route::post('entries/{entry}/verify-account', [PayrollEntryController::class, 'verifyBankAccount'])
            ->name('entries.verify-account');
        
        // Payroll Roles
        Route::resource('roles', PayrollRoleController::class)->except(['show']);
        
        // Schedules
        Route::resource('schedules', PayrollScheduleController::class);
        
        // Runs
        Route::resource('runs', PayrollRunController::class)->only(['index', 'show', 'store', 'destroy']);
        Route::post('runs/{run}/submit', [PayrollRunController::class, 'submit'])
            ->name('runs.submit');
        Route::post('runs/{run}/approve', [PayrollRunController::class, 'approve'])
            ->name('runs.approve')
            ->middleware('can:approvePayroll,App\Models\User');
        Route::post('runs/{run}/cancel', [PayrollRunController::class, 'cancel'])
            ->name('runs.cancel');
        Route::post('runs/{run}/retry', [PayrollRunController::class, 'retryFailed'])
            ->name('runs.retry');
        
        // Payslips
        Route::get('disbursements/{disbursement}/payslip', [PayslipController::class, 'show'])
            ->name('disbursements.payslip');
        Route::get('disbursements/{disbursement}/payslip/pdf', [PayslipController::class, 'download'])
            ->name('disbursements.payslip.pdf');
        
        // Audit Log
        Route::get('audit', [PayrollAuditController::class, 'index'])
            ->name('audit');
        
        // Utilities
        Route::get('banks', [PayrollUtilityController::class, 'banks'])
            ->name('banks');
    });
