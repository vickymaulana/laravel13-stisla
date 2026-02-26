# Changelog - Laravel 12 Stisla Template

All notable changes to this project will be documented in this file.

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
- [ ] Advanced role and permission system
- [ ] Email verification
- [ ] Two-factor authentication
- [x] User activity logging
- [ ] Advanced dashboard with widgets
- [x] File upload management
- [x] Notification center
- [ ] Dark mode support
- [ ] Multi-language support (i18n)

## Support

For questions or issues related to this update:
- Open an issue on GitHub
- Check the LEARNING.md guide
- Review the updated README.md

---

**Update Date:** January 9, 2026  
**Maintainer:** Vicky Maulana
