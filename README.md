# DealSach

DealSach is a Vietnamese-first book price comparison website built with CodeIgniter 4, PHP 8.4, MariaDB, Bootstrap 5, and Docker. The project is scoped for a capstone demo: public catalog and comparison, OTP tracking, import-first retailer data, daily price-drop alerts, redirect logging, and a hidden admin panel.

## Features

- Homepage, catalog search, retailer/category/stock filters, pagination.
- Book detail page with multi-retailer comparison and lowest in-stock price highlight.
- Four demo retailers: Fahasa, Nhasachphuongnam, Tiki, Shopee.
- Historical price snapshots and import-first crawl command.
- Guest OTP tracking flow with email-log/dev-mode fallback.
- Daily alert command with alert deduplication and email log records.
- Safe outbound redirect route `/go/{retailerItemId}` with click logging.
- Hidden admin login, dashboard metrics, Book CRUD, books CSV, activity CSV.
- PHPUnit frontend/operational smoke tests and Playwright visual QA.
- UML, screenshot evidence, demo script, reset scripts.

## Requirements

Docker setup:

- Docker
- Docker Compose v2
- Git

Manual fallback:

- PHP 8.4+
- Composer
- Nginx or Apache
- MySQL/MariaDB with `utf8mb4`
- PHP extensions: `intl`, `mbstring`, `mysqli`, `pdo_mysql`, `curl`, `openssl`, `fileinfo`, `dom`, `simplexml`, `zip`, `gd`

## Quick Start

```bash
git clone <repo-url> dealsach
cd dealsach
cp .env.example .env
docker compose up -d --build
docker exec dealsach-app composer install
docker exec dealsach-app php spark migrate
docker exec dealsach-app php spark db:seed DemoSeeder
docker exec dealsach-app php spark dealsach:crawl all
docker exec dealsach-app php spark dealsach:alerts
```

Open:

- App: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`
- Admin: `http://localhost:8080/ds-admin/login`

Admin demo account:

- Username: `admin`
- Password: `123456`

## Full Demo Reset

PowerShell:

```powershell
.\scripts\reset-demo.ps1
```

Bash:

```bash
bash scripts/reset-demo.sh
```

Equivalent commands:

```bash
docker exec dealsach-app php spark migrate:refresh
docker exec dealsach-app php spark db:seed DemoSeeder
docker exec dealsach-app php spark dealsach:crawl all
docker exec dealsach-app php spark dealsach:alerts
```

Run `php spark dealsach:alerts` a second time to demonstrate deduplication: the second run should send `0` duplicate alerts.

## Environment

Copy `.env.example` to `.env`, then adjust:

- `APP_PORT`, `PMA_PORT`, `DB_PORT`
- `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_ROOT_PASSWORD`
- `app.baseURL`
- `dealsach.adminPath`
- SMTP settings if real email is needed

For demo stability, OTP and alert emails are also recorded in `email_logs`, so real SMTP is optional.

## CLI Commands

```bash
docker exec dealsach-app php spark dealsach:crawl fahasa
docker exec dealsach-app php spark dealsach:crawl phuongnam
docker exec dealsach-app php spark dealsach:crawl tiki
docker exec dealsach-app php spark dealsach:crawl shopee
docker exec dealsach-app php spark dealsach:crawl all
docker exec dealsach-app php spark dealsach:alerts
```

Cron examples:

```cron
0 2 * * * docker exec dealsach-app php spark dealsach:crawl all
0 8 * * * docker exec dealsach-app php spark dealsach:alerts
```

## Testing And QA

PHPUnit:

```bash
docker exec dealsach-app vendor/bin/phpunit --colors=never
```

Visual QA:

```bash
npm install
npm run qa:visual
```

Visual QA checks desktop, tablet, and mobile screenshots for public pages, admin pages, mobile menus, and OTP flow. It fails on blocked Bootstrap assets, console errors, broken images, mojibake, horizontal overflow, clipped controls, and sidebar overlap.

## Documentation

- Scope: `docs/scope.md`
- UML package: `docs/uml.md`
- Integration QA: `docs/p9-integration-qa.md`
- Security review: `docs/p9-security-review.md`
- Demo script: `docs/demo-script.md`
- Final progress report: `docs/final-progress-report.md`
- Screenshot evidence: `docs/evidence/screenshots`

## Shared Hosting Deployment Note

Use CodeIgniter's two-directory layout.

Application files:

```text
/home/USER/dealsach-app/
```

Public web root:

```text
/home/USER/public_html/
```

Only the contents of `public/` should be copied into `public_html/`. Configure `public_html/index.php` to point to the application directory.

Production `.env` should use:

```dotenv
CI_ENVIRONMENT=production
app.forceGlobalSecureRequests=true
cookie.secure=true
cookie.httponly=true
cookie.samesite=Lax
```

Install optimized dependencies:

```bash
composer install --no-dev --optimize-autoloader
```

## Local LEMP Fallback

1. Create a MariaDB database with `utf8mb4_unicode_ci`.
2. Point Nginx/Apache document root to `public/`.
3. Run `composer install`.
4. Configure `.env` database and `app.baseURL`.
5. Run migrations, seeders, import, and alerts using local PHP:

```bash
php spark migrate
php spark db:seed DemoSeeder
php spark dealsach:crawl all
php spark dealsach:alerts
```

## Troubleshooting

- Bootstrap looks unstyled: run `npm run qa:visual`; the suite catches blocked CDN/SRI resources.
- Database connection fails: confirm Docker env variables and `.env` database values match.
- Alert sends `0`: run the full reset script, then run `php spark dealsach:alerts` again.
- OTP email not received: use the dev OTP shown by the JSON/UI flow and verify `email_logs`.
- Admin redirects to login: use `admin / 123456` after seeding `DemoSeeder`.

## Known Limitations

- Retailer data is import-first demo JSON, not live scraping.
- Email delivery is simulated through `email_logs` unless SMTP is configured.
- Only Book has full admin CRUD by scope.
- Public users do not have accounts, carts, checkout, or payment.
