<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLandingCaptchaVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('landing_captcha_verified', false)) {
            return redirect()->route('landing')->with('error', 'Please complete the CAPTCHA to continue.');
        }

        return $next($request);
    }
}

