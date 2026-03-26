================================================================================
  ADMIN TWO-FACTOR AUTHENTICATION (2FA) AND SECURITY
  Web User Request (Laravel)
================================================================================

This document describes how administrator two-factor authentication works and
what additional security measures are applied in this project.


--------------------------------------------------------------------------------
INSTALLATION: WHAT YOU INSTALL (SERVER, COMPOSER, DATABASE)
--------------------------------------------------------------------------------

Requirements on the machine that runs this app
  - PHP 8.2 or newer (matches composer.json: "php": "^8.2").
  - Composer (https://getcomposer.org/) to download PHP libraries.
  - A database Laravel can use (e.g. MySQL / MariaDB) with settings in .env.

One-time project setup (from the project root folder)
  1. composer install
     Downloads everything in composer.json into vendor/, including the 2FA
     libraries below.
  2. cp .env.example .env   (Windows: copy .env.example .env)
  3. php artisan key:generate
     Needed for encrypting secrets in the database (e.g. two_factor_secret).
  4. Edit .env: set DB_* connection values and APP_URL.
  5. php artisan migrate
     Creates/updates tables, including "admins" and the two-factor columns.

PHP package used for admin 2FA (installed by Composer automatically)
  - pragmarx/google2fa (^9.x)
    Listed in composer.json under "require". Implements TOTP (the same
    algorithm used by Google Authenticator and similar apps).
  - Dependency pulled in with it: paragonie/constant_time_encoding
    (installed automatically; you do not add it manually).

If pragmarx/google2fa is missing from vendor/
  - Run: composer require pragmarx/google2fa
    Or: composer install (if composer.json already lists it).
  - Then: composer dump-autoload
    If classes still do not load, this project includes a backup autoloader in
    App\Providers\AppServiceProvider that loads PragmaRX\Google2FA\* from
    vendor/pragmarx/google2fa/src/ when the file exists.

Database fields (after migrate)
  - admins.two_factor_secret   (stored ciphertext; Laravel encrypts/decrypts)
  - admins.two_factor_confirmed_at   (when enrollment was completed)

What is not installed on the server for 2FA
  - No separate Google "2FA appliance". All TOTP checks run in your PHP app.
  - The setup page shows a QR code using an image URL (QuickChart) loaded by
    the admin's browser; manual entry of the secret works without that image.
  - Security HTTP headers and rate limiting use only Laravel and PHP; no extra
    Composer packages were added specifically for the SecurityHeaders
    middleware.

On each admin's phone or tablet (for end users, not the server)
  - Install a free authenticator app from the app store, e.g.:
    Google Authenticator, Microsoft Authenticator, or Authy.


--------------------------------------------------------------------------------
STEP-BY-STEP: ACTIVATING 2FA ON YOUR ADMIN ACCOUNT
--------------------------------------------------------------------------------

What you need first
  - An authenticator app on your phone or tablet (examples: Google
    Authenticator, Microsoft Authenticator, Authy).
  - A valid admin login in this app (email + password that exist in the
    "admins" database table).
  - Browser URL: your site address + /admin/login
    Example locally: http://127.0.0.1:8000/admin/login

First-time activation (enrollment) -- follow in order

  1. Open /admin/login in the browser.
  2. Enter your admin email and password, then sign in.
  3. The app does NOT open the dashboard yet. You are redirected to a page
     titled something like "Set up two-factor authentication". That is
     correct: passwords alone do not finish login until 2FA is done.
  4. On your phone, open the authenticator app.
  5. Add a new account by either:
     - Scanning the QR code displayed on the setup page, OR
     - Choosing "Enter a setup key" (or similar) and typing the long secret
       key shown in plain text on the same page (same data as the QR).
  6. The app will show a 6-digit code that refreshes about every 30 seconds.
  7. Type the current 6-digit code into the form field on the setup page.
  8. Click the button to confirm (e.g. "Confirm and continue").
  9. If the code is valid, you are logged in and taken to the admin dashboard.
     Your account now has 2FA enabled and saved in the database.

Every login after that

  1. Open /admin/login, enter email and password.
  2. You are taken to "Two-factor authentication" (challenge screen).
  3. Open your authenticator app and enter the current 6-digit code.
  4. Submit. You then reach the dashboard. No new QR scan is required.

Brand-new admin created by another admin (Admin accounts page)

  1. First sign-in is the same as "First-time activation": email + password,
     then you MUST complete the setup page (QR or manual key + first code).
  2. Until that is done, the account is not considered fully enrolled.

There is no separate "toggle 2FA on" in settings: enrollment happens
automatically on first successful login after the password step.


--------------------------------------------------------------------------------
1. HOW 2FA IS APPLIED (ADMIN LOGIN) -- TECHNICAL SUMMARY
--------------------------------------------------------------------------------

Technology
  - TOTP (Time-based One-Time Password), compatible with apps such as:
    Google Authenticator, Microsoft Authenticator, Authy, etc.
  - PHP package: pragmarx/google2fa (Composer dependency).

Login flow (high level)
  1. Admin opens /admin/login and enters email + password.
  2. If credentials are valid, the session does NOT yet grant full access.
     Instead, a "pending 2FA" state is stored (pending_2fa_admin_id).
  3. One of two paths:
     a) First-time / not enrolled yet:
        - Redirect to /admin/two-factor/setup
        - A secret key is generated; a QR code is shown (via QuickChart URL) and
          the manual secret is shown for manual entry.
        - Admin enters a 6-digit code from the authenticator app to confirm.
        - On success: secret is stored encrypted in the database
          (admins.two_factor_secret), and two_factor_confirmed_at is set.
     b) Already enrolled:
        - Redirect to /admin/two-factor/challenge
        - Admin enters the current 6-digit code from the app.
  4. After successful setup or challenge, the session receives admin_id and the
     admin can use the dashboard. Pending 2FA keys are cleared.

Storage
  - two_factor_secret: stored encrypted (Laravel encrypted cast).
  - two_factor_confirmed_at: timestamp when enrollment was completed.

Important routes
  - GET  /admin/two-factor/setup
  - POST /admin/two-factor/setup (confirm enrollment)
  - GET  /admin/two-factor/challenge
  - POST /admin/two-factor/challenge (verify code)

Middleware
  - admin.pending-2fa: ensures only users with a valid pending 2FA session
    can access the setup/challenge pages.
  - admin.auth: ensures only fully logged-in admins (after 2FA) access the
    dashboard and account pages.


--------------------------------------------------------------------------------
2. 2FA WHEN CREATING ANOTHER ADMIN ACCOUNT
--------------------------------------------------------------------------------

In Admin accounts (/admin/account/admins), creating a new admin requires:
  - The signed-in admin must have completed 2FA (hasEnabledTwoFactor).
  - A 6-digit authenticator code field must be submitted with the form.
  - That code is verified against the current admin's TOTP secret before the
    new admin record is created.

New admins created this way still have no 2FA secret until they sign in
the first time; they must complete the same enrollment (QR + confirm) on
first login.


--------------------------------------------------------------------------------
3. OTHER SECURITY MEASURES IN THIS PROJECT
--------------------------------------------------------------------------------

A. HTTP security headers (middleware: SecurityHeaders)
   Applied to the "web" middleware group (typical browser pages).

   - X-Frame-Options: SAMEORIGIN
     Reduces clickjacking risk (page not framed by other sites easily).

   - X-Content-Type-Options: nosniff
     Reduces MIME-type confusion attacks.

   - Referrer-Policy: strict-origin-when-cross-origin
     Limits referrer information sent on cross-origin requests.

   - Permissions-Policy
     Disables several browser features by default for the page.

   - Strict-Transport-Security (HSTS)
     Only when APP_ENV is production AND the request uses HTTPS.
     Tells browsers to use HTTPS for this site for a long period.

   File: app/Http/Middleware/SecurityHeaders.php
   Registered in: bootstrap/app.php (appended to web middleware).


B. Rate limiting (throttling)
   Defined in: app/Providers/AppServiceProvider.php (RateLimiter::for ...)

   - admin-login (POST /admin/login)
     Up to 12 attempts per minute per IP + email combination.

   - admin-two-factor (POST setup / challenge verification)
     Up to 30 requests per minute per IP.

   - public-submit (POST /submit-request)
     Up to 40 requests per hour per IP.

   - captcha-verify (POST /verify-captcha)
     Up to 30 requests per minute per IP.

   - admin-action (selected admin POST actions)
     Up to 120 requests per minute per IP + signed-in admin id.
     Used for: approval updates, email change, etc.

   (Too many attempts typically return HTTP 429 Too Many Requests.)

C. Laravel defaults (still in effect)
   - CSRF protection on web forms (VerifyCsrfToken).
   - Session-based admin authentication after password + 2FA.
   - Passwords hashed (bcrypt/argon2 via Laravel hashing).
   - Admin session cleared on logout; identifiers regenerated where appropriate.

D. Composer autoload note (Google2FA)
   If Google2FA classes fail to load after a broken vendor install, the project
   registers a fallback PSR-4 autoloader in AppServiceProvider for
   PragmaRX\Google2FA\ from vendor/pragmarx/google2fa/src/.
   Running "composer dump-autoload" is still recommended for a clean fix.


--------------------------------------------------------------------------------
4. CONFIGURATION TIPS
--------------------------------------------------------------------------------

  - APP_ENV=production and HTTPS: use a real TLS certificate and
    APP_URL=https://... so HSTS and secure cookies behave as intended.

  - APP_DEBUG=false in production to avoid leaking stack traces.

  - Keep APP_KEY stable; encrypted admin fields (e.g. two_factor_secret)
    depend on it.


--------------------------------------------------------------------------------
5. FILE QUICK REFERENCE
--------------------------------------------------------------------------------

  app/Http/Middleware/EnsureAdminAuthenticated.php
  app/Http/Middleware/EnsurePendingAdminTwoFactor.php
  app/Http/Middleware/SecurityHeaders.php
  app/Http/Controllers/AdminTwoFactorController.php
  app/Http/Controllers/AdminAccountController.php
  app/Models/Admin.php
  app/Providers/AppServiceProvider.php
  bootstrap/app.php
  routes/web.php


================================================================================
  End of README_ADMIN_SECURITY.txt
================================================================================
