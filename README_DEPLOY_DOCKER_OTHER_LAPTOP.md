# Run on Another Laptop (Docker)

This repo already includes Docker configuration (`Dockerfile` + `docker-compose.yml`). If another laptop has Docker installed, you can run the app there with the same project files.

## What you need on the other laptop

- Docker Desktop (or Docker Engine)
- Port `8000` free (for the web app)
- Port `3307` free (for MySQL)

## Copy these project files to the other laptop

Make sure you copy the whole project folder, including:

- `.env` (important)
- `docker-compose.yml`
- `Dockerfile`
- `storage/app/google/service-account.json` (important for Google Sheets writing)

Note: your Docker containers mount your project directory (`./` -> `/var/www/html`), so these files must exist in the same relative paths.

## Run the app (Docker)

From the project folder (`Web_UserRequest`), run:

```bash
docker compose up -d --build
```

Run database migrations (first time only, or after clearing volumes):

```bash
docker compose exec app php artisan migrate
```

App URL:

- `http://localhost:8000`

## What may need changing (especially)

### 1) Database (MySQL)

When you run with `docker-compose.yml`, database settings are taken from the compose file (not the `.env` DB values).

So you typically do NOT need to edit database settings for Docker, because compose already sets:

- MySQL database: `web_userrequest`
- MySQL user: `laravel`
- MySQL password: `laravelpass`

If you want to change DB credentials, edit `docker-compose.yml` (`app.environment` + `mysql.environment`) and then re-run `docker compose up -d --build`.

Also note: MySQL data is stored in a Docker volume (`mysql_data`). If you delete volumes, you will need to migrate again.

### 2) Google Sheets (what to change)

Your app writes form submissions into specific Google Sheets tabs using:

- `GOOGLE_SPREADSHEET_ID`
- `GOOGLE_SHEET_*` values (tab/worksheet titles)
- a Service Account JSON key (`GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION`)

Your current `.env` uses:

- `GOOGLE_SERVICE_ENABLED=true`
- `GOOGLE_SERVICE_ACCOUNT_JSON_LOCATION=storage/app/google/service-account.json` (so the file must exist exactly there)
- `GOOGLE_SPREADSHEET_ID` and `GOOGLE_SHEET_ID` (IDs from your Google Sheet URL)
- `GOOGLE_SHEET_ATM`, `GOOGLE_SHEET_SMS`, `GOOGLE_SHEET_MSP`, `GOOGLE_SHEET_FTP`, `GOOGLE_SHEET_CORE`, `GOOGLE_SHEET_MVM`
- `GOOGLE_SHEET_OTHER` (fallback tab)

What you must ensure in Google Sheets:

1. The Google Spreadsheet is shared with the Service Account email (from the JSON, `client_email`).
2. Each tab title exactly matches the tab mapping values from `.env`.
   - Example: if `.env` says `GOOGLE_SHEET_ATM=ATM Portal`, your tab must be named exactly `ATM Portal`.
3. If a user selects a “System” that isn’t mapped in `config('google.sheet_by_system')`, the row goes to the fallback tab (`GOOGLE_SHEET_OTHER`).

Also consider:

- `GOOGLE_REQUEST_NUMBER_SHEET` (optional)
  - If you want the request-number counter to always use a specific tab, set this variable.
  - If not set, it falls back to `GOOGLE_SHEET_NAME`.

### 3) `APP_URL`, reCAPTCHA, and email (may be required)

If the other laptop uses a different URL/domain than the one your Google reCAPTCHA keys were created for, you must update:

- `RECAPTCHA_SITE_KEY`
- `RECAPTCHA_SECRET_KEY`

Similarly, if you want email notifications to go to a different Gmail/account, update these in `.env`:

- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `IT_DEPARTMENT_EMAIL`

## Stop / reset (if needed)

Stop containers:

```bash
docker compose down
```

Stop and remove MySQL data volume (clears database; re-run `php artisan migrate` after):

```bash
docker compose down -v
```

