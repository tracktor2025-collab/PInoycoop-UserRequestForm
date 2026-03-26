<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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
}
