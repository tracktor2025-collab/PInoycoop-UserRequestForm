<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Access Request Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
    <div class="landing-wrapper">
        <main class="container landing-main">
            <div class="row align-items-center justify-content-center g-5">
                <div class="col-12 col-lg-6">
                    <div class="landing-brand mb-3">
                        <img
                            src="{{ asset('MASS-SPECC Logo/MASS-SPECC Logo.png') }}"
                            alt="MASS-SPECC Cooperative Development Center"
                            class="landing-logo"
                            width="280"
                            decoding="async"
                            loading="lazy"
                        >
                    </div>
                    <div class="landing-badge mb-3">
                        <span class="landing-badge-dot"></span>
                        MASS-SPECC Cooperative · Internal
                    </div>
                    <h1 class="landing-title mb-3">
                        Request access to<br>
                        your internal systems
                    </h1>
                    <p class="landing-description mb-4">
                        A single, streamlined portal for requesting access to ATM, MSP, Core 3.0, FTP, and other
                        internal systems. Your request is securely logged and routed for approval.
                    </p>

                    <div class="landing-highlights mb-4">
                        <div class="landing-highlight">
                            <span class="landing-highlight-icon">✓</span>
                            <span>Finish in <strong>3–5 minutes</strong></span>
                        </div>
                        <div class="landing-highlight">
                            <span class="landing-highlight-icon">✓</span>
                            <span>Clear steps and required details</span>
                        </div>
                        <div class="landing-highlight">
                            <span class="landing-highlight-icon">✓</span>
                            <span>Automatically routed for review</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        @php($recaptchaSiteKey = (string) config('services.recaptcha.site_key', ''))
                        <form
                            action="{{ route('captcha.verify') }}"
                            method="POST"
                            class="landing-captcha-form"
                        >
                            @csrf
                            <div class="landing-captcha-card">
                                @if(session('error'))
                                    <div class="alert alert-danger py-2 mb-3">{{ session('error') }}</div>
                                @endif
                                @if($recaptchaSiteKey === '')
                                    <div class="alert alert-warning py-2 mb-0">
                                        CAPTCHA is not configured. Set <code>RECAPTCHA_SITE_KEY</code> and <code>RECAPTCHA_SECRET_KEY</code> in <code>.env</code>.
                                    </div>
                                @else
                                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                                @endif
                                <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg landing-cta" @disabled($recaptchaSiteKey === '')>
                                        Continue to Request Form
                                    </button>
                                    <span class="landing-secondary-text">
                                        No login required · For staff only
                                    </span>
                                </div>
                            </div>
                        </form>
                        <span class="landing-secondary-text">
                            
                        </span>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="landing-visual shadow-sm">
                        <div class="landing-visual-header d-flex justify-content-between align-items-center mb-3">
                            <div class="landing-visual-dots">
                                <span></span><span></span><span></span>
                            </div>
                            <span class="landing-visual-label">User Request Preview</span>
                        </div>
                        <div class="landing-visual-body">
                            <div class="landing-visual-line wide"></div>
                            <div class="landing-visual-line"></div>
                            <div class="landing-visual-line"></div>
                            <div class="landing-visual-tags">
                                <span>ATM</span>
                                <span>MSP</span>
                                <span>Core 3.0</span>
                                <span>FTP</span>
                            </div>
                            <div class="landing-visual-footer">
                                <span class="landing-visual-pill">Step 1 of 3</span>
                                <span class="landing-visual-status">Ready to start</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @if($recaptchaSiteKey !== '')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
</body>
</html>
