# Tasks.md Verification Report

Date: 2026-05-11

Source: `UIT-DEALSACH-PROMPT/uit-dealsach-prompts/tasks.md`

## Commands Run

```bash
docker compose ps
docker exec dealsach-app php spark migrate:refresh
docker exec dealsach-app php spark db:seed DemoSeeder
docker exec dealsach-app php spark dealsach:crawl all
docker exec dealsach-app php spark dealsach:alerts
docker exec dealsach-app php spark dealsach:alerts
docker exec dealsach-app vendor/bin/phpunit --colors=never
npm run qa:visual
docker exec dealsach-app php spark routes
```

Latest results:

- Docker stack: `dealsach-app`, `dealsach-nginx`, `dealsach-db`, `dealsach-pma` all running.
- Reset/migrate/seed/import: PASS.
- Alert first run: `Sent: 5`.
- Alert duplicate run: `Sent: 0`.
- PHPUnit: `OK (13 tests, 116 assertions)`.
- Visual QA: `Visual QA passed`.
- Main HTTP probes: `/`, `/sach`, `/sach/dac-nhan-tam`, `/ds-admin/login` return 200; unknown route returns 404; `/go/1` returns 302 to stored retailer URL.

## Database Verification

| Check | Result |
| --- | --- |
| Application tables | 19 |
| Books | 24 |
| Retailers | 4 |
| Publishers | 6 |
| Categories | 9 |
| Retailer items | 100 |
| Price snapshots | 292 |
| Retailer item changes | 20 |
| Active tracking rules | 5 |
| Alert events after dedupe verification | 10 |
| Email logs | 10 |
| Books with categories | 24 |
| Books with publisher FK | 24 |
| Out-of-stock offers | 9 |
| Discounted offers | 79 |
| Listed-price fallback offers | 21 |
| Vietnamese text query | `Đắc Nhân Tâm` found |

## Package Status

| Package | Status | Evidence | Notes |
| --- | --- | --- | --- |
| P1 Scope/architecture/demo flow | PASS with manual caveats | `docs/scope.md`, `docs/uml.md`, `README.md`, `docs/demo-script.md` | GitHub board/team communication/member-run evidence is external and cannot be proven from local repo. |
| P2 Foundation/environment/layouts | PASS | Docker stack running, routes table, public/admin layouts, `.env.example`, custom errors, `format_vnd(125000)=125.000đ` | Visual QA confirms Bootstrap CSS loads after SRI fix. |
| P3 Database/schema/seed data | PASS | 19 tables, seed counts above, clean `migrate:refresh`, `DemoSeeder` | Includes Vietnamese data, categories, publishers, offers, snapshots, changes, alert scenarios, import JSON files. |
| P4 Public catalog/detail UI | PASS | HTTP probes, `FrontendSmokeTest`, visual QA screenshots | Homepage, catalog, filters, pagination, breadcrumbs, SEO meta, detail comparison, lowest price, OTP UI verified. |
| P5 Import pipeline | PASS | `php spark dealsach:crawl all`, `ImportService`, crawl jobs/snapshots/errors/changes | Uses import-first JSON source files for four retailers. |
| P6 Admin auth/dashboard/Book CRUD/CSV | PASS | `FrontendSmokeTest`, routes table, visual QA screenshots | Admin protection redirects unauthenticated user; dashboard metrics and CSV export verified. |
| P7 OTP tracking workflow | PASS | `FrontendSmokeTest` OTP flow, `OtpService`, `TrackingService`, `email_logs` fallback | Request, verify, create rule, duplicate prevention, signed disable route implemented. |
| P8 Alert job/redirect logging | PASS | `OperationalP8Test`, alert first/second command runs, `/go/1` 302, click DB log | Alert dedupe uses snapshot pair columns; redirect uses DB URL only and logs hashed IP/referrer/book. |
| P9 Integration hardening/security | PASS | `docs/p9-integration-qa.md`, `docs/p9-security-review.md`, full reset + tests + visual QA | Found and fixed stale Bootstrap SRI and alert batch-selection issue. |
| P10 README/UML/evidence/demo script | PASS with manual caveats | `README.md`, `docs/uml.md`, `docs/demo-script.md`, `docs/evidence-checklist.md`, screenshots | “Clean GitHub commits” and live rehearsal are external process evidence, not locally verifiable. |

## Minimum Final Version Checklist

| Requirement | Status |
| --- | --- |
| CodeIgniter 4 app running | PASS |
| MySQL/MariaDB database with migrations | PASS |
| Seeders with Vietnamese data | PASS |
| Import command | PASS |
| Vietnamese-first Bootstrap UI | PASS |
| Homepage | PASS |
| Catalog search/filter/pagination | PASS |
| Book detail comparison | PASS |
| Lowest price with VND formatting | PASS |
| Out-of-stock handling | PASS |
| Historical price snapshots | PASS |
| Four retailers | PASS |
| `php spark dealsach:crawl all` | PASS |
| Hidden admin login/logout | PASS |
| Admin dashboard | PASS |
| Full Book CRUD with flash messages | PASS |
| OTP tracking with dev log fallback | PASS |
| `php spark dealsach:alerts` | PASS |
| Redirect logging | PASS |
| CSV export | PASS |
| Custom 404/500 pages | PASS |
| SEO meta tags and breadcrumbs | PASS |
| Book cover fallback | PASS |
| README | PASS |
| UML package | PASS |
| Demo script | PASS |
| Screenshots | PASS |

## Manual/External Evidence Still Needed

These are required by `tasks.md` but cannot be verified from the local workspace alone:

1. GitHub Project board or issue list screenshots/links.
2. Confirmation that all four team members ran `docker compose up -d`.
3. Team communication channel setup evidence.
4. Final GitHub commit history cleanliness after committing the current work.
5. Human rehearsal confirmation for the 10-minute demo.
