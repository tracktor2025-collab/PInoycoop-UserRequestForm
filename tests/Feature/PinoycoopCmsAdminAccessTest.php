<?php

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function makeAdminWithRole(string $role): Admin
{
    return Admin::query()->create([
        'name' => 'Test Admin',
        'email' => $role.'@example.com',
        'password' => Hash::make('password'),
        'role' => $role,
    ]);
}

test('pinoycoop cms dashboard redirects guests to the cms login', function () {
    $this->get('/admin/dashboard')
        ->assertRedirect('/admin');
});

test('pinoycoop cms dashboard rejects non cms admin sessions', function () {
    $admin = makeAdminWithRole('admin');

    $this->withSession([
        'admin_id' => $admin->id,
        'login_portal' => 'admin',
    ])->get('/admin/dashboard')
        ->assertRedirect('/admin');
});

test('pinoycoop cms login form does not redirect stale cms admin sessions', function () {
    $admin = makeAdminWithRole('cms_admin');

    $response = $this->withSession([
        'admin_id' => $admin->id,
    ])->get('/admin')
        ->assertOk();

    $response
        ->assertSee('Pinoycoop CMS Admin Login')
        ->assertSee('Forgot password?')
        ->assertSee(route('admin.cms.password.request'), false)
        ->assertDontSee('Login as Request Admin')
        ->assertDontSee('Login as Super Admin');
});

test('pinoycoop cms forgot password form is branded separately', function () {
    $this->get('/admin/forgot-password')
        ->assertOk()
        ->assertSee('Pinoycoop Password Reset')
        ->assertSee(route('admin.cms.password.email'), false)
        ->assertSee(route('admin.cms.login.form'), false);
});

test('pinoycoop cms login redirects failed submissions back to login', function () {
    $this->from('/admin')
        ->post('/admin', [
            'email' => 'cms_admin@example.com',
            'password' => 'password',
        ])
        ->assertRedirect('/admin');
});

test('pinoycoop cms login accepts cms admin credentials', function () {
    makeAdminWithRole('cms_admin');

    $this->withMiddleware()
        ->withSession(['_token' => 'test-token'])
        ->post('/admin', [
            '_token' => 'test-token',
            'email' => 'cms_admin@example.com',
            'password' => 'password',
        ])
        ->assertRedirect(route('admin.two-factor.setup'));
});

test('pinoycoop cms dashboard allows cms admin sessions', function () {
    $admin = makeAdminWithRole('cms_admin');

    $this->withSession([
        'admin_id' => $admin->id,
        'login_portal' => 'cms',
    ])->get('/admin/dashboard')
        ->assertOk();
});
