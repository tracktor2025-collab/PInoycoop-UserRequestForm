# Web User Request

Laravel web application for submitting and managing **user access requests** to internal systems (cooperative name/branch, requested portals, approval workflow). Public users complete Google reCAPTCHA, fill a form, and receive confirmation; **admins** sign in with **two-factor authentication**, review requests, and approve or reject them.

## Requirements

- **PHP** 8.2+
- **Composer**
- **MySQL** (or MariaDB compatible with Laravel’s MySQL driver)
- **Node.js** (for building frontend assets with Vite)

Optional:

- **Docker** — see [README_DOCKER.md](README_DOCKER.md) for containerized run instructions.

## Installation

### 1. Clone and install dependencies

From the project root:

```bash
composer install
npm install
```

### 2. Environment file

Copy the example environment file and generate an application key:

```bash
copy .env.example .env
```

On Linux/macOS: `cp .env.example .env`

```bash
php artisan key:generate
```

Edit `.env` and set at least:

| Variable | Purpose |
|----------|---------|
| `APP_URL` | Base URL of the app (e.g. `http://localhost:8000` or your XAMPP vhost) |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_HOST` | MySQL connection |
| `MAIL_*`, `IT_DEPARTMENT_EMAIL` | SMTP and IT notification recipient |
| `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY` | Google reCAPTCHA v2 (checkbox). See [README_CAPTCHA.md](README_CAPTCHA.md) |
| `GOOGLE_*` | Optional Google Sheets integration. See [README_GOOGLE_SHEETS.md](README_GOOGLE_SHEETS.md) |

Create the MySQL database named in `DB_DATABASE` before migrating.

**Security:** Replace placeholder or sample keys in `.env` with your own; never commit real secrets.

### 3. Database

```bash
php artisan migrate
```

### 4. First admin account (optional)

The project can create an initial admin from environment variables (no passwords stored in code). In `.env` set:

```env
ADMIN_EMAIL=your-admin@example.com
ADMIN_INITIAL_PASSWORD=your-secure-password
ADMIN_NAME=Administrator
ADMIN_ROLE=super_admin
```

Then run:

```bash
php artisan db:seed --class=AdminSeeder
```

You can remove `ADMIN_INITIAL_PASSWORD` from `.env` after the account exists if you prefer.

### 5. Frontend assets

Development (hot reload):

```bash
npm run dev
```

Production build:

```bash
npm run build
```

### 6. Google Sheets (optional)

If you use Sheets: place the service account JSON where `GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION` points (e.g. `storage/app/google/service-account.json`), enable APIs, and share the spreadsheet with the service account email. Full steps: [README_GOOGLE_SHEETS.md](README_GOOGLE_SHEETS.md).

---

## Running the application

### Option A: Laravel development server + queue + Vite (recommended for local dev)

This runs HTTP, a queue worker, and the Vite dev server together:

```bash
composer run dev
```

Then open `APP_URL` (default `http://localhost:8000`).

### Option B: PHP server only

```bash
php artisan serve
```

Run `npm run dev` in another terminal if you are changing CSS/JS.

### Option C: XAMPP / Apache

Point the document root to the project’s `public/` directory (or configure a virtual host), ensure `mod_rewrite` is enabled, and set `APP_URL` to match your local URL.

### Queues

Submission handling uses a queued job (`HandleAccessRequestSubmissionJob`). With `QUEUE_CONNECTION=sync` in `.env` (default in many setups), jobs run immediately in the same process. For background processing, set `QUEUE_CONNECTION=database` (after `php artisan queue:table` + migrate) or `redis`, then run:

```bash
php artisan queue:work
```

The `composer run dev` script starts `queue:listen` for you.

---

## How to use

### Public — submit an access request

1. Open the site root `/` (landing page).
2. Complete **reCAPTCHA** and continue to the request form (`/request`).
3. Fill in personal/cooperative details and select **systems** (ATM Portal, CORE 3.0, etc.).
4. Submit. You are redirected to a **success** page with a summary; you can download a **PDF** copy when available.

Submissions are stored in the database; if configured, rows are appended to **Google Sheets** and emails go to **IT** and the requester.

### Admin — review and approve

1. Go to **`/admin/login`** (super admins can also use **`/super-admin/login`** for the dedicated entry).
2. Sign in with email and password, then complete **two-factor authentication** (TOTP; e.g. Google Authenticator). First-time users complete QR/setup once.
3. Use the **dashboard** to browse requests; open **Approvals** to filter by status (pending / approved / rejected).
4. To **approve** from a non-approved state, upload a **signed approval** document (PDF or image) when required by the workflow.
5. **Super admins** can manage other admins under **Account → Admins**, view **system** pages, and **audit trail** (see routes in `routes/web.php`).

More detail on 2FA and security headers: [README_ADMIN_SECURITY.txt](README_ADMIN_SECURITY.txt).

---

## Composer shortcuts

| Command | Description |
|---------|-------------|
| `composer run setup` | Install deps, copy `.env` if missing, key, migrate, npm install, npm build |
| `composer run dev` | `serve` + `queue:listen` + `npm run dev` |
| `composer run test` | Clear config cache and run PHPUnit/Pest tests |

---

## Docker

Quick start:

```bash
docker compose up -d --build
docker compose exec app php artisan migrate
```

Details: [README_DOCKER.md](README_DOCKER.md).

---

## Additional documentation

| File | Topic |
|------|--------|
| [README_INSTALLATION_AND_USAGE.md](README_INSTALLATION_AND_USAGE.md) | **Detailed** installation steps and how to use (public + admin) |
| [DOCUMENTATION_PRESENTATION_OUTLINE.md](DOCUMENTATION_PRESENTATION_OUTLINE.md) | PowerPoint-style outline (aligned with structured tech decks: overview → components → deploy → prereqs → install → integrations → security) |
| [README_CAPTCHA.md](README_CAPTCHA.md) | reCAPTCHA keys and troubleshooting |
| [README_GOOGLE_SHEETS.md](README_GOOGLE_SHEETS.md) | Service account, spreadsheet sharing, column mapping |
| [README_ADMIN_SECURITY.txt](README_ADMIN_SECURITY.txt) | Admin 2FA, rate limits, security headers |
| [README_DEPLOY_DOCKER_OTHER_LAPTOP.md](README_DEPLOY_DOCKER_OTHER_LAPTOP.md) | Deploy notes (if present in your tree) |

---

## Health check

Laravel exposes **`/up`** for basic availability checks.

---

## License

This application builds on [Laravel](https://laravel.com), which is open-sourced software under the [MIT license](https://opensource.org/licenses/MIT).
