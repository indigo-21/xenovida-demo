# Tech Stack

## Backend

- **PHP 8.2+**
- **Laravel 12** (framework)
- **Laravel Breeze** — authentication scaffolding (Blade stack)
- **Laravel Tinker**, **Pail** (log tailing), **Pint** (code style), **Sail**
- HTTP client: Laravel `Http` facade (Guzzle) for the external Xenovida API

## Frontend

- **Blade** templates (server-rendered)
- **Vite 7** (`laravel-vite-plugin`) as the build tool/bundler
- **Tailwind CSS** (with `@tailwindcss/forms`), PostCSS, Autoprefixer
- **Alpine.js** for interactivity
- **Axios** for XHR

## Testing

- **PHPUnit 11** (`tests/` via `Tests\` namespace), with Mockery and FakerPHP

## Configuration

- Environment-driven via `.env` (see `.env.example`).
- External API credentials live in `config/services.php` under `xenovida` (`XV_API_URL`, `XV_API_KEY`, `XV_API_TIMEOUT`). Never hardcode these; read them through config, not `env()` directly in app code.

## Common Commands

```bash
# First-time setup (install, env, key, migrate, build assets)
composer setup

# Run the full dev stack (server + queue + logs + vite) concurrently
composer dev

# Serve the app only
php artisan serve

# Frontend dev server / production build
npm run dev
npm run build

# Run tests
composer test
# or
php artisan test

# Code style (Pint)
./vendor/bin/pint

# Migrations & seeders
php artisan migrate
php artisan db:seed
```

> Note: On Windows/PowerShell, use `;` as the command separator (not `&&`). Avoid running watch/serve commands in blocking automation — run `composer dev`, `npm run dev`, or `php artisan serve` manually in a terminal.
