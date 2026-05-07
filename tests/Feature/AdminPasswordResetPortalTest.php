<?php

use App\Models\Admin;
use App\Notifications\AdminResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

function makePasswordResetAdmin(string $role, ?string $email = null): Admin
{
    return Admin::query()->create([
        'name' => 'Password Reset Admin',
        'email' => $email ?? $role.'-reset@example.com',
        'password' => Hash::make('old-password'),
        'role' => $role,
    ]);
}

test('cms forgot password does not send reset link to request admin accounts', function () {
    Notification::fake();

    $admin = makePasswordResetAdmin('admin', 'request-admin@example.com');

    $this->from('/admin/forgot-password')
        ->post('/admin/forgot-password', [
            'email' => $admin->email,
        ])
        ->assertRedirect('/admin/forgot-password')
        ->assertSessionHas('status');

    Notification::assertNotSentTo($admin, AdminResetPassword::class);
});

test('request admin forgot password does not send reset link to cms admin accounts', function () {
    Notification::fake();

    $admin = makePasswordResetAdmin('cms_admin', 'cms-admin@example.com');

    $this->from('/request-captcha/admin/forgot-password')
        ->post('/request-captcha/admin/forgot-password', [
            'email' => $admin->email,
        ])
        ->assertRedirect('/request-captcha/admin/forgot-password')
        ->assertSessionHas('status');

    Notification::assertNotSentTo($admin, AdminResetPassword::class);
});

test('cms forgot password sends reset link to cms admin accounts', function () {
    Notification::fake();

    $admin = makePasswordResetAdmin('cms_admin', 'cms-admin@example.com');

    $this->post('/admin/forgot-password', [
        'email' => $admin->email,
    ])->assertSessionHas('status', __(Password::RESET_LINK_SENT));

    Notification::assertSentTo($admin, AdminResetPassword::class);
});

test('request admin forgot password sends reset link to request admin accounts', function () {
    Notification::fake();

    $admin = makePasswordResetAdmin('admin', 'request-admin@example.com');

    $this->post('/request-captcha/admin/forgot-password', [
        'email' => $admin->email,
    ])->assertSessionHas('status', __(Password::RESET_LINK_SENT));

    Notification::assertSentTo($admin, AdminResetPassword::class);
});

test('cms reset form rejects request admin email', function () {
    $admin = makePasswordResetAdmin('admin', 'request-admin@example.com');
    $token = Password::broker('admins')->createToken($admin);

    $this->get('/admin/reset-password/'.$token.'?email='.$admin->email)
        ->assertRedirect('/admin/forgot-password')
        ->assertSessionHas('error');
});

test('request admin reset form rejects cms admin email', function () {
    $admin = makePasswordResetAdmin('cms_admin', 'cms-admin@example.com');
    $token = Password::broker('admins')->createToken($admin);

    $this->get('/request-captcha/admin/reset-password/'.$token.'?email='.$admin->email)
        ->assertRedirect('/request-captcha/admin/forgot-password')
        ->assertSessionHas('error');
});
