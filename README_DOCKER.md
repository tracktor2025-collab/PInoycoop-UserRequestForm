# Docker Setup (Laravel + MySQL)

This project is configured with a `Dockerfile`, `docker-compose.yml`, and a Laravel startup entrypoint.

## Prerequisites

- Install Docker Desktop (or Docker Engine) on the machine you want to run this on.
- Make sure ports `8000` (web) and `3307` (MySQL) are available.

## Run

From the project folder (`Web_UserRequest`):

```bash
docker compose up -d --build
```

Open the app:

- `http://localhost:8000`

The app container uses MySQL from the compose network:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=web_userrequest
DB_USERNAME=laravel
DB_PASSWORD=laravelpass
```

## Database migrations

```bash
docker compose exec app php artisan migrate
```

## Optional seed

```bash
docker compose exec app php artisan db:seed
```

## Stop

```bash
docker compose down
```

## Stop + remove MySQL data volume

```bash
docker compose down -v
```

## Optional: run migrations automatically

Set this in `docker-compose.yml` if you want migrations to run whenever the app container starts:

```yaml
RUN_MIGRATIONS: "true"
```

## Google service account

`storage/app/google/*.json` is excluded from Docker image builds so secrets are not baked into the image. In local compose, the project folder is mounted into the container, so `storage/app/google/service-account.json` remains available from your working copy.
