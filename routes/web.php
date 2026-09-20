<?php

use App\Http\Controllers\Admin\ContractExpenseController;
use App\Http\Controllers\Admin\ContractPaymentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PropertyHouseController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:12,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->scopeBindings()->group(function (): void {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Reports
    Route::get('reports/monthly', [DashboardController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('reports/monthly/excel', [DashboardController::class, 'exportMonthlyExcel'])
        ->middleware('throttle:20,1')
        ->name('reports.monthly.excel');
    Route::get('reports/annual', [DashboardController::class, 'annualReport'])->name('reports.annual');
    Route::get('reports/annual/excel', [DashboardController::class, 'exportAnnualExcel'])
        ->middleware('throttle:20,1')
        ->name('reports.annual.excel');

    // Contracts (houses)
    Route::resource('houses', PropertyHouseController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('houses/{house}/contract/pdf',  [PropertyHouseController::class, 'downloadContractPdf'])->name('houses.contract.pdf');
    Route::get('houses/{house}/contract/word', [PropertyHouseController::class, 'downloadContractWord'])->name('houses.contract.word');

    // Contract Payments
    Route::post('houses/{house}/payments', [ContractPaymentController::class, 'store'])->name('houses.payments.store');
    Route::patch('houses/{house}/payments/{payment}', [ContractPaymentController::class, 'update'])->name('houses.payments.update');
    Route::delete('houses/{house}/payments/{payment}', [ContractPaymentController::class, 'destroy'])->name('houses.payments.destroy');

    // Contract Expenses
    Route::post('houses/{house}/expenses', [ContractExpenseController::class, 'store'])->name('houses.expenses.store');
    Route::patch('houses/{house}/expenses/{expense}', [ContractExpenseController::class, 'update'])->name('houses.expenses.update');
    Route::delete('houses/{house}/expenses/{expense}', [ContractExpenseController::class, 'destroy'])->name('houses.expenses.destroy');
});
