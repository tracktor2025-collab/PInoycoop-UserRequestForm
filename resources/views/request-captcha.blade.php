<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Access Request Portal</title>
    <link rel="stylesheet" href="{{ asset('pinooycoop/plugins/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('pinooycoop/plugins/fontawesome/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('pinooycoop/plugins/icofont/icofont.css') }}">
    <link rel="stylesheet" href="{{ asset('pinooycoop/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        .request-captcha-page {
            padding-top: 96px;
        }

        .request-captcha-page .landing-shell {
            min-height: calc(100vh - 96px);
        }

        .request-captcha-page .landing-header {
            display: none;
        }

        .request-captcha-page .landing-action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body class="request-captcha-page" data-spy="scroll" data-target="#mainNav">
    @php($recaptchaSiteKey = (string) config('services.recaptcha.site_key', ''))

    @include('pinooycoop.partials.nav')

    <div class="landing-shell">
        <header class="landing-header">
            <div class="container landing-main">
                <div class="landing-topbar">
                    <a class="landing-brand" href="{{ route('landing') }}" aria-label="MASS-SPECC User Access Request Portal">
                        <img
                            src="{{ asset('MASS-SPECC Logo/MASS-SPECC LOGO 2.png') }}"

                            class="landing-logo"
                            decoding="async"
                            loading="lazy"
                        >
                    </a>

                    <nav class="landing-nav">
                        <a class="landing-nav-link" href="{{ route('admin.login.form') }}">Admin login</a>
                    </nav>
                </div>
            </div>
        </header>

        <main class="landing-hero">
            <div class="container landing-main">
                <div class="row align-items-start justify-content-center g-5">
                    <div class="col-12 col-lg-6">
                        <div class="landing-kicker mb-3">
                            
                        </div>

                        <h1 class="landing-title mb-3">User Access Request</h1>
                        <p class="landing-subtitle mb-4">Internal portal</p>

                        <div class="landing-action">
                            <form action="{{ route('captcha.verify') }}" method="POST" class="landing-captcha-form">
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

                                    <div class="landing-action-row">
                                        <button type="submit" class="btn btn-primary btn-lg landing-cta" @disabled($recaptchaSiteKey === '')>
                                            Start request
                                        </button>
                                        <span class="landing-secondary-text">CAPTCHA required</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <div class="landing-visual shadow-sm">
                            <div class="landing-visual-header d-flex justify-content-between align-items-center mb-3">
                                <div class="landing-visual-dots" aria-hidden="true">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="landing-visual-label">Systems</span>
                            </div>
                            <div class="landing-visual-body">
                                <div class="landing-visual-line wide"></div>
                                <div class="landing-visual-line"></div>
                                <div class="landing-visual-line"></div>
                                <div class="landing-visual-tags" aria-label="Example systems">
                                    <span>ATM</span>
                                    <span>MSP</span>
                                    <span>Core 3.0</span>
                                    <span>FTP</span>
                                </div>
                                <div class="landing-visual-footer">
                                    <span class="landing-visual-pill">Portal</span>
                                    <span class="landing-visual-status">Ready</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @include('pinooycoop.partials.footer')

    <script src="{{ asset('pinooycoop/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('pinooycoop/plugins/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('pinooycoop/js/script.js') }}"></script>
    @if($recaptchaSiteKey !== '')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
</body>
</html>
