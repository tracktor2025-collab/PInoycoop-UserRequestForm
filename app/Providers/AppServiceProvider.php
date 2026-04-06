<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // pragmarx/google2fa must be in composer.json; if Composer's optimized autoload omits it
        // (e.g. incomplete dump-autoload), load PSR-4 classes from vendor here.
        spl_autoload_register(static function (string $class): void {
            $prefix = 'PragmaRX\\Google2FA\\';
            if (! str_starts_with($class, $prefix)) {
                return;
            }

            $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
            $path = base_path('vendor/pragmarx/google2fa/src/'.$relative.'.php');

            if (is_file($path)) {
                require_once $path;
            }
        }, prepend: true);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Admin UI uses Bootstrap 5 only; Laravel's default Tailwind pagination breaks layout
        // (unstyled responsive blocks, oversized SVG chevrons) when Tailwind is not loaded.
        Paginator::useBootstrapFive();

        RateLimiter::for('admin-login', function (Request $request) {
            $email = (string) $request->input('email', '');

            return Limit::perMinute(12)->by($request->ip().'|'.$email);
        });

        RateLimiter::for('admin-password-reset', function (Request $request) {
            $email = (string) $request->input('email', '');

            return Limit::perMinute(6)->by($request->ip().'|'.$email);
        });

        RateLimiter::for('admin-two-factor', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('public-submit', function (Request $request) {
            return Limit::perHour(40)->by($request->ip());
        });

        RateLimiter::for('captcha-verify', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('admin-action', function (Request $request) {
            $adminId = (string) $request->session()->get('admin_id', 'guest');

            return Limit::perMinute(120)->by($request->ip().'|'.$adminId);
        });
    }
}
