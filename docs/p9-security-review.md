# P9 Security Review

Date: 2026-05-11

| Item | Risk Level | Current Status | Verification |
| --- | --- | --- | --- |
| Admin authentication | Medium | Native session auth with `admin_logged_in`, hidden `/ds-admin` path, password verification | Admin routes use `adminAuth`; guest routes use `adminGuest` |
| Password storage | High | Seeder stores hashed admin password, login uses `password_verify` | Admin demo login passes through controller, no plain password lookup |
| CSRF | Medium | Enabled on admin login/logout, Book CRUD writes, OTP/tracking writes | Route filters include `csrf` on mutating routes |
| OTP verification | Medium | OTP hashes stored, session must contain verified email/book before tracking create | `verifiedEmail` filter and `TrackingService` both enforce verification |
| OTP/rate abuse | Medium | Throttle filter applied to OTP request/verify and tracking create | Routes use `throttle:otp-request`, `throttle:otp-verify`, `throttle:tracking-create` |
| Redirect safety | High | `/go/{id}` accepts only numeric id, reads URL from DB, never accepts request URL | P8 redirect test confirms stored URL and outbound log |
| Click logging privacy | Medium | Logs raw IP for demo plus SHA-256 `ip_hash`; report can use hashed value | `outbound_clicks.ip_hash` length verified as 64 |
| Admin CSV export | Medium | Exports live under authenticated admin group | Routes wrapped in `adminAuth` |
| Public admin discovery | Low | Public layout has no admin link | Frontend smoke/visual checks cover public nav |
| Error leakage | Medium | Production should set `CI_ENVIRONMENT=production`; custom 404/500 pages exist | `.env.example` documents production settings |
| Alert dedupe | Medium | Alert events record previous/new crawl job IDs and skip repeated pairs | `dealsach:alerts` second run sends 0 |

## Highest-Risk Items And Fixes Applied

1. **Outbound redirect abuse:** fixed by validating numeric route id and using only `retailer_items.url` from the database.
2. **Duplicate alert spam:** fixed by storing `previous_crawl_job_id` and `new_crawl_job_id` on `alert_events` and checking before insert.
3. **Broken frontend dependency:** fixed stale Bootstrap SRI hash so CSS is not blocked in the browser.

## Verification Commands

```bash
docker exec dealsach-app php spark migrate:refresh
docker exec dealsach-app php spark db:seed DemoSeeder
docker exec dealsach-app php spark dealsach:crawl all
docker exec dealsach-app php spark dealsach:alerts
docker exec dealsach-app php spark dealsach:alerts
docker exec dealsach-app vendor/bin/phpunit --colors=never
npm run qa:visual
```
