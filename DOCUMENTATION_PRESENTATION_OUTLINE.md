# Web User Request — documentation presentation outline

This outline follows the same **documentation style** as the reference deck *Wazuh-Documentation.pptx* (title + product overview, core components with roles, deployment options, prerequisite tables, required software, network ports, step-by-step installation, first access, then integration and security topics).  
Use it to build a **PowerPoint** or **Word** document: each `##` is a suggested slide title; bullets are on-slide or speaker notes.

**Related written docs:** [README.md](README.md), [README_INSTALLATION_AND_USAGE.md](README_INSTALLATION_AND_USAGE.md), [README_CAPTCHA.md](README_CAPTCHA.md), [README_GOOGLE_SHEETS.md](README_GOOGLE_SHEETS.md), [README_ADMIN_SECURITY.txt](README_ADMIN_SECURITY.txt), [README_DOCKER.md](README_DOCKER.md).

### Short project overview

**Web User Request** is a Laravel web application for organizations that need a controlled way to **collect and process internal user access requests** (cooperative, branch, requested systems, and access type). Public users complete **Google reCAPTCHA**, submit a structured form, and receive confirmation (including optional **PDF**). **Administrators** sign in with **two-factor authentication**, review the queue, and **approve or reject** requests. The system persists data in **MySQL**, can notify via **SMTP**, optionally sync to **Google Sheets**, and records actions in an **audit trail**—suited to **MASS-SPECC** or similar governance contexts.

---

## Slide 1 — Title

- **Web User Request** (MASS-SPECC / user access request system)
- Subtitle: *Installation, usage, and operations*
- Organization / team / date (fill in)

---

## Slide 2 — Overview

**Web User Request** is a web application built on Laravel. It lets users submit **structured access requests** (cooperative, branch, systems, access type) after **reCAPTCHA** verification. **Administrators** sign in with **two-factor authentication**, review requests, and **approve** or **reject** them, with optional **email** and **Google Sheets** integration and an **audit trail** for accountability.

---

## Slide 3 — Technology stack

- **Backend:** PHP 8.2+, Laravel 12
- **Database:** MySQL
- **Frontend build:** Vite, Tailwind CSS, Node.js
- **PDF:** DomPDF
- **Optional:** Google reCAPTCHA v2, Google Sheets (service account), SMTP mail

---

## Slide 4 — Core components: the “big three”

The system is split into clear layers from the browser to storage and integrations.

| Component | Role | Description |
|-----------|------|-------------|
| **Laravel application** | The engine | Handles HTTP routes, validation, sessions, admin auth, 2FA, jobs, and mail. |
| **MySQL** | The vault | Stores access requests, admin accounts, audit logs, and queue jobs (if used). |
| **Public + admin UI** | The face | Blade pages: landing + CAPTCHA, user form, success/PDF, admin dashboard and approvals. |

---

## Slide 5 — Optional integration services

| Service | Role |
|---------|------|
| **Google reCAPTCHA** | Protects the request form from bots; session flag after verify. |
| **SMTP / mail** | Notifies IT and the requester; sends status updates on approval/rejection. |
| **Google Sheets** | Appends submission rows and can sync approval fields per tab mapping. |

---

## Slide 6 — Deployment options

- **Local development (all-in-one):** `composer run dev` (PHP server + queue listener + Vite).
- **PHP only:** `php artisan serve` (+ separate `npm run dev` for assets).
- **XAMPP / Apache:** document root = `public/`, `mod_rewrite`, `APP_URL` matches vhost.
- **Docker:** `docker compose` (app on **8000**, MySQL on host **3307** by default) — see [README_DOCKER.md](README_DOCKER.md).

---

## Slide 7 — Deployment: Docker (single stack)

- One **app** container (Laravel) and one **MySQL** container.
- Persistent volume for database data.
- After `up`: run `php artisan migrate` inside the app container.
- Align `.env` / compose env with database name and credentials.

---

## Slide 8 — Prerequisites — application host

| Requirement | Notes |
|-------------|--------|
| **OS** | Windows (XAMPP), Linux, or macOS for dev |
| **PHP** | 8.2 or newer with extensions typical for Laravel (mbstring, openssl, pdo_mysql, …) |
| **Composer** | For PHP dependencies |
| **MySQL** | Empty database created before migrate |
| **Node.js + npm** | For `npm install` / `npm run build` |

---

## Slide 9 — Prerequisites — suggested resources

| Resource | Minimum guidance (adjust per load) |
|----------|-------------------------------------|
| **CPU** | 2+ cores for comfortable dev |
| **RAM** | 4 GB+ for PHP + MySQL + Node |
| **Disk** | Space for `vendor/`, `node_modules/`, DB, uploaded approval files |

*(Production sizing depends on traffic and retention policy.)*

---

## Slide 10 — Required software

- **Git** (optional) — clone or copy project files
- **PHP** + **Composer**
- **MySQL** (or compatible)
- **Docker Desktop / Engine** — only if using Docker deployment

---

## Slide 11 — Exposed ports (reference)

| Port | When |
|------|------|
| **8000** | Common Laravel dev server / Docker app mapping |
| **80 / 443** | Apache or IIS in production (HTTPS recommended) |
| **3306** | MySQL (local install) |
| **3307** | Host mapping for MySQL in included `docker-compose.yml` |

*Firewall rules should only expose what you intend (especially 443 for HTTPS in production).*

---

## Slide 12 — Installation — dependencies

From project root:

```bash
composer install
npm install
```

---

## Slide 13 — Installation — environment file

- Copy `.env.example` to `.env`
- Run `php artisan key:generate`
- Edit `.env`: `APP_URL`, `DB_*`, `MAIL_*`, `IT_DEPARTMENT_EMAIL`, `RECAPTCHA_*`, optional `GOOGLE_*`

**Important:** use real keys for production; never commit secrets.

---

## Slide 14 — Installation — database

```bash
php artisan migrate
```

Creates tables for requests, admins, audit logs, jobs, etc.

---

## Slide 15 — Installation — first administrator

Set in `.env` (no passwords in source code):

- `ADMIN_EMAIL`, `ADMIN_INITIAL_PASSWORD`, `ADMIN_NAME`, `ADMIN_ROLE`

Run:

```bash
php artisan db:seed --class=AdminSeeder
```

Remove `ADMIN_INITIAL_PASSWORD` from `.env` after first use if desired.

---

## Slide 16 — Installation — frontend assets

- Development: `npm run dev`
- Production build: `npm run build`

---

## Slide 17 — Default / first access (concept)

- **Public:** open `APP_URL` → landing → CAPTCHA → `/request`
- **Admin:** `/admin/login` → password → **2FA** setup (first time) or challenge
- No fixed default password in repo — only what you set via `AdminSeeder` or manual DB entry

---

## Slide 18 — Public user interface

- **Landing (`/`):** reCAPTCHA v2 checkbox
- **Form (`/request`):** full access request after CAPTCHA session is valid
- **Success (`/success`):** summary; PDF download where enabled (`/success/pdf`)

---

## Slide 19 — Administrator interface

- **Login:** `/admin/login` (super admins may use `/super-admin/login`)
- **Dashboard / approvals:** filter by pending, approved, rejected
- **Approvals:** changing to **approved** may require **signed approval** upload (PDF/image)
- **Super admin:** manage admins, system pages, audit trail

---

## Slide 20 — Google reCAPTCHA configuration (summary)

- Create **reCAPTCHA v2** “I’m not a robot” keys in Google admin
- Add domains: production host + `localhost` for testing
- Set `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` in `.env`
- `php artisan config:clear` after changes

*Detail:* [README_CAPTCHA.md](README_CAPTCHA.md)

---

## Slide 21 — Email / SMTP configuration (summary)

- Configure `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_*`
- Set `IT_DEPARTMENT_EMAIL` for new-request notifications
- Requesters receive mail when email is present; status mail on approve/reject

---

## Slide 22 — Google Sheets configuration (summary)

- Google Cloud: enable **Sheets** + **Drive** APIs; create **service account**; download JSON
- Place file path in `GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION`
- Set `GOOGLE_SPREADSHEET_ID`, `GOOGLE_SERVICE_ENABLED=true`
- **Share** spreadsheet with service account email (Editor)

*Detail:* [README_GOOGLE_SHEETS.md](README_GOOGLE_SHEETS.md)

---

## Slide 23 — Background processing (queues)

- Submissions dispatch **HandleAccessRequestSubmissionJob** (Sheets + mail)
- `QUEUE_CONNECTION=sync` runs inline (simplest)
- For async: `database` or `redis` + `php artisan queue:work`
- `composer run dev` runs a queue listener for local development

---

## Slide 24 — Security features

- **Admin 2FA:** TOTP (Google Authenticator–compatible), encrypted secret in DB
- **HTTP security headers** middleware (frame options, nosniff, referrer policy, HSTS on HTTPS in production)
- **Rate limiting** on login, CAPTCHA, submit, admin actions
- **Audit logging** for significant actions

*Detail:* [README_ADMIN_SECURITY.txt](README_ADMIN_SECURITY.txt)

---

## Slide 25 — Troubleshooting (quick reference)

| Symptom | Check |
|---------|--------|
| CAPTCHA / form blocked | Keys, domain allowlist, `config:clear` |
| Session lost | `APP_URL`, cookie domain, HTTPS/proxy |
| Mail not received | SMTP credentials, spam folder, queue worker |
| Sheets not updating | Service account share, JSON path, APIs enabled |

---

## Slide 26 — Further reading

- [README.md](README.md) — project index  
- [README_INSTALLATION_AND_USAGE.md](README_INSTALLATION_AND_USAGE.md) — full install + usage  
- [README_DOCKER.md](README_DOCKER.md) — Docker  
- Official Laravel docs: https://laravel.com/docs  

---

### How this maps to the Wazuh reference deck

| Wazuh-style section | This project equivalent |
|---------------------|-------------------------|
| Product overview | Slide 2 |
| Core stack / “big three” components | Slides 4–5 |
| Deployment (Docker single/multi, agents) | Slides 6–7 (single-stack Docker + others) |
| Prerequisites + OS/RAM/disk tables | Slides 8–10 |
| Required software | Slide 10 |
| Exposed ports | Slide 11 |
| Install / compose / first login | Slides 12–17 |
| Main UI (dashboard) | Slides 18–19 |
| Integrations (firewall, VirusTotal, email) | Slides 20–22 (CAPTCHA, mail, Sheets) |
| Rules/decoders deep dive | *Not applicable* — use Slides 23–25 for queues, security, troubleshooting |

Copy slides into PowerPoint, apply your org template, and add screenshots (landing, form, admin login, approvals) where needed.
