<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Create the first admin from environment variables (no passwords in source code).
     * Set ADMIN_EMAIL and ADMIN_INITIAL_PASSWORD in .env, then run:
     * php artisan db:seed --class=AdminSeeder
     */
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', '');
        $plainPassword = (string) env('ADMIN_INITIAL_PASSWORD', '');
        $name = (string) env('ADMIN_NAME', 'Administrator');

        if ($email === '' || $plainPassword === '') {
            $this->command?->warn(
                'AdminSeeder skipped: set ADMIN_EMAIL and ADMIN_INITIAL_PASSWORD in your environment, then run this seeder again.'
            );

            return;
        }

        if (Admin::query()->where('email', $email)->exists()) {
            $this->command?->info('An admin with this email already exists. Skipping AdminSeeder.');

            return;
        }

        Admin::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword,
            'role' => (string) env('ADMIN_ROLE', 'super_admin'),
        ]);

        $this->command?->info('Initial admin account created. You can remove ADMIN_INITIAL_PASSWORD from .env after first login if you prefer.');
    }
}
