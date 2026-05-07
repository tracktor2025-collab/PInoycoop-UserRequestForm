<?php

namespace App\Models;

use App\Notifications\AdminResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements CanResetPasswordContract
{
    use CanResetPassword;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'position',
        'department',
        'contact_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function hasEnabledTwoFactor(): bool
    {
        if ($this->two_factor_confirmed_at === null) {
            return false;
        }

        $secret = $this->two_factor_secret;

        return is_string($secret) && $secret !== '';
    }

    public function isSuperAdmin(): bool
    {
        return (string) $this->role === 'super_admin';
    }

    public function isCmsAdmin(): bool
    {
        return (string) $this->role === 'cms_admin';
    }

    public function isStandardAdmin(): bool
    {
        return (string) $this->role === 'admin';
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new AdminResetPassword($token));
    }
}
