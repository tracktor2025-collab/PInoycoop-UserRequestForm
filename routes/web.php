<?php

use App\Http\Controllers\AdminAccessRequestController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminPasswordResetController;
use App\Http\Controllers\AdminSystemController;
use App\Http\Controllers\AdminTwoFactorController;
use App\Http\Controllers\PinoycoopAdmin\CmsPageController as PinoycoopCmsPageController;
use App\Http\Controllers\PinoycoopAdmin\DashboardController as PinoycoopDashboardController;
use App\Http\Controllers\PinoycoopAdmin\MediaController as PinoycoopMediaController;
use App\Http\Controllers\PinoycoopAdmin\MenuController as PinoycoopMenuController;
use App\Http\Controllers\PinoycoopAdmin\MenuItemController as PinoycoopMenuItemController;
use App\Http\Controllers\PinoycoopAdmin\SettingController as PinoycoopSettingController;
use App\Http\Controllers\PinoycoopAdmin\UserManagementController as PinoycoopUserManagementController;
use App\Http\Controllers\UserAccessRequestController;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserAccessRequestController::class, 'landing'])->name('landing');
Route::view('/about', 'pinooycoop.pages.about')->name('pinooycoop.about');
Route::view('/service', 'pinooycoop.pages.service')->name('pinooycoop.service');
Route::get('/events', function () {
    return view('pinooycoop.pages.events', [
        'items' => Page::query()
            ->where('is_published', true)
            ->whereIn('template', ['headline', 'feature_story', 'standard_news', 'short_brief', 'event', 'news'])
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get(['id', 'title', 'slug', 'template', 'published_at', 'updated_at', 'content', 'image_blob', 'image_mime']),
    ]);
})->name('pinooycoop.events');
Route::get('/p/{slug}', function (string $slug) {
    $page = Page::query()
        ->where('slug', $slug)
        ->where('is_published', true)
        ->firstOrFail();

    return view('pinooycoop.pages.cms-page', [
        'page' => $page,
    ]);
})->name('cms.page');
Route::get('/request-captcha', [UserAccessRequestController::class, 'captchaPage'])->name('request.captcha');
Route::post('/verify-captcha', [UserAccessRequestController::class, 'verifyCaptcha'])
    ->middleware('throttle:captcha-verify')
    ->name('captcha.verify');
Route::get('/request', [UserAccessRequestController::class, 'form'])->middleware('landing.captcha')->name('request.form');
Route::post('/submit-request', [UserAccessRequestController::class, 'submit'])
    ->middleware('throttle:public-submit')
    ->name('user.submit');
Route::get('/success', [UserAccessRequestController::class, 'success'])->name('success');
Route::match(['get', 'post'], '/success/pdf', [UserAccessRequestController::class, 'successPdf'])->name('success.pdf');

Route::prefix('admin')->name('pinoycoop.admin.')->group(function (): void {
    Route::get('/', [PinoycoopDashboardController::class, 'index'])->name('dashboard');
    Route::get('/pages', [PinoycoopCmsPageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [PinoycoopCmsPageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [PinoycoopCmsPageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [PinoycoopCmsPageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [PinoycoopCmsPageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [PinoycoopCmsPageController::class, 'destroy'])->name('pages.destroy');

    Route::get('/menus', [PinoycoopMenuController::class, 'index'])->name('menus.index');
    Route::get('/menus/create', [PinoycoopMenuController::class, 'create'])->name('menus.create');
    Route::post('/menus', [PinoycoopMenuController::class, 'store'])->name('menus.store');
    Route::get('/menus/{menu}/edit', [PinoycoopMenuController::class, 'edit'])->name('menus.edit');
    Route::put('/menus/{menu}', [PinoycoopMenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}', [PinoycoopMenuController::class, 'destroy'])->name('menus.destroy');

    Route::post('/menus/{menu}/items', [PinoycoopMenuItemController::class, 'store'])->name('menus.items.store');
    Route::put('/menus/{menu}/items/{item}', [PinoycoopMenuItemController::class, 'update'])->name('menus.items.update');
    Route::delete('/menus/{menu}/items/{item}', [PinoycoopMenuItemController::class, 'destroy'])->name('menus.items.destroy');

    Route::get('/media', [PinoycoopMediaController::class, 'index'])->name('media.index');
    Route::post('/media', [PinoycoopMediaController::class, 'store'])->name('media.store');

    Route::get('/settings/general', [PinoycoopSettingController::class, 'edit'])->name('settings.general');
    Route::put('/settings/general', [PinoycoopSettingController::class, 'update'])->name('settings.general.update');

    Route::get('/users', [PinoycoopUserManagementController::class, 'index'])->name('users.index');
});

Route::prefix('request-captcha/admin')->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.login.form'))->name('admin.entry');
    Route::get('/login', [AdminAccessRequestController::class, 'loginForm'])->name('admin.login.form');
    Route::post('/login', [AdminAccessRequestController::class, 'login'])
        ->middleware('throttle:admin-login')
        ->name('admin.login');

    Route::get('/forgot-password', [AdminPasswordResetController::class, 'requestForm'])->name('admin.password.request');
    Route::post('/forgot-password', [AdminPasswordResetController::class, 'sendResetLink'])
        ->middleware('throttle:admin-password-reset')
        ->name('admin.password.email');
    Route::get('/reset-password/{token}', [AdminPasswordResetController::class, 'resetForm'])->name('admin.password.reset');
    Route::post('/reset-password', [AdminPasswordResetController::class, 'reset'])
        ->middleware('throttle:admin-password-reset')
        ->name('admin.password.update');

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
        Route::get('/super-dashboard', [AdminAccessRequestController::class, 'superDashboard'])
            ->middleware('admin.super')
            ->name('admin.super.dashboard');
        Route::get('/pdf-archive', [AdminAccessRequestController::class, 'pdfArchive'])->name('admin.pdf.archive');
        Route::get('/pdf-archive/{accessRequest}/download', [AdminAccessRequestController::class, 'downloadPdf'])->name('admin.pdf.download');
        Route::get('/requests/{accessRequest}/summary', [AdminAccessRequestController::class, 'showRequestSummary'])->name('admin.request.summary');
        Route::get('/requests/{accessRequest}/edit', [AdminAccessRequestController::class, 'editRequestForm'])->name('admin.request.edit');
        Route::put('/requests/{accessRequest}', [AdminAccessRequestController::class, 'updateRequest'])
            ->middleware('throttle:admin-action')
            ->name('admin.request.update');
        Route::delete('/requests/{accessRequest}', [AdminAccessRequestController::class, 'destroyRequest'])
            ->middleware('throttle:admin-action')
            ->name('admin.request.destroy');
        Route::get('/requests/{accessRequest}/approval-signed', [AdminAccessRequestController::class, 'downloadApprovalSigned'])->name('admin.request.approval-signed');

        Route::middleware('admin.standard')->group(function (): void {
            Route::get('/approvals', [AdminAccessRequestController::class, 'approvals'])->name('admin.approvals');
            Route::get('/my-account', [AdminAccountController::class, 'myAccount'])->name('admin.account.my');
            Route::post('/account/password', [AdminAccountController::class, 'updatePassword'])->name('admin.account.password.update');
            Route::post('/account/email', [AdminAccountController::class, 'updateEmail'])
                ->middleware('throttle:admin-action')
                ->name('admin.account.email.update');
            Route::post('/approvals/{accessRequest}', [AdminAccessRequestController::class, 'updateApproval'])
                ->middleware('throttle:admin-action')
                ->name('admin.approvals.update');
        });

        Route::middleware('admin.super')->group(function (): void {
            Route::get('/account/admins', [AdminAccountController::class, 'adminsIndex'])->name('admin.account.admins');
            Route::post('/account/admins', [AdminAccountController::class, 'storeAdmin'])->name('admin.account.admins.store');

            Route::get('/system', [AdminSystemController::class, 'index'])->name('admin.system.index');
            Route::get('/system/audit-trail', [AdminSystemController::class, 'auditTrail'])->name('admin.system.audit');
            Route::post('/system/reports/data', [AdminSystemController::class, 'reportData'])
                ->middleware('throttle:admin-action')
                ->name('admin.system.reports.data');
        });
    });
});

Route::prefix('request-captcha/super-admin')->group(function (): void {
    Route::get('/login', [AdminAccessRequestController::class, 'loginFormSuper'])->name('super.login.form');
    Route::post('/login', [AdminAccessRequestController::class, 'loginSuper'])
        ->middleware('throttle:admin-login')
        ->name('super.login');

    Route::middleware(['admin.auth', 'admin.super'])->group(function (): void {
        Route::get('/dashboard', [AdminAccessRequestController::class, 'superDashboard'])->name('super.dashboard');
    });
});
