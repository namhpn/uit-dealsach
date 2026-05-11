# Evidence Checklist

| Required Evidence | File Or Command | Status |
| --- | --- | --- |
| Homepage | `docs/evidence/screenshots/homepage.png` | PASS |
| Catalog search | `docs/evidence/screenshots/catalog-search.png` | PASS |
| Retailer filter result | `npm run qa:visual` report, catalog scenario | PASS |
| Stock filter result | `npm run qa:visual` report, catalog scenario | PASS |
| Pagination | `tests/feature/FrontendSmokeTest.php` catalog assertions | PASS |
| Book detail comparison | `docs/evidence/screenshots/book-detail-comparison.png` | PASS |
| Lowest price highlight | Detail screenshot and frontend smoke assertions | PASS |
| Out-of-stock offer | Seeded demo data and detail comparison table | PASS |
| Retailer redirect | `curl http://localhost:8080/go/1` returns stored retailer URL | PASS |
| Redirect/click log | `tests/feature/OperationalP8Test.php` | PASS |
| OTP request | `docs/evidence/screenshots/otp-flow.png` | PASS |
| OTP verification/tracking success | `tests/feature/FrontendSmokeTest.php` OTP flow | PASS |
| Alert command terminal output | `php spark dealsach:alerts` first run sends 5, second sends 0 | PASS |
| Alert event/email log result | `tests/feature/OperationalP8Test.php` | PASS |
| Admin login | `docs/evidence/screenshots/admin-login.png` | PASS |
| Admin dashboard | `docs/evidence/screenshots/admin-dashboard.png` | PASS |
| Book CRUD list | `docs/evidence/screenshots/admin-books.png` | PASS |
| Book create/edit | `docs/evidence/screenshots/book-form.png` | PASS |
| CSV export download | `tests/feature/FrontendSmokeTest.php` CSV assertions | PASS |
| Migration/seeder/import output | `scripts/reset-demo.ps1` / `scripts/reset-demo.sh` | PASS |
| Mobile responsive evidence | `docs/evidence/screenshots/mobile-home.png` | PASS |
| UML package | `docs/uml.md` | PASS |
| Demo script | `docs/demo-script.md` | PASS |
