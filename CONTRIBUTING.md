# Contributing to S-Payroll

First off — thank you for taking the time to contribute. S-Payroll is an open source, self-hosted payroll system and every contribution, no matter how small, makes a difference.

This guide covers everything you need to know to contribute effectively.

---

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Ways to Contribute](#ways-to-contribute)
- [Before You Start](#before-you-start)
- [Development Setup](#development-setup)
- [Branching & Workflow](#branching--workflow)
- [Commit Message Convention](#commit-message-convention)
- [Pull Request Guidelines](#pull-request-guidelines)
- [Coding Standards](#coding-standards)
- [Reporting Bugs](#reporting-bugs)
- [Suggesting Features](#suggesting-features)
- [Security Vulnerabilities](#security-vulnerabilities)

---

## Code of Conduct

This project follows a simple rule: **be respectful**. Harassment, discrimination, or hostile behavior of any kind will not be tolerated. Treat everyone as a collaborator, not a competitor.

---

## Ways to Contribute

You don't have to write code to contribute. Here are all the ways you can help:

- 🐛 **Report bugs** — open an issue with clear reproduction steps
- 💡 **Suggest features** — open a feature request issue
- 📝 **Improve documentation** — fix typos, clarify instructions, add examples
- 🧪 **Write tests** — help increase test coverage
- 🌐 **Add translations** — help localize the UI
- 🔧 **Fix bugs or implement features** — submit a pull request
- ⭐ **Star the repo** — helps with visibility

---

## Before You Start

Before starting significant work, please:

1. **Check existing issues and PRs** — your idea might already be in progress
2. **Open an issue first** for any non-trivial change to align on approach before writing code
3. **Keep PRs focused** — one feature or fix per pull request makes review much easier

---

## Development Setup

### Requirements

- PHP 8.3+
- Composer 2
- Node.js 18+ & npm
- MySQL 8 / MariaDB 10.6+ (or SQLite for local dev)

### Setup steps

```bash
# 1. Fork and clone the repository
git clone https://github.com/YOUR_USERNAME/s-payroll.git
cd s-payroll

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies and build assets
npm install
npm run build

# 4. Configure environment
cp .env.example .env
php artisan key:generate

# 5. Set up the database
# Edit .env with your DB credentials, then:
php artisan migrate --seed

# 6. Link storage
php artisan storage:link

# 7. Start the development server
composer run dev
```

> For SQLite (quick local setup), set `DB_CONNECTION=sqlite` in `.env` — no database server needed.

---

## Branching & Workflow

We use a simple feature branch workflow:

```
main          ← stable, production-ready code
feat/*        ← new features
fix/*         ← bug fixes
docs/*        ← documentation only
refactor/*    ← code restructuring without behavior change
chore/*       ← maintenance, dependency updates, tooling
```

**Steps:**

1. Fork the repository
2. Create your branch from `main`:
   ```bash
   git checkout -b feat/your-feature-name
   ```
3. Make your changes
4. Push to your fork and open a Pull Request against `main`

---

## Commit Message Convention

We follow [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>(<scope>): <short description>
```

**Types:**

| Type | When to use |
|---|---|
| `feat` | New feature |
| `fix` | Bug fix |
| `docs` | Documentation only |
| `style` | Formatting, whitespace (no logic change) |
| `refactor` | Code change that's neither a fix nor a feature |
| `test` | Adding or updating tests |
| `chore` | Build process, dependencies, tooling |
| `perf` | Performance improvements |

**Examples:**

```bash
feat(payroll): add bulk approve for draft payrolls
fix(auth): allow HR and manager roles to login
docs(readme): add RBAC permission matrix
refactor(policy): extract demo guard into reusable trait
chore(deps): update laravel/framework to 13.x
```

Keep the subject line under 72 characters. Use imperative mood ("add" not "added").

---

## Pull Request Guidelines

### Before submitting

- [ ] Code follows the project's coding standards (see below)
- [ ] You have run `php artisan migrate` if you added migrations
- [ ] You have tested your changes locally
- [ ] No unnecessary files are included (`.env`, IDE configs, `node_modules`, etc.)

### PR description template

```
## What does this PR do?
<!-- Brief description of the change -->

## Why?
<!-- Context and motivation -->

## How was it tested?
<!-- Steps to reproduce / test -->

## Related issue
<!-- Closes #123 -->
```

### Review process

- PRs will be reviewed by the maintainer within a reasonable timeframe
- Be prepared to discuss and revise based on feedback
- Small, focused PRs are reviewed faster than large ones

---

## Coding Standards

### PHP / Laravel

- Follow **PSR-12** coding style
- Use **Laravel Pint** for automatic formatting:
  ```bash
  ./vendor/bin/pint
  ```
- Controllers should be thin — business logic belongs in Services or dedicated classes
- Use `Gate::authorize()` at the top of controller methods before any data processing
- New models should include proper `$fillable`, `$casts`, and relationship methods
- Add soft deletes (`SoftDeletes`) to models that manage important records

### Blade / Frontend

- Keep Blade templates clean — avoid heavy PHP logic inside views
- Use `@can` / `@if(auth()->user()->hasRole(...))` for UI guards, not just backend
- Follow the existing naming convention for CSS classes

### Database

- Every schema change must have a migration
- Migrations must be reversible — always implement `down()`
- Use descriptive column names and add appropriate indexes
- Do not modify existing migrations — always create a new one

### RBAC

- New features must respect the existing role system (`admin`, `HR`, `manager`, `staff`)
- Add demo account guards (`isDemo()`) to any write operation that could corrupt shared demo data
- Update the sidebar visibility logic in `resources/views/layouts/sidebar.blade.php` if adding new menu items

---

## Reporting Bugs

Open a [GitHub Issue](https://github.com/Sultonisky/SPayroll/issues) with:

- **Title**: short, descriptive summary
- **Environment**: PHP version, Laravel version, OS, browser
- **Steps to reproduce**: numbered steps that reliably trigger the bug
- **Expected behavior**: what should happen
- **Actual behavior**: what actually happens
- **Screenshots or logs**: if applicable

---

## Suggesting Features

Open a [GitHub Issue](https://github.com/Sultonisky/SPayroll/issues) with the label `enhancement` and include:

- What problem does this feature solve?
- Who would benefit from it?
- How do you envision it working?
- Are there any alternatives you've considered?

Features that align with the project's focus — **remote-first payroll for small to mid-sized teams** — are prioritized.

---

## Security Vulnerabilities

**Please do not open a public issue for security vulnerabilities.**

Instead, report them privately by:
- Opening a [GitHub Security Advisory](https://github.com/Sultonisky/SPayroll/security/advisories/new)
- Or contacting the maintainer directly via GitHub

All security reports will be addressed promptly and credited to the reporter.

---

## Questions?

If you're unsure about something, open a [Discussion](https://github.com/Sultonisky/SPayroll/discussions) or ask in the issue you're working on. We're happy to help.

---

_Thank you for making S-Payroll better._ 🙏
