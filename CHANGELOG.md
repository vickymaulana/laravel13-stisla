# Changelog - Laravel 13 Stisla Template

All notable changes to this project will be documented in this file.

## [4.1.0] - 2026-04-24

### Updated
- Updated Laravel 13 dependencies within the current major release line.
- Added `laravel/pail` and wired it into the combined `composer run dev` workflow.
- Updated frontend tooling within the current major release line and added `concurrently` as an explicit dev dependency.

### Refactored
- Extracted file manager folder normalization and file type filtering into reusable support classes.
- Extracted default settings into a shared source used by both the seeder and settings reset flow.
- Moved profile, settings, notification, file manager, and role access validation into Form Request classes.

### Improved
- Modernized application Blade views from Bootstrap 4 modal/alert/form helper attributes to Bootstrap 5 conventions.
- Added focused unit tests for reusable support classes and Form Request rule contracts.
- Documented the local SQLite PHP extensions required by the default test suite.

## [4.0.0] - 2026-03-18

### 🚀 Upgraded to Laravel 13
- Bumped `laravel/framework` from `^12.0` to `^13.0`
- Bumped minimum PHP requirement from `8.2` to `8.3`
- Bumped `laravel/tinker` from `^2.10` to `^3.0`
- Bumped `phpunit/phpunit` from `^11.5` to `^12.0`
- Updated `branch-alias` from `12.x-dev` to `13.x-dev`
- Added JSON schema, new convenience scripts (`setup`, `dev`, `test`), and `pre-package-uninstall` hook to `composer.json`

### 🔄 Changed
- Modernized base `Controller.php` to Laravel 13 style (removed explicit trait imports and base class extension)
- Added `: void` return types to middleware and exception closures in `bootstrap/app.php`
- Updated `config/cache.php` — new hyphenated prefix format, `serializable_classes` security hardening, `failover` store, `lock_table` option
- Updated `config/session.php` — new `serialization` option, hyphenated cookie format, `(int)` cast on lifetime
- Updated `config/app.php` — `(string)` cast on `APP_PREVIOUS_KEYS`
- Updated `phpunit.xml` — added `BROADCAST_CONNECTION`, `DB_URL`, `NIGHTWATCH_ENABLED` env vars
- Updated CI pipeline to PHP 8.4 (latest stable)
- Updated `.env.example` app name to "Laravel 13 Stisla"

### 📚 Documentation
- Updated `README.MD` — title, badges, tech stack, prerequisites, CI table, and clone URL all reflect Laravel 13
- Updated `CHANGELOG.md` title to "Laravel 13 Stisla Template"
- Updated `LEARNING.md` title and docs link to Laravel 13

### ✅ Verified
- `composer validate` passes
- All version references updated consistently across the codebase
- Config files aligned with official `laravel/laravel` 13.x skeleton
- No breaking changes to application-level code — existing controllers, models, middleware, and views are fully compatible

## [3.0.0] - 2026-02-27

### 🚀 Added
- Added **Livewire v3** integration and registered Livewire assets in the main layout
- Added **Spatie Laravel Permission** package with published config (`config/permission.php`)
- Added role/permission schema migration (`create_permission_tables`)
- Added role migration path from legacy `users.role` into Spatie pivot tables
- Added `RolePermissionSeeder` with default roles (`superadmin`, `user`) and starter permissions
- Added dark mode infrastructure:
  - Theme toggle in navbar (persisted in `localStorage`)
  - `public/css/dark-mode.css` overrides for Stisla components
  - Early theme bootstrap script to avoid flash of wrong theme
- Added Livewire `NotificationBadge` component (polling unread count)

### 🔄 Changed
- Migrated authorization checks from `Auth::user()->role` to Spatie role APIs (`hasRole`, Blade `@role`)
- Updated `User` model to use `HasRoles` trait and removed `role` from `$fillable`
- Updated `HakaksesController` and role access views to use `syncRoles()` and role collections
- Updated registration flow to assign default `user` role on account creation
- Updated middleware aliases in `bootstrap/app.php` to include `role`, `permission`, and `role_or_permission`
- Updated base controller to extend Laravel routing controller so auth controllers can use `$this->middleware()`
- Updated Vite + Blade entry consistency (`resources/sass/app.scss`) to fix manifest lookup issues
- Updated Vite Sass config to silence dependency deprecation noise during build

### ⚠️ Breaking Changes
- Legacy `users.role` column is now deprecated and dropped by migration path
- Access control now depends on Spatie role tables (`roles`, `model_has_roles`, etc.)
- Existing deployments must run new migrations and seeders before using admin role features

### ✅ Verified
- `php artisan test` — all tests pass
- `npm run build` — production assets build successfully
- Dark mode text contrast and override order validated in the main layout pages

## [2.2.0] - 2026-02-27

### 🔄 Clean Code & Modernization
- Updated base `Controller` to Laravel 12 abstract class style (removed legacy traits)
- Added return type hints to all controller methods (`View`, `RedirectResponse`, `JsonResponse`, `StreamedResponse`)
- Added typed parameters (`int $id`, `string $id`) to all controller action methods
- Added class-level PHPDoc blocks to all controllers, models, and notification classes
- Converted `$casts` property to `casts()` method in `ActivityLog` and `File` models (L12 convention)
- Added relationship return types (`BelongsTo`, `MorphTo`) to all model relationships
- Modernized `GeneralNotification` with constructor promotion and `readonly` properties
- Removed unused `ShouldQueue` import from `GeneralNotification`
- Renamed `ProfileController::changepassword()` → `changePassword()` for camelCase consistency
- Updated route definition to match renamed method
- Translated middleware abort message from Indonesian to English
- Cleaned up `bootstrap/app.php` formatting

### 🔧 Configuration & CI
- Fixed `composer.json` branch-alias from `11.x-dev` → `12.x-dev`
- Bumped CI PHP version to 8.3, Node.js to 22 LTS
- Added Composer caching to CI pipeline
- Added missing environment variables to CI test step

### 📚 Documentation
- Rewrote `README.MD` with professional formatting, version badges, and tables
- Added "Why This Template?" comparison section
- Added "Security" section documenting protection measures
- Updated project structure to reflect current codebase
- Updated tech stack table with current versions

### ✅ Verified
- All PHP files pass syntax check
- `php artisan route:list` — all routes resolve correctly
- `php artisan test` — all tests pass
- `npm run build` — frontend builds successfully

## [2.1.0] - 2026-02-20

### 🐛 Fixed (Critical)
- Fixed `HakaksesController.destroy()` — broken model binding, no redirect after delete
- Fixed `HakaksesController.update()` — unused parameter, no validation, no success message
- Fixed `HakaksesController.index()` — search by `id LIKE` replaced with name/email search
- Fixed `ProfileController.update()` — added validation (name required, email unique)
- Fixed `SettingController.update()` — boolean checkboxes now properly save unchecked state
- Fixed `SettingController` — removed dead `destroy()` method with no route
- Fixed `User` model — added `role` to `$fillable` for mass assignment
- Fixed `PasswordResetFlowTest` — test helper now uses `config()` instead of `putenv()`

### 🔄 Refactored
- **Removed `Hakakses` model** — redundant proxy to `users` table, replaced with `User` model
- **Removed `ActivityLogger` helper** — duplicated `ActivityLog::log()`, moved convenience methods to `ActivityLog` model
- Removed redundant constructor `middleware('auth')` from `HomeController` and `ExampleController`
- Replaced `env()` with `config()` in `ForgotPasswordController` and `ResetPasswordController`
- Added `password_reset_method`, `password_reset_otp_expire`, `password_reset_otp_max_attempts` to `config/auth.php`
- Used `$request->only()` instead of `$request->all()` in `SettingController.store()` for security
- Standardized all flash message keys to `'success'` (was mixed: `status`, `message`, `success`)
- Replaced free-text role input with `<select>` dropdown in hakakses edit view
- Added PHPDoc comments to all controllers and models

### 🌐 Localization
- Translated all Indonesian UI text to English for community use:
  - Sidebar: "Hak Akses" → "Role Access", "Ganti Password" → "Change Password"
  - Header: "Hai" → "Hi", "Selamat Datang" → "Welcome"
  - Change Password form: all labels translated
  - Hakakses views: complete English rewrite with proper breadcrumbs

### ✅ Verified
- `php artisan route:list` — 60 routes, no errors
- `php artisan test` — 7 tests, 18 assertions, all passed
- `npm run build` — built successfully

## [Unreleased] - 2026-01-09

### ✨ Added
- Comprehensive documentation with detailed README.md
- CONTRIBUTING.md guide for contributors
- LEARNING.md step-by-step learning path for beginners
- PHPDoc comments throughout codebase
- Inline code comments explaining Laravel concepts
- Route documentation with clear sections
- Improved code examples in controllers

### 🔄 Updated

#### Backend Dependencies
- `laravel/framework`: ^12.0 (v12.0.1 → **v12.46.0** - Latest stable with 46 improvements!)
- `laravel/ui`: ^4.6 (stable release)
- `laravel/tinker`: v2.10.1 → **v2.11.0**
- `laravel/pint`: v1.21.0 → **v1.27.0** (Latest code style fixer)
- `laravel/sail`: v1.41.0 → **v1.52.0** (Docker improvements)
- `fakerphp/faker`: Latest version for testing
- `phpunit/phpunit`: v11.5.10 → **v11.5.46**
- `spatie/laravel-ignition`: Latest error page
- `symfony/*`: v7.2.x → **v7.4.x** (All Symfony components updated)
- Plus **70+ other package updates**!

#### Frontend Dependencies
- `@popperjs/core`: ^2.11.6 → ^2.11.8
- `axios`: ^1.6.4 → ^1.7.9 (security updates)
- `bootstrap`: ^5.2.3 → ^5.3.3 (latest Bootstrap 5)
- `laravel-vite-plugin`: ^1.0 → ^1.1.1
- `sass`: ^1.56.1 → ^1.83.0 (latest features)
- `vite`: ^5.0 → ^6.0.5 (Vite 6 with performance improvements)

### 🐛 Fixed
- Fixed syntax error in ProfileController password method (extra curly brace)
- Improved validation in ProfileController with proper email uniqueness check
- Enhanced error handling throughout the application

### 📚 Documentation
- Added comprehensive README with badges and emojis
- Created learning path for beginners
- Added code examples with explanations
- Included troubleshooting section
- Added contribution guidelines

### 🎨 Improved
- Better code organization with clear sections
- Enhanced route documentation
- Improved controller method documentation
- Added model documentation with trait explanations
- Better notification class documentation

## [Previous Versions]

### What Was There Before
- Basic Laravel 12 installation
- Stisla Bootstrap template integration
- Basic authentication system
- User profile management
- Access rights management (hakakses)
- Example pages for Stisla components

---

## Migration Notes

### From Previous Version

If you're updating from a previous version:

1. **Backup your database and .env file**
   ```bash
   cp .env .env.backup
   mysqldump -u root -p your_database > backup.sql
   ```

2. **Update dependencies**
   ```bash
   composer update
   npm install
   ```

3. **Clear caches**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Rebuild assets**
   ```bash
   npm run build
   ```

5. **Review updated files**
   - Check `ProfileController.php` for validation improvements
   - Review `routes/web.php` for better organization
   - Read new documentation files

### Breaking Changes
- None in this update (fully backward compatible)

### Deprecated Features
- None

## Upcoming Features

### Planned for Next Release
- [ ] API authentication with Laravel Sanctum
- [x] Advanced role and permission system
- [ ] Email verification
- [ ] Two-factor authentication
- [x] User activity logging
- [ ] Advanced dashboard with widgets
- [x] File upload management
- [x] Notification center
- [x] Dark mode support
- [ ] Multi-language support (i18n)

## Support

For questions or issues related to this update:
- Open an issue on GitHub
- Check the LEARNING.md guide
- Review the updated README.md

---

**Update Date:** January 9, 2026  
**Maintainer:** Vicky Maulana
