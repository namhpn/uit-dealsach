# DealSach Scope

DealSach is a Vietnamese-first book price comparison website for a capstone demo. The implementation uses CodeIgniter 4, PHP 8.4, MariaDB, Bootstrap 5, Docker, server-rendered pages, and import-first retailer data.

## In Scope

1. Public homepage, catalog search/filter/pagination, and book detail comparison.
2. Four retailers: Fahasa, Nhasachphuongnam, Tiki, Shopee.
3. Historical price snapshots and lowest available effective price logic.
4. Guest email OTP tracking with dev-mode email log fallback.
5. Daily alert command with alert event and email log records.
6. Safe outbound redirect logging through `/go/{retailerItemId}`.
7. Hidden admin login/logout with native session auth.
8. Admin dashboard, Book CRUD, and CSV exports.
9. Docker setup, reset scripts, tests, visual QA, UML, README, and demo script.

## Out Of Scope

1. Cart, checkout, payment, and public user accounts.
2. Multi-role admin or CodeIgniter Shield.
3. Real affiliate integration.
4. Live scraping dependency during demo.
5. Advanced analytics dashboards.

## Demo Account

Admin URL: `http://localhost:8080/ds-admin/login`

Username: `admin`

Password: `123456`
