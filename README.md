# Laravel AdminLTE Boilerplate

Laravel AdminLTE Boilerplate is a reusable starting point for building administration and internal business applications. It combines Laravel's application structure and authentication features with the AdminLTE v3 interface and includes foundational user and access-level management modules.

The project provides a clean, maintainable base that can be extended with additional business modules. Its interface uses the AdminLTE resources stored in `public/`, while application-specific behavior follows Laravel conventions and separates request handling, business logic, persistence, and presentation concerns.

## Project objectives

- Maintain a lean Laravel and Breeze installation by retaining only the scaffolding and assets used by the application.
- Present a responsive, consistent, and modern administration interface based on AdminLTE v3.
- Provide reusable implementation patterns for CRUD modules.
- Manage application users and their assigned user levels.
- Supply repeatable development data through database seeders.

## Current features

- Authentication powered by Laravel Breeze, including registration, login, logout, password reset, email verification, and password confirmation.
- An authenticated dashboard showing user and user-level statistics.
- Profile information, password, and account management.
- User management with create, list, edit, and delete operations.
- User Level management with create, list, edit, and delete operations.
- Assignment of one user level to each user.
- Active and inactive user statuses.
- Validation for user and user-level forms.
- Soft deletion and `created_by`, `updated_by`, and `deleted_by` audit fields.
- Protection against deleting a user level that is still assigned to users.
- AdminLTE v3 components with DataTables, Select2, SweetAlert2, Font Awesome, and Bootstrap 4.
- Idempotent seeders for five user levels and five sample users.

## Technology stack

- PHP 8.2 or later
- Laravel 12
- Laravel Breeze 2
- MySQL
- AdminLTE v3 and Bootstrap 4
- Blade templates
- Vite, Tailwind CSS, and Alpine.js
- PHPUnit 11

## Project structure

The principal application areas are organized as follows:

```text
app/Http/Controllers/   HTTP and resource controllers
app/Models/             Eloquent models and relationships
app/Services/           Validation and persistence logic for CRUD modules
database/migrations/    Database schema
database/seeders/       Repeatable sample data
public/dist/            AdminLTE theme resources
public/plugins/         AdminLTE-compatible frontend plugins
resources/views/        Blade layouts, components, authentication, and module pages
routes/                 Web and authentication routes
tests/                  Unit and feature tests
```

## Requirements

Install the following before setting up the application:

- PHP 8.2 or later with the extensions required by Laravel and MySQL
- Composer
- MySQL or MariaDB
- Node.js and npm

For a Windows/XAMPP environment, ensure that the PHP executable used by Composer and Artisan is the intended XAMPP PHP installation.

## Installation

1. Clone the repository and enter its directory.

   ```bash
   git clone <repository-url>
   cd boilerplate-laravel
   ```

2. Install PHP dependencies.

   ```bash
   composer install
   ```

3. Create the environment file and generate an application key.

   On Windows PowerShell:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

   On macOS or Linux:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Create a MySQL database and configure its connection in `.env`.

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=boilerplate_laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Install frontend dependencies and build the assets.

   ```bash
   npm install
   npm run build
   ```

6. Create the database tables and development data.

   ```bash
   php artisan migrate --seed
   ```

7. Start the local development server.

   ```bash
   php artisan serve
   ```

Open `http://127.0.0.1:8000` in a browser. When the project is served through XAMPP instead, configure the virtual host or document root to point to the project's `public` directory.

The repository also provides a combined development command that starts the web server, queue listener, log viewer, and Vite server:

```bash
composer run dev
```

## Seeded development accounts

Running `php artisan migrate --seed` creates the following local accounts. Every seeded account uses the password `Password@1`.

| User | Email | User level | Status |
| --- | --- | --- | --- |
| Admin User | `admin@example.com` | Super Administrator | Active |
| John Doe | `john.doe@example.com` | Administrator | Active |
| Jane Smith | `jane.smith@example.com` | Manager | Active |
| Mike Jones | `mike.jones@example.com` | Staff | Active |
| Sara White | `sara.white@example.com` | Viewer | Inactive |

These credentials are for local development only. Replace or remove them before deploying the application to a shared or production environment.

The seeders use `updateOrCreate`, so they can be run repeatedly without intentionally creating duplicate user-level or user records:

```bash
php artisan db:seed
```

## Application modules

### Users

The User module manages first name, last name, email address, username, password, assigned user level, and account status. Its authenticated resource routes are available under `/user`.

### User Levels

The User Level module manages the access-level descriptions assigned to users. Its authenticated resource routes are available under `/user-level`. A level cannot be deleted while one or more users are assigned to it.

User levels currently classify accounts for administration purposes. New modules should add authorization policies or middleware when those classifications need to enforce permissions.

## Development checks

Run the automated test suite with:

```bash
composer test
```

Check PHP formatting without changing files with:

```bash
vendor/bin/pint --test
```

Verify that production frontend assets compile successfully with:

```bash
npm run build
```

## Adding new modules

Use the User and User Level modules as the reference pattern for new CRUD functionality. Keep controllers focused on HTTP flow, place validation and persistence operations in a service class, define relationships in Eloquent models, protect management routes with appropriate middleware, and build views from the shared AdminLTE layouts and components.

## Production notes

Before deployment, configure production database and mail services, set `APP_ENV=production` and `APP_DEBUG=false`, replace the seeded credentials, restrict public registration if it is not required, add authorization rules for protected modules, and configure a process manager for queue workers when background jobs are used.

## License

This project is built on Laravel, which is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).
