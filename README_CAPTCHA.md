# CAPTCHA (reCAPTCHA) Activation

This Laravel app uses **Google reCAPTCHA v2 ("I'm not a robot" checkbox)** to protect access to the internal request form.

## How CAPTCHA is wired in this system

- **Landing page (`/`)**: renders the reCAPTCHA checkbox widget.
- **Verify endpoint (`POST /verify-captcha`)**: sends the reCAPTCHA token to Google and checks the `success` response.
- **Protected page (`/request`)**: guarded by the middleware alias `landing.captcha`.
- After successful verification, the app stores a session flag: `landing_captcha_verified`.

## Step 1: Create reCAPTCHA keys in Google

1. Go to the **Google reCAPTCHA admin console**.
2. Create a new reCAPTCHA:
   - Type: **reCAPTCHA v2 - "I'm not a robot" (checkbox)**
3. Add allowed domains for your environment:
   - For local dev, include `localhost` (and/or the hostname you use).
   - For server deployments, include your actual domain.
4. Copy:
   - **Site key**
   - **Secret key**

## Step 2: Configure `.env`

Edit your project `.env` file and set:

- `RECAPTCHA_SITE_KEY=YOUR_SITE_KEY_HERE`
- `RECAPTCHA_SECRET_KEY=YOUR_SECRET_KEY_HERE`

Notes:

- If you use `APP_ENV=local` and leave keys empty, this project falls back to Google’s official reCAPTCHA v2 test keys.

## Step 3: Clear config cache + restart

After updating `.env`, run:

```bash
php artisan config:clear
php artisan cache:clear
```

Then restart your app server (examples):

- If using Laravel’s dev server: re-run `php artisan serve`
- If using Apache/XAMPP: restart Apache

## Step 4: Test the activation

1. Open the landing page: `/`
2. Confirm the “Continue to Request Form” button is enabled.
3. Check the **“I’m not a robot”** box, then click **Continue to Request Form**.
4. Verify you are redirected to `/request`.

If it fails, you will see an error message on the landing page (set by the verify handler).

## Troubleshooting

1. **CAPTCHA widget does not show / button is disabled**
   - The app checks whether `config('services.recaptcha.site_key')` is set.
   - Ensure `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` are present in `.env`.

2. **“CAPTCHA verification failed”**
   - The reCAPTCHA keys might not match / the allowed domains in Google reCAPTCHA might be incorrect.

3. **You pass CAPTCHA but still get redirected back to `/`**
   - The middleware checks the session flag `landing_captcha_verified`.
   - Confirm sessions are working and not being blocked (this project uses `SESSION_DRIVER=file`).

