# Architecture Overview

This document serves as a critical, living template designed to equip agents with a rapid and comprehensive understanding of the codebase's architecture, enabling efficient navigation and effective contribution from day one. Update this document as the codebase evolves.

---

## 1. Project Structure

```
uit-dealsach/                        # Project root
├── app/                             # CodeIgniter 4 application layer
│   ├── Common.php                   # Global helper overrides
│   ├── Config/                      # CI4 configuration files
│   │   ├── Routes.php               # Full route map (public + admin + CLI)
│   │   ├── Filters.php              # Filter aliases (adminAuth, adminGuest, throttle, etc.)
│   │   ├── Database.php             # DB connection settings (read from .env)
│   │   ├── Email.php                # SMTP settings
│   │   └── ...                      # Other CI4 config files
│   ├── Controllers/                 # HTTP request handlers
│   │   ├── BaseController.php       # Shared controller base
│   │   ├── Public/                  # (planned) Public-facing controllers
│   │   │   ├── HomeController.php
│   │   │   ├── BookController.php   # Handles both /sach (catalog) and /sach/(:segment) (detail)
│   │   │   ├── TrackingController.php
│   │   │   └── RedirectController.php
│   │   └── Admin/                   # (planned) Admin-only controllers
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       ├── BookCrudController.php
│   │       ├── ExportController.php
│   │       └── AjaxController.php
│   ├── Models/                      # CI4 models (planned; currently empty)
│   │   ├── AdminUserModel.php
│   │   ├── AdminAuthLogModel.php
│   │   ├── PublisherModel.php
│   │   ├── CategoryModel.php
│   │   ├── BookModel.php
│   │   ├── BookCategoryModel.php
│   │   ├── AuthorModel.php
│   │   ├── BookAuthorModel.php
│   │   ├── RetailerModel.php
│   │   ├── RetailerItemModel.php
│   │   ├── RetailerItemChangeModel.php
│   │   ├── PriceSnapshotModel.php
│   │   ├── CrawlJobModel.php
│   │   ├── CrawlJobErrorModel.php
│   │   ├── TrackingRuleModel.php
│   │   ├── OtpRequestModel.php
│   │   ├── AlertEventModel.php
│   │   ├── EmailLogModel.php
│   │   └── OutboundClickModel.php
│   ├── Views/                       # PHP view templates
│   │   ├── layouts/
│   │   │   ├── public.php           # Public layout (Bootstrap 5, nav, footer)
│   │   │   └── admin.php            # Admin layout (Bootstrap 5, sidebar)
│   │   ├── public/
│   │   │   ├── home.php             # Vietnamese homepage
│   │   │   ├── catalog_placeholder.php
│   │   │   └── detail_placeholder.php
│   │   ├── admin/
│   │   │   ├── login.php            # Hidden admin login page
│   │   │   └── dashboard_placeholder.php
│   │   └── errors/
│   │       ├── html/                # Custom HTML error pages (404, 500)
│   │       └── cli/                 # CLI error output
│   ├── Filters/                     # Custom CI4 request filters (planned)
│   │   ├── AdminAuthFilter.php      # Protect admin routes
│   │   ├── AdminGuestFilter.php     # Redirect authenticated admin from login
│   │   ├── VerifiedEmailFilter.php  # Gate tracking creation behind OTP verify
│   │   └── SignedTrackingTokenFilter.php # Validate disable-tracking signed token
│   ├── Helpers/
│   │   └── currency_helper.php      # format_vnd() - VND currency formatting
│   ├── Libraries/                   # (reserved for custom library classes)
│   ├── ThirdParty/                  # (reserved for bundled third-party code)
│   └── Database/
│       ├── Migrations/              # Database migration files (planned)
│       └── Seeds/                   # Database seeders (planned)
├── docker/
│   ├── php/Dockerfile               # PHP 8.4-FPM image definition
│   └── nginx/default.conf           # Nginx virtual host for CI4
├── public/                          # Web root (only this dir is public-facing)
│   ├── index.php                    # CI4 front controller
│   ├── .htaccess                    # Apache rewrite rules
│   ├── robots.txt
│   └── favicon.ico
├── tests/                           # PHPUnit test suites
├── writable/                        # CI4 writable directory (logs, cache, sessions)
├── vendor/                          # Composer dependencies
├── docker-compose.yml               # Docker stack definition
├── composer.json                    # PHP dependency manifest
├── .env.example                     # Environment variable template
├── .gitignore
├── README.md                        # Setup guide and project overview
└── ARCHITECTURE.md                  # This document
```

---

## 2. High-Level System Diagram

```
                     ┌──────────────────────────────┐
                     │        Browser / Client        │
                     └────────────┬─────────────────┘
                                  │ HTTP/HTTPS
                     ┌────────────▼─────────────────┐
                     │   Nginx (dealsach-nginx)       │
                     │   Reverse proxy + static       │
                     └────────────┬─────────────────┘
                                  │ FastCGI
                     ┌────────────▼─────────────────┐
                     │   PHP-FPM 8.4 (dealsach-app)  │
                     │   CodeIgniter 4 application    │
                     │  ┌─────────┐  ┌────────────┐  │
                     │  │ Public  │  │   Admin    │  │
                     │  │ Routes  │  │   Routes   │  │
                     │  └────┬────┘  └─────┬──────┘  │
                     │       │             │          │
                     │  ┌────▼─────────────▼──────┐  │
                     │  │  Services / Models Layer  │  │
                     │  └────────────┬─────────────┘  │
                     └───────────────┼───────────────┘
                                     │ MySQLi/PDO
                     ┌───────────────▼───────────────┐
                     │  MariaDB 11 (dealsach-db)      │
                     │  Database: dealsach            │
                     └───────────────────────────────┘

  External integrations:
  ┌──────────────────────────────────────────────────┐
  │  CLI (cron / manual)  →  PHP-FPM container       │
  │  php spark dealsach:crawl all                    │
  │  php spark dealsach:alerts                       │
  └──────────────────────────────────────────────────┘

  ┌─────────────────────────┐
  │  Retailer Websites      │
  │  Fahasa, Phuong Nam,    │─── HTTP scrape ──▶ CrawlImportService
  │  Tiki, Shopee           │
  └─────────────────────────┘

  ┌─────────────────────────┐
  │  SMTP Server            │◀─── MailService ── OtpService / AlertService
  └─────────────────────────┘

  Dev-only:
  ┌─────────────────────────┐
  │  phpMyAdmin (pma)       │◀─── port 8081 ── browser
  └─────────────────────────┘
```

---

## 3. Core Components

### 3.1. Frontend (Server-Rendered Views)

**Name:** Public Web UI + Admin Panel

**Description:** Server-rendered PHP views using CodeIgniter 4's native View system. The public interface is Vietnamese-first and presents the homepage, book catalog with search/filters/pagination, and a book detail page with multi-retailer price comparison. The admin panel is a hidden, session-gated dashboard providing book CRUD, metrics, CSV exports, and auth logs. Selective AJAX is used for OTP flows and admin interactions. There is no SPA or JavaScript framework.

**Technologies:** PHP 8.4 (view templates), Bootstrap 5 (CDN), vanilla JavaScript (AJAX), CodeIgniter 4 View Cells/layouts, `currency_helper` (VND formatting)

**Deployment:** Served via Nginx → PHP-FPM within Docker; deployable to shared hosting using the two-directory CodeIgniter layout (see README §15).

---

### 3.2. Backend

#### 3.2.1. Public Application

**Name:** Public HTTP Application

**Description:** Handles all guest-facing HTTP requests. Responds to search, catalog pagination, book detail, OTP send/verify, tracking rule creation/disabling, and outbound redirect logging. Stateless for anonymous browsing; OTP-gated session for tracking creation. All reads come from pre-crawled MySQL data — no live crawls on page load.

**Technologies:** PHP 8.4, CodeIgniter 4.7+, MySQLi driver, CI4 Throttle filter, CI4 CSRF filter

**Key Controllers (planned):**
- `Public\HomeController` — Vietnamese homepage
- `Public\BookController` — Catalog listing (`/sach`) AND book detail/comparison (`/sach/(:segment)`); no separate CatalogController
- `Public\TrackingController` — OTP request/verify, tracking create/disable
- `Public\RedirectController` — Outbound click logging and redirect

**Deployment:** Docker (`dealsach-app` container, PHP-FPM), Nginx reverse proxy

---

#### 3.2.2. Admin Panel

**Name:** Hidden Admin Panel

**Description:** Session-authenticated admin area accessed at a hidden, configurable URL prefix (`dealsach.adminPath` in `.env`, default: `ds-admin`). Provides canonical book CRUD, dashboard metrics aggregation, CSV exports, and admin auth log visibility. No public link to the admin area is exposed anywhere in the public UI.

**Technologies:** PHP 8.4, CodeIgniter 4, Bootstrap 5 (admin layout), AJAX (live book search, delete confirmation)

**Key Controllers (planned):**
- `Admin\AuthController` — Login (`GET/POST ds-admin/login`), logout, auth log
- `Admin\DashboardController` — Metrics aggregation
- `Admin\BookCrudController` — Full CRUD for canonical books (7 routes)
- `Admin\ExportController` — CSV book catalog (`exports/books.csv`) + activity summary (`exports/activity.csv`)
- `Admin\AjaxController` — Admin AJAX endpoints (live search, delete confirmation)

**Deployment:** Same container as public application

---

#### 3.2.3. CLI Commands (Spark)

**Name:** Background / Scheduled CLI Commands

**Description:** CodeIgniter Spark commands invoked via `php spark`. Not accessible over HTTP. Two main commands: the crawler/importer (fetches and normalizes retailer data into MySQL) and the alerts processor (compares price snapshots and sends daily batch emails).

**Technologies:** PHP 8.4, CodeIgniter 4 Commands, cURL (retailer scraping), CI4 Email library (SMTP)

**Key Commands (planned):**
- `dealsach:crawl {retailer|all}` — Crawl/import retailer data (Fahasa, Phuongnam, Tiki, Shopee); writes `retailer_items`, `price_snapshots`, `crawl_jobs`
- `dealsach:alerts` — Compares newest vs previous lowest effective price; creates `alert_events`; sends batch email

**Deployment:** Executed in `dealsach-app` Docker container via host crontab or manual `docker exec`

---

### 3.3. Service Layer

The service layer decouples business logic from controllers. All planned services live under `app/Libraries/` or a dedicated `app/Services/` directory:

| Service | Responsibility |
|---|---|
| `CatalogService` | Public catalog query, search, filters (retailer, category, stock), pagination |
| `ComparisonService` | Canonical book + retailer offers + lowest effective price |
| `CategoryService` | Category tree, book-category assignment, catalog category filter |
| `CrawlImportService` | Orchestrates retailer import jobs, writes crawl jobs, snapshots, and change logs |
| `RetailerParserService` | Common parsing interface for retailer adapters |
| `FahasaImportService` | Fahasa-specific extraction/import |
| `PhuongNamImportService` | Nhasachphuongnam extraction/import |
| `TikiImportService` | Tiki extraction/import |
| `ShopeeImportService` | Shopee extraction/import |
| `MatchingService` | ISBN-first match; title + author fallback; confidence scoring |
| `SnapshotService` | Write `price_snapshots`, compute effective price, track field changes in `retailer_item_changes` |
| `OtpService` | Generate, hash, store, throttle, verify 6-digit OTP |
| `TrackingService` | Create/disable guest tracking rules; dedup by email+book |
| `AlertService` | Price-drop detection and alert event creation |
| `MailService` | SMTP send for OTP emails and daily alert batch; writes to `email_logs` |
| `RedirectService` | Log outbound click and return retailer URL |
| `DashboardService` | Aggregate admin dashboard metrics |
| `ReportService` | Generate CSV content for book catalog and activity |
| `AdminAuthService` | Password verify, session create, audit log write |
| `SlugService` | Vietnamese-safe canonical URL slug generation |
| `TextNormalizeService` | Normalize Vietnamese titles/authors for matching |

---

## 4. Data Stores

### 4.1. Primary Application Database

**Name:** DealSach MariaDB

**Type:** MariaDB 11 (MySQL-compatible)

**Purpose:** Single source of truth for all application data. Public pages read exclusively from pre-imported data (no live scraping on request). All pricing history is retained as time-series snapshots.

**Charset/Collation:** `utf8mb4` / `utf8mb4_unicode_ci` everywhere (Vietnamese support)

**Key Tables (19 total):**

| Table | Model | Description |
|---|---|---|
| `admin_users` | `AdminUserModel` | Single-role admin accounts (bcrypt password) |
| `admin_auth_logs` | `AdminAuthLogModel` | Login audit trail |
| `publishers` | `PublisherModel` | Normalized publisher master (replaces raw text publisher field in books) |
| `categories` | `CategoryModel` | Book genres/categories with self-referencing parent_id for subcategories |
| `books` | `BookModel` | Canonical book master; `publisher_id` FK → `publishers`; supports soft delete via `deleted_at` |
| `book_categories` | `BookCategoryModel` | Book ↔ Category junction; `is_primary` flag |
| `authors` | `AuthorModel` | Author master |
| `book_authors` | `BookAuthorModel` | Book ↔ Author junction |
| `retailers` | `RetailerModel` | Fixed 4 retailers: Fahasa, Nhasachphuongnam, Tiki, Shopee |
| `retailer_items` | `RetailerItemModel` | Retailer-specific products linked to canonical books |
| `price_snapshots` | `PriceSnapshotModel` | Historical price per retailer item per crawl job |
| `retailer_item_changes` | `RetailerItemChangeModel` | Field-level change log per import (price, stock, title changes) |
| `crawl_jobs` | `CrawlJobModel` | Import/crawl operational log per run |
| `crawl_job_errors` | `CrawlJobErrorModel` | Row-level error details per crawl run |
| `tracking_rules` | `TrackingRuleModel` | Guest email-book price-drop subscriptions |
| `otp_requests` | `OtpRequestModel` | 6-digit OTP records (hashed); 10-min TTL, 5-attempt limit |
| `alert_events` | `AlertEventModel` | Price-drop detections and email send results |
| `email_logs` | `EmailLogModel` | All outbound email records (OTP + alert); status tracking |
| `outbound_clicks` | `OutboundClickModel` | Simulated affiliate redirect log |

**Key Business Rules:**
- `effective_price = discounted_price` (if valid) else `listed_price`
- Out-of-stock items are visible but excluded from the lowest-price highlight
- One snapshot per retailer item per crawl job
- One active tracking rule per `(book_id, email_normalized)` pair

**Deployment:** Docker volume `dealsach-db-data` (persistent), port exposed via `DB_PORT` in `.env`

---

### 4.2. Session Store

**Name:** File-based Session

**Type:** CI4 FileHandler (filesystem)

**Purpose:** Admin authentication session. Sessions are stored in `writable/session/` inside the PHP container. Cookie name: `dealsach_session`. Session TTL: 7200 s (2 h).

---

### 4.3. Cache

**Type:** CI4 Page Cache (file-based, optional)

**Purpose:** CI4's built-in `PageCache` filter is configured in `Filters.php` required chain. Not actively used in development; can be enabled per route in production.

---

## 5. External Integrations / APIs

| Service | Purpose | Integration Method |
|---|---|---|
| **Fahasa** (fahasa.com) | Book price & availability data | HTTP scraping via cURL (`FahasaImportService`) |
| **Nhasachphuongnam** (nhasachphuongnam.com) | Book price & availability data | HTTP scraping via cURL (`PhuongNamImportService`) |
| **Tiki** (tiki.vn) | Book price & availability data | HTTP scraping via cURL / Tiki API if available (`TikiImportService`) |
| **Shopee** (shopee.vn) | Book price & availability data | HTTP scraping via cURL (`ShopeeImportService`) |
| **SMTP Server** | OTP emails and daily price-drop alert batch | CI4 Email library via SMTP (configured in `.env`: `email.*`) |

> **Note:** Retailer integration is import-first (CLI). The public application never makes live outbound HTTP calls to retailers; it only reads from stored MySQL data.

---

## 6. Deployment & Infrastructure

### 6.1. Development (Docker)

**Stack:** Docker Compose v2

| Container | Image | Role | Port |
|---|---|---|---|
| `dealsach-app` | Custom PHP 8.4-FPM (`docker/php/Dockerfile`) | CI4 application + CLI | — |
| `dealsach-nginx` | `nginx:alpine` | Reverse proxy / web server | `APP_PORT` (default 8080) |
| `dealsach-db` | `mariadb:11` | Primary database | `DB_PORT` (default 3306) |
| `dealsach-pma` | `phpmyadmin:latest` | Database management UI | `PMA_PORT` (default 8081) |

All containers share the `dealsach-network` bridge network.

**CI/CD Pipeline:** Not yet configured. Target: GitHub Actions (planned).

**Monitoring & Logging:** CI4 file-based logger (`writable/logs/`); `docker logs` per container. `logger.threshold` configurable in `.env`.

### 6.2. Production (Shared Hosting)

**Layout:** Two-directory CodeIgniter structure:

```
/home/USER/dealsach-app/    ← Application files (app/, vendor/, writable/)
/home/USER/public_html/     ← Only contents of public/ (index.php, .htaccess, assets)
```

**Required `.env` overrides for production:**

```dotenv
CI_ENVIRONMENT = production
app.forceGlobalSecureRequests = true
cookie.secure = true
cookie.httponly = true
cookie.samesite = Lax
```

**Composer install for production:**

```bash
composer install --no-dev --optimize-autoloader
```

**Scheduled Jobs (crontab on host or shared hosting cron):**

```cron
0 2 * * * docker exec dealsach-app php spark dealsach:crawl all
0 8 * * * docker exec dealsach-app php spark dealsach:alerts
```

---

## 7. Security Considerations

| Concern | Implementation |
|---|---|
| **Authentication** | Admin-only session auth via `password_hash()` / `password_verify()` (bcrypt). No public user accounts. |
| **Authorization** | Single-role (Admin). `adminAuth` filter guards all authenticated admin routes. `adminGuest` prevents authenticated admin from re-accessing the login page. |
| **CSRF Protection** | CI4 CSRF filter (`cookie` mode, `tokenRandomize = true`) applied per-route on all state-changing POST endpoints. |
| **OTP Security** | 6-digit OTP, stored hashed only. 10-minute TTL, one-time use, max 5 attempts, 60-second resend cooldown. |
| **Rate Limiting** | CI4 `throttle` filter applied to: `otp-request`, `otp-verify`, `tracking-create`, `admin-login`, `redirect`. |
| **Admin URL Obfuscation** | Admin prefix (`ds-admin`) is configurable via `.env`. No public link exposes the admin path. |
| **Data Encryption in Transit** | TLS enforced in production via `app.forceGlobalSecureRequests = true` and `cookie.secure = true`. |
| **Signed Tokens** | Tracking rule disable flow uses a `signedTrackingToken` filter to validate a signed URL token. |
| **Audit Logging** | All admin login attempts (success and failure) are written to `admin_auth_logs`. |
| **Input Validation** | CI4 Validation library. `InvalidChars` filter available. |
| **Vietnamese Encoding** | All tables use `utf8mb4` / `utf8mb4_unicode_ci` to prevent encoding-related injection surface. |

---

## 8. Development & Testing Environment

**Local Setup:** See [README.md](./README.md) — Docker-first setup in §2–§8. Manual LEMP/XAMPP/MAMP fallback in README §15.

**Testing Frameworks:**
- PHPUnit 10.5+ (`vendor/bin/phpunit`)
- FakerPHP (seeder/test data generation)
- vfsStream (virtual filesystem mocking)

**Run tests:**

```bash
docker exec -it dealsach-app vendor/bin/phpunit
```

**Code Quality Tools:**
- PSR-4 autoloading enforced via Composer
- CI4 debug toolbar (active in `development` environment)
- PHP `intl`, `mbstring` extensions required for Vietnamese string handling

**Key `.env` switches:**

| Variable | Dev value | Production value |
|---|---|---|
| `CI_ENVIRONMENT` | `development` | `production` |
| `app.forceGlobalSecureRequests` | `false` | `true` |
| `dealsach.adminPath` | `ds-admin` | *(change for production)* |
| `crawler.enabled` | `true` | `true` |
| `logger.threshold` | `4` | `4` |

---

## 9. Future Considerations / Roadmap

- **Controller scaffolding:** All planned controllers (`Public\HomeController`, `Public\BookController`, `Public\TrackingController`, `Public\RedirectController`, `Admin\AuthController`, `Admin\DashboardController`, `Admin\BookCrudController`, `Admin\ExportController`) are currently stubs or not yet created. Implementation follows the 10-stage plan in `uit-dealsach-prompts/P1/implementationPlan.md`.
- **Database migrations:** The `Migrations/` and `Seeds/` directories are empty. Migration files for all **19 planned tables** must be generated (see `prd.md` §4 for schema and `tasks.md` P3 for migration order).
- **Service layer implementation:** The full service list (21 services) is planned but not yet implemented. Currently only the `currency_helper` and view scaffolding exist.
- **AJAX hardening:** OTP send/verify and admin AJAX endpoints are designed but not wired to real controllers.
- **CI/CD pipeline:** GitHub Actions for automated testing and deployment is planned but not yet configured.
- **Real crawler adapters:** Retailer-specific import adapters (`FahasaImportService`, etc.) depend on each retailer's HTML structure and may require maintenance as those sites change.
- **Shared-hosting deployment scripts:** Packaging and deployment automation scripts are planned.
- **UML/ERD documentation:** Use case, activity, sequence, class, component, ERD, and deployment diagrams are grading requirements (see `uit-dealsach-prompts/P1/implementationPlan.md` §8).

---

## 10. Project Identification

| Field | Value |
|---|---|
| **Project Name** | DealSach |
| **Description** | Vietnamese-first book price comparison website |
| **Repository URL** | *(set when published to GitHub)* |
| **Prompts / Docs Repo** | `uit-dealsach-prompts` (sibling directory) |
| **Primary Tech Stack** | PHP 8.4, CodeIgniter 4.7+, MariaDB 11, Bootstrap 5, Docker |
| **Primary Contact / Team** | UIT Capstone Team |
| **Date of Last Update** | 2026-04-26 (updated: 19-table schema, route naming, controller namespaces) |

---

## 11. Glossary / Acronyms

| Term / Acronym | Definition |
|---|---|
| **CI4** | CodeIgniter 4 — the PHP MVC framework used by this project |
| **VND** | Vietnamese Đồng — the currency displayed throughout the UI |
| **OTP** | One-Time Password — 6-digit code sent by email to verify guest identity for tracking |
| **Canonical Book** | A single deduplicated book record in the `books` table, matched across multiple retailer listings |
| **Retailer Item** | A retailer-specific product listing (`retailer_items`) linked to a canonical book |
| **Price Snapshot** | A single row in `price_snapshots` capturing listed price, discounted price, and availability at a point in time |
| **Effective Price** | `discounted_price` if valid and available, otherwise `listed_price` — the price used for comparison |
| **Crawl Job** | One execution of `dealsach:crawl` for a single retailer, logged in `crawl_jobs` |
| **Alert Event** | A row in `alert_events` created when the effective price drops below the previous snapshot's lowest |
| **Admin Path** | The hidden URL prefix for the admin panel, configured via `dealsach.adminPath` in `.env` |
| **Spark** | CodeIgniter 4's CLI tool (`php spark`) used for migrations, seeds, and custom commands |
| **LEMP** | Linux, Nginx, MySQL/MariaDB, PHP — the server stack used locally and in production |
| **ISBNFirst Matching** | Strategy where `matching_service` tries to match books by ISBN before falling back to title + author similarity |
| **Soft Delete** | Marking a record as deleted (via `deleted_at` or `status`) without removing it from the database |
| **adminAuth** | CI4 filter alias that verifies an active admin session before allowing access to protected routes |
| **adminGuest** | CI4 filter alias that redirects already-authenticated admins away from the login page |
| **throttle** | CI4 rate-limiting filter applied to OTP, tracking, and admin login routes |
| **signedTrackingToken** | A URL-safe HMAC-signed token used to authorize tracking rule disabling without requiring admin auth |
| **format\_vnd()** | Global helper function (`currency_helper.php`) that formats an integer amount as Vietnamese Đồng (e.g. `199.000₫`) |
