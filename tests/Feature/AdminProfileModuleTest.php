<?php

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function makeProfileModuleAdmin(string $role, ?string $email = null): Admin
{
    return Admin::query()->create([
        'name' => 'Profile Module Admin',
        'email' => $email ?? $role.'-profile@example.com',
        'password' => Hash::make('password'),
        'role' => $role,
        'position' => 'System Lead',
        'department' => 'IT',
        'contact_number' => '+63 900 000 0000',
    ]);
}

test('super admin can open the profile module', function () {
    $admin = makeProfileModuleAdmin('super_admin', 'super-profile@example.com');

    $this->withSession(['admin_id' => $admin->id])
        ->get('/request-captcha/admin/profile')
        ->assertOk()
        ->assertSee('Profile')
        ->assertSee('My admin information')
        ->assertSee('My account settings')
        ->assertSee('super-profile@example.com')
        ->assertSee(route('admin.account.my'), false);
});

test('admin accounts page keeps only admin management and existing admins', function () {
    $admin = makeProfileModuleAdmin('super_admin', 'super-profile@example.com');

    $response = $this->withSession(['admin_id' => $admin->id])
        ->get('/request-captcha/admin/account/admins')
        ->assertOk()
        ->assertSee('Admin accounts')
        ->assertSee('Admin management')
        ->assertSee('Existing admins')
        ->assertDontSee('My admin information')
        ->assertDontSee('My account settings')
        ->assertDontSee('Change your password')
        ->assertDontSee('Change your email');

    expect($response->getContent())->toContain('Existing admins');
    expect(strpos($response->getContent(), 'Existing admins'))
        ->toBeLessThan(strpos($response->getContent(), 'Admin management'));
});
