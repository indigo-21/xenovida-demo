# Project Structure

Standard Laravel 12 layout. Key directories and the conventions used in this project.

```
app/
  Http/
    Controllers/        # Thin controllers; delegate logic to Services
      Auth/             # Breeze auth controllers
    Requests/           # FormRequest validation classes
  Models/               # Eloquent models (User, UserLevel)
  Services/             # Business logic + external API clients
  Providers/
  View/Components/      # Blade component classes (AppLayout, GuestLayout)
config/                 # Framework + custom config (services.php holds xenovida.*)
database/
  factories/            # Model factories
  migrations/           # Schema migrations
  seeders/              # Seeders (UserSeeder, UserLevelSeeder)
documentation/api/      # External API reference (body-composition.md)
resources/
  views/
    auth/               # Breeze auth views
    components/         # Blade components (<x-...>)
    layouts/            # Page layouts
    pages/              # Feature pages: demo/, user/, user-level/
    profile/
  js/                   # app.js, bootstrap.js (Alpine, Axios)
  css/                  # Tailwind entry
routes/
  web.php               # Web routes
  auth.php              # Breeze auth routes (required into web.php)
public/                 # Web root; built assets in public/build
tests/                  # PHPUnit tests (Feature/ and Unit/)
```

## The three API integrations

Body Composition, Essential Sheet, and Progress Sheet are parallel integrations that follow the same shape. Each has:

- A controller (`BodyCompositionController`, `EssentialSheetController`, `ProgressSheetController`)
- A FormRequest (`BodyCompositionRequest`, `EssentialSheetRequest`, `ProgressSheetRequest`)
- A service (`BodyCompositionService`, `EssentialSheetService`, `ProgressSheetService`) that hits `{XV_API_URL}/api/v1/<feature>`

They share the single `/demo` page: `BodyCompositionController@show` renders it, and each `store` action proxies one submission. The demo views live in `pages/demo/` as `form-*.blade.php` + `result-*.blade.php` (plus `script.blade.php`). When adding or editing one integration, mirror the existing pattern across its controller/request/service.

## Conventions

- **Controllers stay thin.** Validation goes in `app/Http/Requests` FormRequest classes; business logic and all external HTTP calls go in `app/Services`. Controllers inject services via the constructor and return views / JSON.
- **Service naming is inconsistent** in the codebase: external-API clients are singular (`...Service`) while the admin CRUD helpers are plural (`UserServices`, `UserLevelServices`). Match the existing file when editing; prefer the `...Service` form for new classes.
- **External API access is centralised** in the per-feature services. The browser never calls the external API directly — it posts to a local controller that proxies the request so the API key stays server-side. Every service normalises responses into the envelope `{ ok, status, data, message, errors }` and logs 5xx / connection failures.
- **Unconfigured degradation:** when `isConfigured()` is false, `store` returns a 503 envelope rather than calling upstream.
- **Views follow a `pages/<feature>/` structure** for feature screens; shared UI lives in `layouts/` and `components/`. Reference views by dot notation (e.g. `view('pages.user.index')`).
- **Routing:** resource controllers for admin CRUD (`user`, `user-level`) behind `auth` middleware; the `/demo` routes are intentionally public (unauthenticated).
- **Models** use `$fillable`, a `casts()` method, soft deletes, and audit columns (`created_by` / `updated_by` / `deleted_by`). Accessor example: `getFullNameAttribute`.
- **Config over env:** read external credentials through `config('services.xenovida.*')`, not `env()` in application code.
```
