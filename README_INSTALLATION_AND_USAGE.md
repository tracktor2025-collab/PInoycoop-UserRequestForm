# Installation and usage guide

This document is the step-by-step guide for installing the Web User Request application and using it after it runs. For a short project overview, see the main [README.md](README.md). Deep dives: [README_CAPTCHA.md](README_CAPTCHA.md), [README_GOOGLE_SHEETS.md](README_GOOGLE_SHEETS.md), [README_ADMIN_SECURITY.txt](README_ADMIN_SECURITY.txt), [README_DOCKER.md](README_DOCKER.md).

---

## Part 1 — Installation process

### What you need first (prerequisites)

| Item | Why it matters |
|------|----------------|
| PHP 8.2+ with common extensions (mbstring, openssl, pdo_mysql, etc.) | Laravel 12 requires it |
| Composer | Installs PHP dependencies |
| MySQL (or compatible) | Stores access requests, admins, audit logs |
| Node.js + npm | Builds CSS/JS (Vite) |
| Git (optional) | If you clone the repository |

Important: Create an empty MySQL database before running migrations. The name must match `DB_DATABASE` in `.env`.

---

### Installation steps (order matters)

#### Step 1 — Get the code

Clone or copy the project folder to your machine (e.g. XAMPP `htdocs`, or any path).

#### Step 2 — Install PHP and JavaScript dependencies

Open a terminal in the project root (same folder as `composer.json`):

```bash
composer install
npm install
```

Important: If `composer install` fails, fix PHP version or missing extensions first; do not skip this step.

#### Step 3 — Create the environment file

- Windows (cmd): `copy .env.example .env`
- PowerShell / Linux / macOS: `cp .env.example .env`

Generate the application encryption key:

```bash
php artisan key:generate
```

Important: `APP_KEY` is required for sessions and for encrypting admin 2FA secrets. Do not share it or change it lightly on a server that already has data.

#### Step 4 — Configure `.env` (minimum required)

Edit `.env` and set at least the following. Adjust every value to your environment.

| Setting | What to set |
|---------|-------------|
| `APP_URL` | Full base URL users will use, e.g. `http://localhost:8000` or `https://your-domain.com` |
| `APP_ENV` / `APP_DEBUG` | Use `local` + `true` for development; for production use `production` and `false` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Your MySQL database and credentials |
| MAIL settings in `.env` | SMTP server for sending mail |
| `IT_DEPARTMENT_EMAIL` | Where new submission notifications are sent |
| `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY` | Google reCAPTCHA v2 checkbox keys (see [README_CAPTCHA.md](README_CAPTCHA.md)) |

Important: Do not use example or leaked keys in production. Register your own reCAPTCHA keys and add your real domain (and `localhost` for local testing) in the Google reCAPTCHA admin console.

#### Step 5 — Run database migrations

```bash
php artisan migrate
```

This creates tables for access requests, admins, audit logs, jobs, etc.

Important: If migration fails, check database name, user permissions, and that MySQL is running.

#### Step 6 — (Optional) Create the first administrator

This project does not hard-code admin passwords in the repository. Set these in `.env`:

```env
ADMIN_EMAIL=your-admin@example.com
ADMIN_INITIAL_PASSWORD=your-secure-temporary-password
ADMIN_NAME=Administrator
ADMIN_ROLE=super_admin
```

Then run:

```bash
php artisan db:seed --class=AdminSeeder
```

Important: After the account exists, you may remove `ADMIN_INITIAL_PASSWORD` from `.env` and change the password from the app. The first login will require 2FA setup (see Part 2).

#### Step 7 — Build or run frontend assets

- Local development (live reload): `npm run dev`
- Production or “no dev server”: `npm run build`

Important: If styles or scripts look broken, you likely need `npm run build` or a running `npm run dev` with Vite.

#### Step 8 — (Optional) Google Sheets

If you need submissions written to a spreadsheet:

1. Create a Google Cloud project, enable Sheets and Drive APIs, create a service account, download the JSON key.
2. Store the file where `GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION` points (e.g. `storage/app/google/service-account.json`).
3. Set `GOOGLE_SPREADSHEET_ID`, `GOOGLE_SERVICE_ENABLED=true`, and share the spreadsheet with the service account email (Editor).
4. Run `php artisan config:clear` after changing `.env`.

Important: Full column and tab rules are in [README_GOOGLE_SHEETS.md](README_GOOGLE_SHEETS.md).

#### Step 9 — Start the application

Choose one approach:

| Method | Command / action |
|--------|------------------|
| All-in-one local dev (recommended) | `composer run dev` — runs PHP server, queue listener, and Vite together |
| PHP only | `php artisan serve` — use another terminal for `npm run dev` if you edit assets |
| XAMPP / Apache | Point the vhost to the `public` folder, enable URL rewriting, set `APP_URL` to match |
| Docker | See [README_DOCKER.md](README_DOCKER.md) |

#### Step 10 — Queues (important for reliability)

Form submission dispatches a job that talks to Google Sheets and sends emails.

- If `QUEUE_CONNECTION=sync` (common in `.env`), work runs during the web request (simpler, slower).
- For background processing, configure `database` or `redis` queue driver and run:

```bash
php artisan queue:work
```

`composer run dev` already runs a queue listener for you.

---

### After installation — quick verification

1. Open `APP_URL` in a browser — you should see the landing page.
2. Complete reCAPTCHA and reach `/request` — form should load.
3. Open `/admin/login` — login page should load (sign-in requires a seeded or existing admin).

Health check URL: `/up` (Laravel built-in).

---

## Part 2 — How to use this project

### A. Public user — submit an access request

| Step | Action |
|------|--------|
| 1 | Go to the site home page (`/`). |
| 2 | Complete reCAPTCHA and proceed so the session allows the form. |
| 3 | Open the request form (`/request`). Fill personal/cooperative fields and tick the systems you need (ATM Portal, CORE 3.0, etc.). |
| 4 | Submit the form. |
| 5 | On success, review the summary; download PDF from the success flow if offered (`/success/pdf`). |

What happens in the background: The request is saved in the database; IT (and optionally the requester) may receive email; if configured, rows are written to Google Sheets.

---

### B. Administrator — login and approvals

| Step | Action |
|------|--------|
| 1 | Go to `/admin/login`. (Super admins may also use `/super-admin/login`.) |
| 2 | Enter email and password. |
| 3 | Complete two-factor authentication (TOTP app: Google Authenticator, Microsoft Authenticator, etc.). First login includes one-time QR / secret setup. |
| 4 | Use Dashboard to see requests; use Approvals to list by status (pending / approved / rejected / all). |
| 5 | To change status: open a request, set approved/rejected/pending as allowed, add remarks if needed. Approving from a non-approved state typically requires uploading a signed approval file (PDF or image) the first time. |
| 6 | Super admins can open Account → Admins to add admins (your 2FA code may be required), and System / Audit trail for monitoring. |

Important security notes:

- Admins must not share authenticator secrets or reuse passwords across sites.
- See [README_ADMIN_SECURITY.txt](README_ADMIN_SECURITY.txt) for 2FA flow, rate limits, and HTTP headers.

---

### Important URLs (reference)

| Path | Purpose |
|------|---------|
| `/` | Landing + CAPTCHA |
| `/request` | Access request form (requires CAPTCHA session) |
| `/success` | Submission confirmation |
| `/admin/login` | Admin login |
| `/admin/dashboard` | Admin dashboard (after login + 2FA) |
| `/admin/approvals` | Approval queue (standard admin) |
| `/super-admin/login` | Alternate entry for super-admin login flow |
| `/up` | Application health check |

Exact menu labels match the Blade templates under `resources/views/admin/`.

---

### Problems people hit most often

| Symptom | What to check |
|---------|----------------|
| CAPTCHA / cannot reach form | RECAPTCHA settings in `.env`, domain allowed in Google console, `php artisan config:clear` |
| Session lost / kicked to landing | `APP_URL` matches browser URL, cookies/sessions work, `SESSION_DRIVER` |
| Mail not sent | MAIL settings in `.env`, firewall, app passwords for Gmail, queue worker if not `sync` |
| Sheets not updating | Service account JSON path, spreadsheet shared with service account email, APIs enabled |
| 500 errors after deploy | `APP_DEBUG=true` temporarily to log path; fix permissions on `storage/` and `bootstrap/cache/` |

---

## Related files

| File | Content |
|------|---------|
| [README.md](README.md) | Project summary and index |
| [README_CAPTCHA.md](README_CAPTCHA.md) | reCAPTCHA setup |
| [README_GOOGLE_SHEETS.md](README_GOOGLE_SHEETS.md) | Google Sheets setup |
| [README_ADMIN_SECURITY.txt](README_ADMIN_SECURITY.txt) | Admin 2FA and security |
| [README_DOCKER.md](README_DOCKER.md) | Docker |
