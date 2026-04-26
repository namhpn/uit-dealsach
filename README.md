# DealSach

Vietnamese-first book price comparison website built with PHP 8.4, CodeIgniter 4, MariaDB, Bootstrap 5 and Docker.

## 1. Requirements

- Docker
- Docker Compose v2
- Git

Manual fallback requirements:

- PHP 8.4+
- Composer
- Nginx or Apache
- MySQL/MariaDB
- Required PHP extensions:
  - intl
  - mbstring
  - mysqli
  - pdo_mysql
  - curl
  - openssl
  - fileinfo
  - dom
  - simplexml
  - zip
  - gd

## 2. Clone project

```bash
git clone <repo-url> dealsach
cd dealsach
````

## 3. Create environment file

```bash
cp .env.example .env
```

## 4. Start Docker stack

```bash
docker compose up -d --build
```

## 5. Install PHP dependencies

```bash
docker exec -it dealsach-app composer install
```

## 6. Check PHP and CodeIgniter

```bash
docker exec -it dealsach-app php -v
docker exec -it dealsach-app php spark
```

## 7. Run migrations

```bash
docker exec -it dealsach-app php spark migrate
```

## 8. Seed demo data

```bash
docker exec -it dealsach-app php spark db:seed DemoSeeder
```

Seed data should include:

* Admin account
* Retailers:

  * Fahasa
  * Nhasachphuongnam
  * Tiki
  * Shopee
* Sample canonical books
* Sample retailer items
* Sample price snapshots

## 9. Access application

App:

```text
http://localhost:8080
```

phpMyAdmin:

```text
http://localhost:8081
```

Admin login:

```text
http://localhost:8080/ds-admin/login
```

## 10. Useful Docker commands

Stop stack:

```bash
docker compose down
```

Stop stack and remove database volume:

```bash
docker compose down -v
```

View logs:

```bash
docker logs dealsach-app
docker logs dealsach-nginx
docker logs dealsach-db
```

Open PHP container shell:

```bash
docker exec -it dealsach-app bash
```

Run tests:

```bash
docker exec -it dealsach-app vendor/bin/phpunit
```

## 11. Scheduled jobs in Docker

CLI commands:

```bash
docker exec -it dealsach-app php spark dealsach:crawl fahasa
docker exec -it dealsach-app php spark dealsach:crawl phuongnam
docker exec -it dealsach-app php spark dealsach:crawl tiki
docker exec -it dealsach-app php spark dealsach:crawl shopee
docker exec -it dealsach-app php spark dealsach:crawl all
docker exec -it dealsach-app php spark dealsach:alerts
```

Host crontab examples:

```cron
0 2 * * * docker exec dealsach-app php spark dealsach:crawl all
0 8 * * * docker exec dealsach-app php spark dealsach:alerts
```


## 12. Core public features

* Vietnamese-first homepage
* Book catalog
* Search by title
* Filter by retailer
* Filter by availability
* Pagination
* Book detail page
* Multi-retailer price comparison
* Lowest available effective price highlight
* OTP tracking creation
* Outbound redirect logging

## 13. Core admin features

* Hidden admin login
* Dashboard metrics
* Full Book CRUD
* CSV book export
* CSV activity export
* Recent crawl status
* Recent admin authentication logs

## 14. AJAX features

Use AJAX for:

* Send OTP
* Verify OTP
* Duplicate tracking-rule check
* Admin book live search or delete confirmation

## 15. Shared-hosting deployment later

Shared hosting should use the two-directory CodeIgniter layout.

Application files:

```text
/home/USER/dealsach-app/
```

Public web root:

```text
/home/USER/public_html/
```

Only contents of `public/` should be copied into `public_html/`.

Production `.env` should use:

```dotenv
CI_ENVIRONMENT=production
app.forceGlobalSecureRequests=true
cookie.secure=true
cookie.httponly=true
cookie.samesite=Lax
```

Before packaging for shared hosting:

```bash
composer install --no-dev --optimize-autoloader
```