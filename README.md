<p align="center">
  <img src="public/assets/images/logo-brand-white.png" alt="Scroll" height="80">
</p>

<h1 align="center">Scroll</h1>

<p align="center">
  Open source, self-hosted payroll system built for remote-first companies.
</p>

<p align="center">
  <a href="https://github.com/Sultonisky/SPayroll/blob/main/LICENSE"><img src="https://img.shields.io/badge/license-MIT-green" alt="License"></a>
  <a href="https://github.com/Sultonisky/SPayroll/actions/workflows/ci.yml"><img src="https://github.com/Sultonisky/SPayroll/actions/workflows/ci.yml/badge.svg" alt="CI"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-13.x-red?logo=laravel" alt="Laravel"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-8.3+-blue?logo=php" alt="PHP"></a>
  <img src="https://img.shields.io/badge/self--hosted-yes-brightgreen" alt="Self Hosted">
  <img src="https://img.shields.io/badge/tests-PHPUnit-blue?logo=php" alt="Tests">
  <img src="https://img.shields.io/badge/code%20style-Laravel%20Pint-orange" alt="Laravel Pint">
</p>

<p align="center">
  <a href="#-installation">📦 Installation</a> &bull;
  <a href="#-features">✨ Features</a> &bull;
  <a href="#-customization">🎨 Customization</a> &bull;
  <a href="#-testing">🧪 Testing</a> &bull;
  <a href="#-cicd">🔄 CI/CD</a> &bull;
  <a href="#-limitations">⚠️ Limitations</a> &bull;
  <a href="#-contributing">🤝 Contributing</a> &bull;
  <a href="#-legal">⚖️ Legal</a>
</p>

---

## Overview

Scroll is a lightweight, self-hosted payroll management system designed for small to mid-sized remote-first companies. It gives your HR and finance team a clean, straightforward interface to manage employees, run monthly payroll, handle bonuses, and export pay data - all on your own infrastructure, with no vendor lock-in.

Built with [Laravel 13](https://laravel.com) and a minimal frontend stack.

---

## ✨ Features

### Employee Management
- Add, edit, and archive employees with full profile data (NIK, department, position, join date, bank details)
- Support for two employee types: **Fulltime** and **Internship** - each with their own base salary per position
- Employee code generation
- Soft delete with trash & restore

### Department & Position
- Manage departments and positions independently
- Set base salaries per position per employee type (fulltime / internship)
- Soft delete with restore support

### Payroll
- **Bulk payroll generation** - run payroll for all active employees in one click for a given month/year
- **Preview before generating** - see a salary breakdown per employee before committing
- Payroll workflow: `Draft → Approved → Paid`
- Bulk approve drafts and bulk mark-as-paid
- Period overview with aggregate stats (total employees, total salary, draft/approved/paid counts)
- Per-payroll CSV export and bulk approved payroll export (bank transfer format)
- Salary formula: `Total Salary = Base Salary + Approved Bonuses`

### Bonus Management
- Submit bonus requests per employee per period with type and description
- Approval workflow: `Pending → Approved / Rejected`
- Approved bonuses are automatically included in payroll calculations
- Soft delete with restore

### User & Role Management
- Four built-in roles: **Admin**, **HR**, **Manager**, **Finance**, **Staff**
- Fine-grained permission control per module per role via policies and middleware
- Profile management with photo upload

### Notifications
- In-app dashboard notifications

### Other
- Secure login with remember-me and login throttling
- Security headers middleware
- Image processing for profile photos (via Intervention Image)
- Responsive dashboard UI (CoreUI 5)
- Trash & restore (soft delete) across all major models

---

## 🧪 Testing

Scroll ships with a comprehensive test suite covering Feature and Unit layers. All tests run against SQLite in-memory — no external database required.

### Running Tests

```bash
# Run the full test suite
php artisan test

# Run only unit tests
php artisan test --testsuite=Unit

# Run only feature tests
php artisan test --testsuite=Feature

# Run a specific test file
php artisan test tests/Feature/Controllers/PayrollControllerTest.php
```

### Test Coverage

#### Unit Tests (`tests/Unit/`)

| File | What it covers |
|---|---|
| `UserModelTest` | User model scopes, role helpers, and factory states |
| `BonusModelTest` | Bonus model relationships, status scopes, and factory states |
| `PositionModelTest` | Position model salary scopes and relationships |
| `AttendanceServiceTest` | AttendanceService import/parsing logic |
| `PayrollCalculatorServiceTest` | Salary calculation, bonus inclusion, and edge cases |
| `EmployeeObserverTest` | Employee observer — auto-code generation on create |

#### Feature Tests (`tests/Feature/`)

| Directory | What it covers |
|---|---|
| `Auth/` | Login, logout, remember-me, and failed authentication |
| `Commands/` | `CleanUpTrashCommand` and `DemoResetCommand` artisan commands |
| `Controllers/` | Full CRUD HTTP tests for all dashboard controllers (Bonus, Dashboard, Department, Employee, Payroll, Position, Profile, User) |
| `Middleware/` | `RoleMiddleware` — route access per role |
| `Policy/` | Laravel Gate policies for Bonus, Employee, Payroll, and User |

### Test Infrastructure

- **Base class** — `tests/TestCase.php` uses `RefreshDatabase` and provides role-based factory helpers: `adminUser()`, `hrUser()`, `managerUser()`, `staffUser()`, `demoAdmin()`
- **Database** — SQLite `:memory:` configured in `phpunit.xml` — no setup needed
- **Framework** — PHPUnit via `php artisan test`

---

## 🔄 CI/CD

Every push and pull request targeting `main` runs three parallel jobs via GitHub Actions:

| Job | What it does |
|---|---|
| **Code Style (Pint)** | Runs `./vendor/bin/pint --test` — fails if any file has style violations |
| **Tests (PHP 8.3)** | Runs the full PHPUnit suite against SQLite in-memory |
| **Build Frontend Assets** | Runs `npm ci && npm run build` to verify the Vite build passes |

### Pipeline Details

- PHP **8.3** with extensions: `bcmath`, `ctype`, `fileinfo`, `mbstring`, `pdo_sqlite`, and more
- Composer dependencies are **cached** via `actions/cache@v4` keyed on `composer.lock`
- Node.js **20** with npm cache enabled
- Tests use `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:` — no migration step needed

### Running CI Checks Locally

```bash
# Code style check (dry run — shows issues without fixing)
./vendor/bin/pint --test

# Auto-fix all style issues
./vendor/bin/pint

# Full test suite
php artisan test

# Frontend build
npm run build
```

---

## ⚠️ Limitations

Before deploying, be aware of the current scope and known limitations:

| Area | Status |
|---|---|
| Attendance tracking | 🚧 Work in progress - routes are scaffolded but disabled |
| Tax calculation (PPh 21) | ❌ Not yet implemented - total salary is base + bonus only |
| Multi-currency | ❌ Single currency only |
| Payslip PDF generation | ❌ Not yet - export is CSV only |
| Multi-company / multi-tenant | ❌ Single company per installation |
| Overtime & deductions | ❌ Not yet implemented |
| API / mobile access | ❌ Web only |
| Email notifications | ❌ Not yet wired up (queue is in place) |

> Contributions are welcome! See [Contributing](#-contributing) if you want to help add any of the above.

---

## 🛠 Tech Stack

- **Backend:** PHP 8.3, Laravel 13
- **Frontend:** CoreUI 5, jQuery, Font Awesome
- **Database:** MySQL / MariaDB (recommended) or SQLite (for local dev)
- **Storage:** Local disk or AWS S3 (via `league/flysystem-aws-s3-v3`)
- **Excel:** Maatwebsite Laravel Excel
- **Image:** Intervention Image 3

---

## 📦 Installation

### Requirements

- PHP **8.3+** with extensions: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- Composer 2
- Node.js 18+ & npm
- MySQL 8 / MariaDB 10.6+ (or SQLite for quick local setup)

---

### 1. Clone the repository

```bash
git clone https://github.com/Sultonisky/SPayroll.git
cd SPayroll
```

### 2. Install PHP dependencies

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Install Node dependencies & build assets

```bash
npm install
npm run build
```

### 4. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and set your database, app URL, and storage settings:

```env
APP_NAME="Scroll"
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=s_payroll
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 5. Run migrations & seed default data

```bash
php artisan migrate --force
php artisan db:seed
```

> The seeder creates a default **Admin** account. Check `database/seeders/DatabaseSeeder.php` for the credentials and change them immediately after first login.

### 6. Set storage permissions

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### 7. (Optional) Configure queue worker

Scroll uses Laravel queues for background jobs. For production, run a persistent worker:

```bash
php artisan queue:work --tries=3
```

Or use Supervisor to keep it alive. See [Laravel Queue docs](https://laravel.com/docs/queues#supervisor-configuration).

---

### Quick start (local / dev)

Use the built-in `composer dev` shortcut which starts the Laravel server, queue listener, log watcher, and Vite dev server concurrently:

```bash
composer run dev
```

---

### Using Docker (optional)

A `Dockerfile` and `docker-compose.yml` are not included yet but are planned. For now, you can use any standard Laravel-compatible Docker setup such as [Laravel Sail](https://laravel.com/docs/sail):

```bash
composer require laravel/sail --dev
php artisan sail:install
./vendor/bin/sail up
```

---

## 🎨 Customization

After installing, you can brand Scroll to match your company by swapping the logo and adjusting the primary color. Everything is in plain CSS and SVG — no build step required.

### Logo

Scroll uses three logo files, all located in `public/assets/images/`. Replace them with your own files using the exact same filenames so all references update automatically.

| File | Where it appears |
|---|---|
| `logo.svg` | Browser favicon (tab icon) across all pages |
| `logo-brand.svg` | Login page (left panel) and legal pages (Privacy Policy, Terms of Service) |
| `logo-brand-white.svg` | README header — not rendered in the app itself, referenced for documentation purposes |

**Requirements for replacement files:**
- Keep the same filenames exactly
- SVG format is recommended (scales cleanly at any size)
- `logo.svg` is typically a square icon/mark (no wordmark)
- `logo-brand.svg` is typically a wider horizontal lockup — the login panel renders it at `280×280px`

After replacing:
```bash
# Clear any cached views (optional, recommended)
php artisan view:clear
```

---

### App Name

The application name appears in two places in the layout files. Update both to match your company name:

**1. Browser tab title** — `resources/views/layouts/app.blade.php`

```html
<title>Your Company - Dashboard</title>
```

**2. Sidebar brand name** — `resources/views/layouts/sidebar.blade.php`

```html
<span class="fw-bold">Dashboard <span class="text-primary">YourBrand</span></span>
```

---

### Primary Color

The primary color drives the sidebar accent, active nav links, form labels, login panel background, and interactive element highlights. It is defined in **two separate CSS files** — update both to keep the login page and dashboard in sync.

**1. Dashboard theme** — `public/assets/dashboard/style/coreui.css`

```css
:root {
    --cui-primary: #00F260;         /* ← change this */
    --cui-primary-rgb: 0, 242, 96;  /* ← RGB equivalent for the same color */
    --cui-link-color: #00F260;      /* ← link color */
    --cui-link-hover-color: #00c94f; /* ← link hover (slightly darker) */
}

[data-coreui-theme="dark"] {
    --cui-primary: #00F260;         /* ← also update for dark mode */
    --cui-primary-rgb: 0, 242, 96;
}
```

**2. Login page theme** — `public/assets/dashboard/style/login.css`

```css
:root {
    --primary-color: #00F260;  /* ← login panel background color */
    --primary-dark: #00c94f;   /* ← used for hover accents and emphasized text */
}
```

> **Tip:** To convert a hex color to RGB for `--cui-primary-rgb`, use any online converter (e.g., `#2563EB` → `37, 99, 235`).

**Example — switching to a blue theme:**

```css
/* coreui.css */
:root {
    --cui-primary: #2563EB;
    --cui-primary-rgb: 37, 99, 235;
    --cui-link-color: #2563EB;
    --cui-link-hover-color: #1d4ed8;
}
[data-coreui-theme="dark"] {
    --cui-primary: #2563EB;
    --cui-primary-rgb: 37, 99, 235;
}

/* login.css */
:root {
    --primary-color: #2563EB;
    --primary-dark: #1d4ed8;
}
```

No rebuild is needed — these are plain CSS files served directly as static assets.

---

## 🔐 Default Roles & Permissions

Scroll uses a role-based access control (RBAC) system with five built-in roles. Permissions are enforced at both the backend (Laravel Policies + Gate) and frontend (sidebar visibility + UI element guards).

### Role Overview

| Role | User Mgmt | Departments | Positions | Employees | Bonus | Payroll Generate | Payroll Workflow | Profile |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| **Admin** | ✅ Full | ✅ Full | ✅ Full | ✅ Full | ✅ Full | ✅ | ✅ Full | ✅ Edit |
| **HR** | ❌ | ✅ Full | ✅ Full | ✅ Full | ✅ Full | ✅ | ✅ Full | ✅ Edit |
| **Manager** | ❌ | 👁 View | 👁 View | ✅ Edit | ✅ Create/Edit | ❌ | ✅ Approve/View | ✅ Edit |
| **Finance** | ❌ | ❌ | ❌ | 👁 View | ✅ Full | ✅ | ✅ Full | ✅ Edit |
| **Staff** | ❌ | ❌ | ❌ | 👁 View | ❌ | ❌ | 👁 View | ✅ Edit |

### Detailed Permission Matrix

#### Users
| Action | Admin | HR | Manager | Finance | Staff |
|---|:---:|:---:|:---:|:---:|:---:|
| View list | ✅ | ❌ | ❌ | ❌ | ❌ |
| Create user | ✅ | ❌ | ❌ | ❌ | ❌ |
| Edit user | ✅ | ❌ | ❌ | ❌ | ❌ |
| Delete / Restore | ✅ | ❌ | ❌ | ❌ | ❌ |
| Force delete | ✅ | ❌ | ❌ | ❌ | ❌ |

#### Departments & Positions
| Action | Admin | HR | Manager | Finance | Staff |
|---|:---:|:---:|:---:|:---:|:---:|
| View list & detail | ✅ | ✅ | ✅ | ❌ | ❌ |
| Create / Edit | ✅ | ✅ | ❌ | ❌ | ❌ |
| Delete / Restore | ✅ | ✅ | ❌ | ❌ | ❌ |
| Force delete | ✅ | ✅ | ❌ | ❌ | ❌ |

#### Employees
| Action | Admin | HR | Manager | Finance | Staff |
|---|:---:|:---:|:---:|:---:|:---:|
| View list & detail | ✅ | ✅ | ✅ | 👁 View | ✅ |
| Create / Edit | ✅ | ✅ | ✅ | ❌ | ❌ |
| Delete / Restore | ✅ | ✅ | ❌ | ❌ | ❌ |
| Force delete | ✅ | ✅ | ❌ | ❌ | ❌ |
| Export | ✅ | ✅ | ✅ | ❌ | ✅ |

#### Employee Bonus
| Action | Admin | HR | Manager | Finance | Staff |
|---|:---:|:---:|:---:|:---:|:---:|
| View list & detail | ✅ | ✅ | ✅ | ✅ | ❌ |
| Create / Edit | ✅ | ✅ | ✅ | ✅ | ❌ |
| Approve / Reject | ✅ | ✅ | ❌ | ✅ | ❌ |
| Delete / Restore | ✅ | ✅ | ❌ | ❌ | ❌ |
| Force delete | ✅ | ✅ | ❌ | ❌ | ❌ |

#### Payroll
| Action | Admin | HR | Manager | Finance | Staff |
|---|:---:|:---:|:---:|:---:|:---:|
| View (all) | ✅ | ✅ | ✅ | ✅ | ✅ |
| Generate bulk | ✅ | ✅ | ❌ | ✅ | ❌ |
| Create single | ✅ | ✅ | ✅ | ✅ | ❌ |
| Edit draft | ✅ | ✅ | ✅ | ✅ | ❌ |
| Approve draft | ✅ | ✅ | ❌ | ✅ | ❌ |
| Mark as paid | ✅ | ✅ | ❌ | ✅ | ❌ |
| Export CSV | ✅ | ✅ | ✅ | ✅ | ✅ |
| Delete / Restore | ✅ | ✅ | ❌ | ❌ | ❌ |
| Force delete | ✅ | ✅ | ❌ | ❌ | ❌ |

---

## 🧪 Live Demo & Demo Accounts

A live demo is available at the official deployment. You can try Scroll with any of the following pre-seeded accounts — each representing a different role so you can explore the full permission model.

### Demo Credentials

| Role | Email | Password |
|---|---|---|
| Admin | `admin@demo.spayroll.com` | `demo12345` |
| HR | `hr@demo.spayroll.com` | `demo12345` |
| Manager | `manager@demo.spayroll.com` | `demo12345` |
| Finance | `finance@demo.spayroll.com` | `demo12345` |
| Staff | `staff@demo.spayroll.com` | `demo12345` |

> Click any credential card on the login page to auto-fill the form — no typing required.

### Demo Account Restrictions

Demo accounts carry their full role permissions **except** for the following, which exist to protect demo data integrity for all visitors:

| Feature | Demo restriction |
|---|---|
| User management (create / edit / delete) | ❌ Blocked for all demo roles |
| Employee create / edit / delete | ❌ Blocked |
| Bonus create / edit / approve / delete | ❌ Blocked |
| Profile update (name, email, password, photo) | ❌ Full read-only |
| Payroll engine (generate, approve, mark paid, export) | ✅ **Fully enabled** |

**Why these specific restrictions?**
- User and employee data are the structural foundation of the demo. Modifying them would break the payroll engine for other visitors.
- The payroll workflow is intentionally unrestricted so visitors can experience the full generate → approve → mark paid cycle end-to-end.
- Profile is read-only to prevent visitors from locking each other out by changing credentials.

### Demo Data Reset

The demo environment resets automatically every night at **00:00** via a scheduled `php artisan demo:reset --force` command. This wipes all data and re-seeds fresh dummy employees, departments, positions, payrolls, and bonuses.

Manual reset:
```bash
php artisan demo:reset
```

---

## 🗂 Project Structure

```
app/
├── Console/Commands/       # Artisan commands (cleanup, demo reset)
├── Http/
│   ├── Controllers/
│   │   └── Dashboard/      # Feature controllers
│   └── Middleware/         # Role-based access, security headers
├── Models/                 # Eloquent models
├── Observers/              # Model event observers
├── Policies/               # Authorization policies
├── Services/               # Business logic (PayrollCalculatorService, AttendanceService)
└── Traits/                 # Reusable traits (image processing, etc.)

resources/views/
├── auth/                   # Login page
├── dashboard/              # All dashboard views per module
├── layouts/                # Shared layouts (sidebar, navbar)
└── legal/                  # Privacy policy, terms of service

tests/
├── TestCase.php            # Base class with RefreshDatabase + role helpers
├── Feature/
│   ├── Auth/               # Authentication flow tests
│   ├── Commands/           # Artisan command tests
│   ├── Controllers/        # HTTP tests for all dashboard controllers
│   ├── Middleware/         # RoleMiddleware access control tests
│   └── Policy/             # Laravel Gate policy tests
└── Unit/
    ├── AttendanceServiceTest.php
    ├── BonusModelTest.php
    ├── EmployeeObserverTest.php
    ├── PayrollCalculatorServiceTest.php
    ├── PositionModelTest.php
    └── UserModelTest.php

.github/workflows/
└── ci.yml                  # 3-job CI pipeline: Pint, PHPUnit, Vite build
```

---

## 🤝 Contributing

Contributions, bug reports, and feature requests are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) for the full guide including branching conventions, commit message format, and coding standards.

We follow a [Code of Conduct](CODE_OF_CONDUCT.md) — please read it before contributing.

1. Fork the repository
2. Create a branch: `git checkout -b feat/your-feature`
3. Commit your changes following [Conventional Commits](https://www.conventionalcommits.org/)
4. Push to the branch: `git push origin feat/your-feature`
5. Open a Pull Request using the provided template

Please keep PRs focused — one feature or fix per PR.

---

## 🛡 Security

If you discover a security vulnerability, please open a **private** issue or contact the maintainer directly via GitHub instead of using the public issue tracker.

For production deployments:
- Always set `APP_ENV=production` and `APP_DEBUG=false`
- Use HTTPS
- Rotate the `APP_KEY` after initial setup
- Change default seeded credentials immediately
- Keep PHP, Laravel, and all dependencies updated with security patches
- Restrict database access to localhost or a private network

---

## ⚖️ Legal

Scroll is provided as open-source software under the **MIT License**. The following legal documents apply to the official demo environment and the software distribution:

| Document | Applies to |
|---|---|
| [Privacy Policy](resources/views/legal/privacy-policy.blade.php) | Official demo — describes how personal data is handled |
| [Terms of Service](resources/views/legal/terms-of-service.blade.php) | Official demo — acceptable use, disclaimers, and liability |
| [MIT License](LICENSE) | Source code — rights to use, modify, and distribute |
| [Code of Conduct](CODE_OF_CONDUCT.md) | Community — expected behavior for contributors and maintainers |

**For self-hosted operators:** You are the data controller for all personal data processed through your installation. You are responsible for establishing your own privacy policy, ensuring compliance with applicable data protection laws (including **UU PDP No. 27/2022** in Indonesia), and securing your infrastructure. The project maintainers have no access to and no responsibility for data in independently operated instances.

> Scroll does not implement automated PPh 21 tax calculation. All payroll outputs should be independently verified before disbursement or regulatory submission.

---

## 📄 License

Scroll is open source software released under the [MIT License](LICENSE).

You are free to use, modify, and distribute this software for personal or commercial use, subject to retaining the original copyright notice. Attribution is appreciated but not required beyond that.

---

## 👤 Credits

Built and maintained by **[Mohammad Sultoni](https://github.com/Sultonisky)**.

### Built with

| Package | Purpose |
|---|---|
| [Laravel](https://laravel.com) | Application framework |
| [CoreUI 5](https://coreui.io) | Admin UI components |
| [Maatwebsite Excel](https://laravel-excel.com) | Excel/CSV import & export |
| [Intervention Image](https://image.intervention.io) | Image processing |
| [Font Awesome](https://fontawesome.com) | Icons |
| [jQuery](https://jquery.com) | Frontend utilities |

---

<p align="center">
  Made with ❤️ for remote-first teams.
</p>
