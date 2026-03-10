<?php

use App\Http\Controllers\Accountants\AccountantDashboardController;
use App\Http\Controllers\Accountants\FinancialAidController;
use App\Http\Controllers\Accountants\ReportController;
use App\Http\Controllers\Accountants\StudentController;
use App\Http\Controllers\Accountants\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'school.scope'])->prefix('accountant')->name('accountant.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AccountantDashboardController::class, 'index'])->name('dashboard');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{payment}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Financial Aid
    Route::get('/financial-aid', [FinancialAidController::class, 'index'])->name('financial-aid.index');
    Route::get('/financial-aid/create', [FinancialAidController::class, 'create'])->name('financial-aid.create');
    Route::post('/financial-aid', [FinancialAidController::class, 'store'])->name('financial-aid.store');
    Route::get('/financial-aid/{financialAid}', [FinancialAidController::class, 'show'])->name('financial-aid.show');
    Route::get('/financial-aid/{financialAid}/edit', [FinancialAidController::class, 'edit'])->name('financial-aid.edit');
    Route::put('/financial-aid/{financialAid}', [FinancialAidController::class, 'update'])->name('financial-aid.update');
    Route::get('/financial-aid/{financialAid}/beneficiaries', [FinancialAidController::class, 'manageBeneficiaries'])->name('financial-aid.beneficiaries');
    Route::post('/financial-aid/{financialAid}/beneficiaries', [FinancialAidController::class, 'addBeneficiary'])->name('financial-aid.beneficiaries.add');
    Route::delete('/financial-aid/{financialAid}/beneficiaries/{student}', [FinancialAidController::class, 'removeBeneficiary'])->name('financial-aid.beneficiaries.remove');

    // Students (View only for payment tracking)
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/payments', [StudentController::class, 'payments'])->name('students.payments');
});
