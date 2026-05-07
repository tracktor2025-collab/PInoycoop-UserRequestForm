<?php

use App\Models\Admin;
use App\Notifications\AdminResetPassword;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

uses(TestCase::class);

test('cms admin reset password notification uses pinoycoop account copy', function () {
    URL::forceRootUrl('http://127.0.0.1:8000');

    $admin = new Admin([
        'name' => 'CMS Admin',
        'email' => 'cms@example.com',
        'role' => 'cms_admin',
    ]);

    $notification = new AdminResetPassword('reset-token');
    $mailMessage = $notification->toMail($admin);

    expect($mailMessage->view)->toBe('emails.admin-reset-password');
    expect($mailMessage->viewData)->not->toHaveKey('logoPath');
    expect($mailMessage->viewData)->not->toHaveKey('logoUrl');
    expect($mailMessage->viewData)->not->toHaveKey('logoAlt');
    expect($mailMessage->viewData['accountLabel'])->toBe('Pinoycoop CMS admin account');
    expect($mailMessage->viewData['expire'])->toBe((int) config('auth.passwords.admins.expire', 60));
    expect($mailMessage->viewData['actionUrl'])->toContain('/admin/reset-password/reset-token');
});

test('request admin reset password notification uses request admin account copy', function () {
    URL::forceRootUrl('http://127.0.0.1:8000');

    $admin = new Admin([
        'name' => 'Request Admin',
        'email' => 'request@example.com',
        'role' => 'admin',
    ]);

    $notification = new AdminResetPassword('reset-token');
    $mailMessage = $notification->toMail($admin);

    expect($mailMessage->view)->toBe('emails.admin-reset-password');
    expect($mailMessage->viewData)->not->toHaveKey('logoPath');
    expect($mailMessage->viewData)->not->toHaveKey('logoUrl');
    expect($mailMessage->viewData)->not->toHaveKey('logoAlt');
    expect($mailMessage->viewData['accountLabel'])->toBe('request admin account');
    expect($mailMessage->viewData['actionUrl'])->toContain('/request-captcha/admin/reset-password/reset-token');
});

test('reset password email renders without a logo image', function () {
    $html = view('emails.admin-reset-password', [
        'subject' => 'Reset Password Notification',
        'actionUrl' => 'http://127.0.0.1:8000/admin/reset-password/reset-token?email=cms%40example.com',
        'expire' => 60,
        'accountLabel' => 'Pinoycoop CMS admin account',
    ])->render();

    expect($html)->not->toContain('<img');
    expect($html)->not->toContain('embed(');
    expect($html)->not->toContain('cid:');
});
