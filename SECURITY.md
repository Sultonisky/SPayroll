# Security Policy

## Supported Versions

| Version | Supported |
|---|---|
| latest (main) | ✅ Active |
| older branches | ❌ No longer maintained |

We only maintain the latest version on the `main` branch. Please update to the latest release before reporting a vulnerability.

---

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub Issues.**

If you discover a security vulnerability in S-Payroll, please report it responsibly:

### Option 1 — GitHub Security Advisory (preferred)

Open a [private security advisory](https://github.com/Sultonisky/s-payroll/security/advisories/new) directly on GitHub. This keeps the disclosure private until a fix is available.

### Option 2 — Direct contact

Contact the maintainer via GitHub: [@Sultonisky](https://github.com/Sultonisky)

---

## What to Include

Please provide as much of the following as possible:

- **Type of vulnerability** (e.g., SQL injection, XSS, authentication bypass, privilege escalation)
- **Affected component** (e.g., `AuthController`, a specific route, a Blade template)
- **Steps to reproduce** — a minimal, reliable reproduction
- **Impact assessment** — what an attacker could achieve
- **Suggested fix** (optional, but appreciated)

---

## Response Timeline

| Stage | Target |
|---|---|
| Acknowledgement | Within 72 hours |
| Initial assessment | Within 5 business days |
| Fix or mitigation | Depends on severity — critical issues are prioritized |
| Public disclosure | After a fix is released and users have had time to update |

---

## Scope

The following are **in scope**:

- Authentication and authorization flaws
- RBAC / policy bypass
- SQL injection or mass assignment vulnerabilities
- XSS in any rendered view
- CSRF vulnerabilities
- Insecure direct object references
- Exposed sensitive data (credentials, PII, payroll figures)

The following are **out of scope**:

- Vulnerabilities in third-party dependencies (report directly to the dependency maintainer)
- Issues only reproducible with physical access to the server
- Theoretical vulnerabilities without a working proof of concept
- Self-hosted instances with misconfigured infrastructure (server hardening is the operator's responsibility)

---

## Production Hardening Checklist

For operators running self-hosted instances:

- Set `APP_ENV=production` and `APP_DEBUG=false`
- Use HTTPS with a valid TLS certificate
- Rotate `APP_KEY` after initial setup
- Change default seeded credentials immediately (`admin@spayroll.com`)
- Restrict database access to localhost or a private network
- Keep PHP, Laravel, and all Composer/npm dependencies updated
- Configure a firewall — only expose ports 80/443 publicly
- Use `storage:link` carefully — never expose the full storage directory
- Set up regular automated backups of the database

---

## Credits

We publicly credit security reporters in the release notes and CHANGELOG, unless the reporter prefers to remain anonymous.

Thank you for helping keep S-Payroll and its users safe.
