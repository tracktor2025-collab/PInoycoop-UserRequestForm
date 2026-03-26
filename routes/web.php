<?php

use App\Http\Controllers\AdminAccessRequestController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminSystemController;
use App\Http\Controllers\AdminTwoFactorController;
use App\Http\Controllers\UserAccessRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserAccessRequestController::class, 'landing'])->name('landing');
Route::post('/verify-captcha', [UserAccessRequestController::class, 'verifyCaptcha'])
    ->middleware('throttle:captcha-verify')
    ->name('captcha.verify');
Route::get('/request', [UserAccessRequestController::class, 'form'])->middleware('landing.captcha')->name('request.form');
Route::post('/submit-request', [UserAccessRequestController::class, 'submit'])
    ->middleware('throttle:public-submit')
    ->name('user.submit');
Route::get('/success', [UserAccessRequestController::class, 'success'])->name('success');
Route::match(['get', 'post'], '/success/pdf', [UserAccessRequestController::class, 'successPdf'])->name('success.pdf');

Route::prefix('admin')->group(function (): void {
    Route::get('/login', [AdminAccessRequestController::class, 'loginForm'])->name('admin.login.form');
    Route::post('/login', [AdminAccessRequestController::class, 'login'])
        ->middleware('throttle:admin-login')
        ->name('admin.login');

    Route::middleware('admin.pending-2fa')->group(function (): void {
        Route::get('/two-factor/setup', [AdminTwoFactorController::class, 'setup'])->name('admin.two-factor.setup');
        Route::post('/two-factor/setup', [AdminTwoFactorController::class, 'confirmSetup'])
            ->middleware('throttle:admin-two-factor')
            ->name('admin.two-factor.setup.confirm');
        Route::get('/two-factor/challenge', [AdminTwoFactorController::class, 'challenge'])->name('admin.two-factor.challenge');
        Route::post('/two-factor/challenge', [AdminTwoFactorController::class, 'verifyChallenge'])
            ->middleware('throttle:admin-two-factor')
            ->name('admin.two-factor.verify');
    });

    Route::middleware('admin.auth')->group(function (): void {
        Route::post('/logout', [AdminAccessRequestController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [AdminAccessRequestController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/approvals', [AdminAccessRequestController::class, 'approvals'])->name('admin.approvals');
        Route::post('/approvals/bulk-approve', [AdminAccessRequestController::class, 'bulkApprove'])
            ->middleware('throttle:admin-action')
            ->name('admin.approvals.bulk');
        Route::post('/approvals/{accessRequest}', [AdminAccessRequestController::class, 'updateApproval'])
            ->middleware('throttle:admin-action')
            ->name('admin.approvals.update');
        Route::get('/pdf-archive', [AdminAccessRequestController::class, 'pdfArchive'])->name('admin.pdf.archive');
        Route::get('/pdf-archive/{accessRequest}/download', [AdminAccessRequestController::class, 'downloadPdf'])->name('admin.pdf.download');

        Route::get('/account/admins', [AdminAccountController::class, 'adminsIndex'])->name('admin.account.admins');
        Route::post('/account/password', [AdminAccountController::class, 'updatePassword'])->name('admin.account.password.update');
        Route::post('/account/email', [AdminAccountController::class, 'updateEmail'])
            ->middleware('throttle:admin-action')
            ->name('admin.account.email.update');
        Route::post('/account/admins', [AdminAccountController::class, 'storeAdmin'])->name('admin.account.admins.store');

        Route::get('/system', [AdminSystemController::class, 'index'])->name('admin.system.index');
        Route::get('/system/audit-trail', [AdminSystemController::class, 'auditTrail'])->name('admin.system.audit');
        Route::post('/system/reports/data', [AdminSystemController::class, 'reportData'])
            ->middleware('throttle:admin-action')
            ->name('admin.system.reports.data');
    });
});
