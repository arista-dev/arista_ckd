<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterModelController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ReportController;

// ─── Guest ────────────────────────────────────────────────────────────────────
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Authenticated ────────────────────────────────────────────────────────────
Route::middleware('auth.ckd')->group(function () {

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Master Model — Admin only ─────────────────────────────────────────────
    Route::middleware('auth.ckd:admin')->group(function () {
        Route::resource('master', MasterModelController::class);
        Route::post('/master/{master}/component/{component}/toggle',
                    [MasterModelController::class, 'toggleComponent'])
             ->name('master.component.toggle');
    });

    // ── Receiving — Admin only ────────────────────────────────────────────────
    Route::middleware('auth.ckd:admin')->group(function () {
        Route::get('/receiving',        [ReceivingController::class, 'index'])->name('receiving.index');
        Route::get('/receiving/create', [ReceivingController::class, 'create'])->name('receiving.create');
        Route::post('/receiving',       [ReceivingController::class, 'store'])->name('receiving.store');
    });

    // ── Inspection — Admin & Inspector ────────────────────────────────────────
    Route::middleware('auth.ckd:admin,inspector')->group(function () {
        Route::get('/inspection',       [InspectionController::class, 'index'])->name('inspection.index');
        Route::get('/inspection/{id}',  [InspectionController::class, 'show'])->name('inspection.show');
        Route::post('/inspection/{id}', [InspectionController::class, 'update'])->name('inspection.update');
    });

    // ── Approval — Supervisor only ────────────────────────────────────────────
    Route::middleware('auth.ckd:supervisor')->group(function () {
        Route::get('/approval',       [ApprovalController::class, 'index'])->name('approval.index');
        Route::post('/approval/{id}', [ApprovalController::class, 'action'])->name('approval.action');
    });

    // ── Report — Admin & Supervisor ───────────────────────────────────────────
    Route::middleware('auth.ckd:admin,supervisor')->group(function () {
        Route::get('/report',        [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/export', [ReportController::class, 'export'])->name('report.export');
    });
});
