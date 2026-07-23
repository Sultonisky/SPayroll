<p align="center">
  <img src="public/assets/images/logo-brand.svg" alt="S-Payroll" height="80">
</p>

<h1 align="center">S-Payroll</h1>

<p align="center">
  Open source, self-hosted payroll system built for remote-first companies.
</p>

<p align="center">
  <a href="https://github.com/Sultonisky/s-payroll/blob/main/LICENSE"><img src="https://img.shields.io/badge/license-MIT-green" alt="License"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-13.x-red?logo=laravel" alt="Laravel"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-8.3+-blue?logo=php" alt="PHP"></a>
  <img src="https://img.shields.io/badge/self--hosted-yes-brightgreen" alt="Self Hosted">
</p>

<p align="center">
  <a href="https://your-demo-url.com">🚀 Live Demo</a> &bull;
  <a href="#-installation">📦 Installation</a> &bull;
  <a href="#-features">✨ Features</a> &bull;
  <a href="#-limitations">⚠️ Limitations</a>
</p>

---

## Overview

S-Payroll is a lightweight, self-hosted payroll management system designed for small to mid-sized remote-first companies. It gives your HR and finance team a clean, straightforward interface to manage employees, run monthly payroll, handle bonuses, and export pay data — all on your own infrastructure, with no vendor lock-in.

Built with [Laravel 13](https://laravel.com) and a minimal frontend stack.

---

## ✨ Features

### Employee Management
- Add, edit, and archive employees with full profile data (NIK, department, position, join date, bank details)
- Support for two employee types: **Fulltime** and **Internship** — each with their own base salary per position
- Employee code generation
- Soft delete with trash & restore

### Department & Position
- Manage departments and positions independently
- Set base salaries per position per employee type (fulltime / internship)
- Soft delete with restore support

### Payroll
- **Bulk payroll generation** — run payroll for all active employees in one click for a given month/year
- **Preview before generating** — see a salary breakdown per employee before committing
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
- Four built-in roles: **Admin**, **HR**, **Manager**, **Staff**
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

## ⚠️ Limitations

Before deploying, be aware of the current scope and known limitations:

| Area | Status |
|---|---|
| Attendance tracking | 🚧 Work in progress — routes are scaffolded but disabled |
| Tax calculation (PPh 21) | ❌ Not yet implemented — total salary is base + bonus only |
| Multi-currency | ❌ Single currency only |
| Payslip PDF generation | ❌ Not yet — export is CSV only |
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
git clone https://github.com/Sultonisky/s-payroll.git
cd s-payroll
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
APP_NAME="S-Payroll"
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

S-Payroll uses Laravel queues for background jobs. For production, run a persistent worker:

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

## 🔐 Default Roles & Permissions

| Role | Capabilities |
|---|---|
| **Admin** | Full access — all modules including user management |
| **HR** | Employees, departments, positions, payroll (full), bonuses (full) |
| **Manager** | View employees, departments, positions; create/edit payroll & bonuses |
| **Staff** | View only — own profile, employees list, payroll (view), attendance (view) |

---

## 🗂 Project Structure

```
app/
├── Console/Commands/       # Artisan commands (cleanup, etc.)
├── Http/
│   ├── Controllers/
│   │   └── Dashboard/      # Feature controllers
│   └── Middleware/         # Role-based access, security headers
├── Models/                 # Eloquent models
├── Observers/              # Model event observers
├── Policies/               # Authorization policies
├── Services/               # Business logic (PayrollCalculatorService, etc.)
└── Traits/                 # Reusable traits (image processing, etc.)

resources/views/
├── auth/                   # Login page
├── dashboard/              # All dashboard views per module
├── layouts/                # Shared layouts (sidebar, navbar)
└── legal/                  # Privacy policy, terms of service
```

---

## 🤝 Contributing

Contributions, bug reports, and feature requests are welcome.

1. Fork the repository
2. Create a branch: `git checkout -b feature/your-feature`
3. Commit your changes: `git commit -m 'Add some feature'`
4. Push to the branch: `git push origin feature/your-feature`
5. Open a Pull Request

Please keep PRs focused — one feature or fix per PR.

---

## 🛡 Security

If you discover a security vulnerability, please open a **private** issue or contact the maintainer directly via GitHub instead of using the public issue tracker.

For production deployments:
- Always set `APP_ENV=production` and `APP_DEBUG=false`
- Use HTTPS
- Rotate the `APP_KEY` after initial setup
- Change default seeded credentials immediately

---

## 📄 License

S-Payroll is open source software released under the [MIT License](LICENSE).

You are free to use, modify, and distribute this software for personal or commercial use. Attribution is appreciated but not required.

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
