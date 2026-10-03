# Northbridge College of Nursing

A production-oriented nursing-college landing site built with Laravel 12, Vue 3 Composition API, Vite, Bootstrap 5, Bootstrap Icons, and MySQL. The page content API provides database-backed programs, faculty, events, and admissions milestones; the admissions inquiry form validates and persists submissions.

## Requirements

- PHP 8.2 or newer with PDO MySQL
- Composer 2
- Node.js 20.19+ and npm
- MySQL 8+

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use `Copy-Item .env.example .env` instead of `cp` if needed.

Create an empty MySQL database and configure these values in `.env`:

```env
APP_NAME="Northbridge College of Nursing"
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=northbridge_college
DB_USERNAME=root
DB_PASSWORD=
```

Then run:

```bash
php artisan migrate --seed
npm install
npm run dev
php artisan serve
```

Open `http://127.0.0.1:8000`.

## Admin panel

Visit `http://127.0.0.1:8000/admin` after migrating and seeding.

```text
Email: akhtar@website.com
Password: 1234qwer.
```

The protected dashboard manages public page copy, programs, faculty, news and events, admissions steps, and inquiry statuses. Change the seeded password before any non-local deployment.

## Production build

```bash
npm ci
npm run build
php artisan optimize
php artisan migrate --force
```

Configure the web server document root as `public/`, set `APP_ENV=production` and `APP_DEBUG=false`, and ensure `storage/` and `bootstrap/cache/` are writable. Serve over HTTPS and configure secure production MySQL credentials only in `.env`.

## API

- `GET /api/college-content` — programs, faculty, events, and admissions milestones
- `POST /api/inquiries` — validated admissions inquiry (`name`, `email`, optional `phone`, `program`)

The inquiry endpoint is rate-limited to ten requests per minute per client. Laravel returns standard JSON validation responses with HTTP 422 and creates successful submissions with HTTP 201.

## Quality checks

```bash
php artisan test
npm run build
php artisan route:list --except-vendor
```

Generated project photography is stored in `public/images/`, so no external image service is required at runtime.
