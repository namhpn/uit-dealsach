# 10-Minute Demo Script

| Segment | Time | Action | Speaking Script | Screen |
| --- | --- | --- | --- | --- |
| Public overview | 0:00-0:45 | Open `/` | "DealSach is a Vietnamese book price comparison website. The public user does not need an account." | Homepage |
| Catalog search | 0:45-1:45 | Search `Đắc`, open `/sach` filters | "The catalog supports Vietnamese search, retailer filter, category filter, stock filter, and pagination." | Catalog |
| Detail comparison | 1:45-3:00 | Open `Đắc Nhân Tâm` detail | "The detail page compares offers from four retailers and highlights the lowest in-stock effective price." | Detail table |
| Redirect logging | 3:00-3:45 | Click retailer button or show `/go/1` result | "Retailer links use a safe redirect. The request only passes an item id; the external URL is loaded from the database and the click is logged." | Browser and DB/query evidence |
| OTP tracking | 3:45-5:30 | Enter email, request OTP, verify, create rule | "Tracking uses OTP verification. For demo stability, the OTP and email are logged locally, so SMTP is not required." | OTP forms |
| Alert command | 5:30-6:30 | Run `php spark dealsach:alerts` twice | "The alert command compares the latest two full snapshot batches, creates alert events and email logs, and deduplicates repeated runs." | Terminal |
| Admin login | 6:30-7:15 | Open `/ds-admin/login`, login | "The admin panel is hidden and protected by session authentication." | Admin login |
| Admin dashboard | 7:15-8:00 | Show dashboard metrics | "Dashboard metrics include books, retailers, offers, tracking rules, clicks, alerts, failed jobs, and sign-ins." | Dashboard |
| Book CRUD/export | 8:00-9:00 | Open books, create/edit form, export CSV | "Only Book has full CRUD, matching the project scope. CSV export is admin-only." | Admin books |
| Technical evidence | 9:00-10:00 | Show README, UML, tests, visual QA | "The final package includes Docker setup, reset scripts, PHPUnit checks, visual QA screenshots, UML diagrams, and demo fallback plans." | README/docs |

## Fallback Plans

SMTP failure: use `email_logs` and visible `dev_otp` in JSON/UI as the official demo fallback.

Import failure: run `php spark db:seed DemoSeeder`; seeded snapshots are sufficient for catalog, comparison, tracking, and alerts.

Visual/network issue: use screenshots in `docs/evidence/screenshots` and the generated visual QA report.
