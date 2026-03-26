# Docker Setup (Laravel + MySQL)

This project is already configured with a `Dockerfile` and `docker-compose.yml`.

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

