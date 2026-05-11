# P9 Integration QA Checklist

Date: 2026-05-11

## Reset And CLI Evidence

| Check | Command | Expected | Verified |
| --- | --- | --- | --- |
| Clean DB reset | `docker exec dealsach-app php spark migrate:refresh` | All migrations rollback and rerun from empty DB | PASS |
| Demo seed | `docker exec dealsach-app php spark db:seed DemoSeeder` | Admin, books, retailers, offers, snapshots, tracking, alerts, email logs | PASS |
| Import command | `docker exec dealsach-app php spark dealsach:crawl all` | Four retailer JSON files imported with completed crawl jobs | PASS |
| Alert command first run | `docker exec dealsach-app php spark dealsach:alerts` | Uses full snapshot batches `#2 -> #3`, sends 5 demo alerts | PASS |
| Alert command duplicate run | `docker exec dealsach-app php spark dealsach:alerts` | Sends 0 duplicate alerts | PASS |
| PHPUnit | `docker exec dealsach-app vendor/bin/phpunit --colors=never` | Full backend/frontend/P8 suite passes | PASS |
| Visual QA | `npm run qa:visual` | Desktop, tablet, mobile screenshots and layout checks pass | PASS |

## Demo Path

| ID | Area | Test Case | Expected Result | Status |
| --- | --- | --- | --- | --- |
| P9-01 | Public | Homepage loads at `/` | Vietnamese hero, search, stats, featured books render | PASS |
| P9-02 | Public | Catalog loads at `/sach` | Search, retailer/category/stock filters, cards, pagination render | PASS |
| P9-03 | Public | Filtered catalog query | Results page keeps selected filters and shows matching books | PASS |
| P9-04 | Public | Detail page `/sach/dac-nhan-tam` | Breadcrumbs, metadata, comparison table, lowest-price badge, OTP UI render | PASS |
| P9-05 | Redirect | `/go/{id}` | Looks up stored URL only, inserts `outbound_clicks`, returns 302 | PASS |
| P9-06 | Redirect | invalid retailer item | Returns safe 404, no raw exception | PASS |
| P9-07 | Redirect | inactive retailer item | Redirects back to book detail with friendly flash message | PASS |
| P9-08 | Tracking | OTP request | JSON success with dev OTP and email log fallback | PASS |
| P9-09 | Tracking | OTP verify and create rule | Creates active tracking rule after OTP verification | PASS |
| P9-10 | Tracking | Duplicate active rule | Service blocks duplicate active tracking for same book/email | PASS |
| P9-11 | Admin | Hidden login `/ds-admin/login` | Login form renders, public nav has no admin link | PASS |
| P9-12 | Admin | Protected dashboard | Unauthenticated user redirected to login; admin session sees dashboard | PASS |
| P9-13 | Admin | Book CRUD | List, create form, create flow, update/delete routes protected by CSRF | PASS |
| P9-14 | Admin | CSV export | Authenticated admin downloads books/activity CSV | PASS |
| P9-15 | Error | 404 page | Unknown route returns styled Vietnamese error response | PASS |
| P9-16 | UI | Responsive visual QA | No blocked Bootstrap assets, mojibake, clipped controls, or overflow | PASS |

## Bugs Found And Fixed

| Bug | Impact | Fix | Status |
| --- | --- | --- | --- |
| Bootstrap CSS SRI hash was stale | Browser blocked Bootstrap CSS, causing broken visual layout | Updated Bootstrap 5.3.3 CSS integrity hash in public/admin/error layouts | FIXED |
| Alert job compared one-item import jobs after `dealsach:crawl all` | Full reset demo could send 0 alerts after import | AlertService now prefers completed snapshot batches with at least 2 snapshots, then falls back to any two jobs | FIXED |
| Redirect logging lacked book/referrer/hash fields | P8 evidence was weaker and stored raw IP only | Added P8 migration columns and controller insert for `book_id`, `referrer`, `ip_hash` | FIXED |

## Remaining Risks

| Risk | Current Handling |
| --- | --- |
| SMTP credentials may not be available during demo | OTP and alerts write deterministic `email_logs` records as demo fallback |
| Import JSON is demo-sized | Seeded snapshot data remains the stable comparison dataset for alert demonstration |
| CDN availability | Visual QA catches blocked/missing Bootstrap resources before demo |
