<?php

use App\Http\Middleware\EnsureAdminAuthenticated;
use App\Http\Middleware\EnsureLandingCaptchaVerified;
use App\Http\Middleware\EnsurePendingAdminTwoFactor;
use App\Http\Middleware\EnsureStandardAdmin;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);

        $middleware->alias([
            'landing.captcha' => EnsureLandingCaptchaVerified::class,
            'admin.auth' => EnsureAdminAuthenticated::class,
            'admin.pending-2fa' => EnsurePendingAdminTwoFactor::class,
            'admin.super' => EnsureSuperAdmin::class,
            'admin.standard' => EnsureStandardAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
