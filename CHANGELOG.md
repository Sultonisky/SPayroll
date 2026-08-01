# Changelog

All notable changes to Scroll will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Open source rebranding with live demo banner on login page
- Demo account system with 4 pre-seeded accounts per role
- `is_demo` flag on users table with full RBAC enforcement for demo accounts
- `php artisan demo:reset` command for nightly demo environment reset
- Demo mode banner in dashboard layout with pulse indicator
- One-click credential auto-fill on login page
- Role-based sidebar navigation visibility (RBAC-aware)
- Detailed permission matrix per role (Admin, HR, Manager, Staff)
- Privacy Policy and Terms of Service pages (`/privacy-policy`, `/terms-of-service`)
- `Gate::before` exclusion for demo admin accounts to enforce policies correctly
- `LICENSE` (MIT), `CONTRIBUTING.md`, `SECURITY.md`, `CODE_OF_CONDUCT.md`
- GitHub issue templates and PR template

### Fixed
- HR and Manager roles blocked from login (now all 4 roles can authenticate)
- `Gate::authorize` called after data processing in `UserController::update` (moved before)
- Demo admin bypassing all policies via `Gate::before` — now correctly routed through policies

### Changed
- Login page fully translated to English
- Login form section centered vertically
- Footer updated with legal links, GitHub source code link
- README rewritten as open source self-hosted documentation with full RBAC matrix

---

## [0.1.0] - 2025-07-01

### Added
- Initial release
- Employee management (create, edit, soft delete, restore, export)
- Department and position management with per-type base salaries
- Payroll engine: bulk generate, preview, draft → approved → paid workflow
- Bulk approve and bulk mark-as-paid
- Payroll period overview with aggregate statistics
- Payroll CSV export (per-record and bank transfer format)
- Employee bonus management with approval workflow
- Four built-in roles: Admin, HR, Manager, Staff
- Laravel Policies for all major models
- Role middleware for route-level protection
- In-app notifications
- Profile management with photo upload (Supabase storage)
- Security headers middleware
- Soft delete with trash & restore across all major models
- Database seeders with realistic dummy data

---

[Unreleased]: https://github.com/Sultonisky/Scroll/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/Sultonisky/Scroll/releases/tag/v0.1.0
