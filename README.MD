# Laravel 12 × Stisla Admin Template

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Vite](https://img.shields.io/badge/Vite-7-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vite.dev)
[![License](https://img.shields.io/badge/License-MIT-22c55e?style=for-the-badge)](LICENSE)
[![CI](https://img.shields.io/github/actions/workflow/status/vickymaulana/laravel12-stisla/ci.yml?branch=main&style=for-the-badge&label=CI)](https://github.com/vickymaulana/laravel12-stisla/actions)

> A production-ready Laravel 12 admin starter kit powered by the Stisla dashboard UI.  
> Clone → configure → start building business features immediately.

---

## Table of Contents

- [Why This Template?](#why-this-template)
- [Core Features](#core-features)
- [Advanced Features](#advanced-features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Password Reset Modes](#password-reset-modes)
- [Quick Tour Routes](#quick-tour-routes)
- [Generator Commands](#generator-commands)
- [Project Structure](#project-structure)
- [Testing & Quality](#testing--quality)
- [CI Pipeline](#ci-pipeline)
- [Security](#security)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)
- [Contributors](#contributors)
- [Acknowledgments](#acknowledgments)

---

## Why This Template?

| Without this template | With this template |
|---|---|
| Set up auth from scratch | Auth + OTP password reset ready |
| Build admin layout manually | Stisla UI integrated with Vite |
| Write repetitive CRUD boilerplate | `make:crud-stisla` generator |
| No activity tracking | Built-in activity log system |
| No file management | File manager with folder support |
| No notification center | Database notifications ready |
| No settings panel | Grouped dynamic settings |

---

## Core Features

- **Laravel 12 + PHP 8.2+** — latest framework with modern PHP features
- **Authentication** via Laravel UI (login, register, password reset)
- **User Profile Management** — edit profile & change password
- **Role Access Control** — `superadmin` middleware with role management UI
- **Responsive Admin Layout** — Stisla dashboard with Bootstrap 5
- **Route Grouping** — organized by domain for readability and maintenance

## Advanced Features

### Activity Log System (`/activity-logs`)
- Track user and system events (login, logout, CRUD operations)
- Filter by user, event type, date range, and keyword search
- Safe bulk clear with explicit confirmation and optional retention period

### Settings Management (`/settings`)
- Grouped application settings managed from admin panel
- Type-aware inputs: text, number, boolean, email, url, json, textarea
- Cached for performance with automatic invalidation

### Notifications (`/notifications`)
- List, mark as read, mark all read, delete notifications
- Admin notification sending to selected users
- AJAX endpoints for real-time badge count and dropdown

### File Manager (`/file-manager`)
- Upload, download, rename, and delete files with ownership checks
- Folder organization with nested path support
- Path traversal prevention for security hardening
- File type icons and human-readable size display

---

## Tech Stack

### Backend
| Technology | Version | Purpose |
|---|---|---|
| PHP | ≥ 8.2 | Runtime |
| Laravel | 12.x | Framework |
| Laravel UI | 4.x | Authentication scaffolding |
| PHPUnit | 11.x | Testing |
| Laravel Pint | 1.x | Code style |

### Frontend
| Technology | Version | Purpose |
|---|---|---|
| Bootstrap | 5.3 | UI framework |
| Vite | 7.x | Build tool |
| Sass | 1.x | CSS preprocessor |
| Axios | 1.x | HTTP client |

> **Note:** Legacy Stisla view attributes are shimmed in layout for compatibility while using modern Vite assets.

---

## Prerequisites

- PHP ≥ 8.2
- Composer
- Node.js ≥ 20
- npm
- MySQL / PostgreSQL / SQLite
- Git

---

## Quick Start

```bash
# Clone the repository
git clone https://github.com/vickymaulana/laravel12-stisla.git
cd laravel12-stisla

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan storage:link

# Development server
npm run dev          # Terminal 1 — Vite dev server
php artisan serve    # Terminal 2 — Laravel dev server
```

Open **http://localhost:8000** and register a new account.

### Superadmin Setup

After registration, promote a user to superadmin:

```bash
php artisan tinker
```

```php
App\Models\User::where('email', 'your@email.com')->update(['role' => 'superadmin']);
```

---

## Configuration

Key environment variables in `.env.example`:

```env
# Password Reset
PASSWORD_RESET_METHOD=token          # or 'otp'
PASSWORD_RESET_OTP_EXPIRE=10         # minutes
PASSWORD_RESET_OTP_MAX_ATTEMPTS=5

# Database
DB_CONNECTION=mysql
DB_DATABASE=laravel_stisla

# Mail (required for OTP mode)
MAIL_MAILER=log
```

---

## Password Reset Modes

### 1. Token Mode (default)
Standard Laravel password reset flow using email link with token.

### 2. OTP Mode (optional)
Enhanced security with OTP verification on top of the reset token:
- OTP is time-limited (configurable expiry)
- Rate-limited to prevent brute-force attacks
- OTP state is invalidated after successful reset

```env
PASSWORD_RESET_METHOD=otp
```

---

## Quick Tour Routes

After login, visit `/quick-tour` to navigate key features.

| Route | Description | Access |
|---|---|---|
| `/home` | Dashboard | All users |
| `/profile/edit` | Edit profile | All users |
| `/profile/change-password` | Change password | All users |
| `/table-example` | Table component demo | All users |
| `/form-example` | Form component demo | All users |
| `/notifications` | Notification center | All users |
| `/file-manager` | File manager | All users |
| `/settings` | Application settings | Superadmin |
| `/activity-logs` | Activity log viewer | Superadmin |
| `/hakakses` | Role access management | Superadmin |

---

## Generator Commands

### Seeder + Factory Generator

```bash
php artisan make:seeder-factory Product
```

Generates a seeder and factory for the given model.

### CRUD Generator with Stisla Views

```bash
php artisan make:crud-stisla Product
```

Scaffolds controller, model, migration, views, and routes with Stisla styling.  
Supports custom fields and excluded fields options.

---

## Project Structure

```text
app/
  Console/Commands/           # Custom generators (crud-stisla, seeder-factory)
  Http/Controllers/           # Feature controllers
  Http/Controllers/Auth/      # Authentication controllers
  Http/Middleware/            # Access middleware (Superadmin)
  Models/                     # Eloquent models (User, File, Setting, ActivityLog)
  Notifications/              # Notification classes (GeneralNotification)
  Providers/                  # Service providers

bootstrap/
  app.php                     # Application bootstrap with middleware aliases

config/
  auth.php                    # Auth + OTP password reset configuration

resources/
  views/                      # Blade templates (Stisla layout)
  css/                        # Vite CSS entry
  js/                         # Vite JS entry

routes/
  web.php                     # Route definitions grouped by domain
  console.php                 # Console commands

database/
  migrations/                 # Database schema
  seeders/                    # Seed data
  factories/                  # Model factories

tests/
  Feature/                    # Feature tests (auth, file manager, notifications, security)
  Unit/                       # Unit tests

stubs/                        # Generator stubs for CRUD scaffolding
```

---

## Testing & Quality

```bash
# Run the test suite
php artisan test

# Validate all routes resolve correctly
php artisan route:list --except-vendor

# Check code style (PSR-12)
./vendor/bin/pint --test

# Build production assets
npm run build
```

---

## CI Pipeline

GitHub Actions workflow: `.github/workflows/ci.yml`

| Step | Description |
|---|---|
| PHP 8.3 + SQLite | Environment setup |
| `composer install` | Backend dependencies |
| `php artisan migrate` | Database schema |
| `php artisan route:list` | Route integrity check |
| `php artisan test` | Test suite |
| `npm ci` + `npm run build` | Frontend build |

---

## Security

- **Path Traversal Prevention** — File manager normalizes and validates all folder paths
- **Ownership Checks** — Files are scoped to the authenticated user
- **OTP Rate Limiting** — Password reset OTP is rate-limited per IP
- **CSRF Protection** — All forms use Laravel CSRF tokens
- **Mass Assignment Protection** — Models declare explicit `$fillable` arrays
- **Password Hashing** — Bcrypt with configurable rounds

To report a security vulnerability, please open a private issue or email the maintainer directly.

---

## Troubleshooting

### `php artisan route:list` fails
Check for syntax or import errors in custom controllers.

### Password reset / OTP issues
- Verify `PASSWORD_RESET_METHOD` and `MAIL_*` settings in `.env`
- Ensure the reset link token is valid and not expired

### File manager cannot display files
Run `php artisan storage:link` to create the public storage symlink.

### Frontend changes not reflected
- Use `npm run dev` during development for hot-reload
- Use `npm run build` for production assets

---

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for coding standards, PR checklist, and validation commands.

---

## License

This project is licensed under the [MIT License](LICENSE).

---

## Contributors

### Project Maintainer
**Vicky Maulana** — [GitHub](https://github.com/vickymaulana)

### Special Thanks
Gratitude to all the interns at LLDIKTI 2 Division of Information System Development:

[Ahmad Dimas Aldian Al-furqon](https://github.com/anqois) · [Abdillah Khalid](https://github.com/KhalidUnsri) · [Haikal Tirta Albanna](https://github.com/HaikalAlbanna) · [Meta Berliana](https://github.com/Metabrln) · [Imelda Triadmajaya](https://github.com/imeldatriajaya) · [Hilwa Izzatinnafisah](https://github.com/ksnwaa) · [Triyana Sugiyarti](https://github.com/TriyanaSgi) · [Adelia Isni Hendrawan Putri](https://github.com/lilisky07) · [Siti Nur Azizah](https://github.com/sukasukajija) · [M. Husaini Hasyim](https://github.com/son-alone) · [Andre Satriawan](https://github.com/andresa11satriawan) · [Lathifah Putri Aresti](https://github.com/lathifahputri) · [Sisca Amelia](https://github.com/siscaamelia) · [M. Denny Tri Lisandi](https://github.com/Koutsura)

---

## Acknowledgments

- [Laravel](https://laravel.com) — The PHP framework for web artisans
- [Stisla](https://getstisla.com) — Free Bootstrap admin template
- [Bootstrap](https://getbootstrap.com) — Frontend toolkit
- [Vite](https://vite.dev) — Next generation frontend tooling
