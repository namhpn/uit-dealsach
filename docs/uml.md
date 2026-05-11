# DealSach UML Package

These diagrams match the implemented CodeIgniter 4 application, routes, services, commands, and 19-table schema.

## 1. Use Case Diagram

```mermaid
flowchart LR
    Guest["Guest"]
    Admin["Admin"]
    Cron["Cron/Scheduler"]
    SMTP["SMTP/Email Log"]
    Source["Retailer JSON Source"]

    Guest --> UC1["Search/filter catalog"]
    Guest --> UC2["View book comparison"]
    Guest --> UC3["Request/verify OTP"]
    Guest --> UC4["Create tracking rule"]
    Guest --> UC5["Click retailer redirect"]

    Admin --> UC6["Login/logout"]
    Admin --> UC7["View dashboard"]
    Admin --> UC8["Manage Book CRUD"]
    Admin --> UC9["Export CSV"]

    Cron --> UC10["Import retailer data"]
    Cron --> UC11["Send price-drop alerts"]
    Source --> UC10
    UC3 --> SMTP
    UC11 --> SMTP
```

## 2. Activity Diagram

```mermaid
flowchart TD
    A["Open homepage"] --> B["Search or browse catalog"]
    B --> C["Apply keyword, retailer, category, stock filters"]
    C --> D["Open book detail"]
    D --> E["Compare in-stock retailer offers"]
    E --> F{"Want tracking?"}
    F -- No --> G["Click retailer via /go/{id}"]
    F -- Yes --> H["Submit email and target price"]
    H --> I["Request OTP"]
    I --> J["Verify OTP"]
    J --> K["Create tracking rule"]
    K --> L["Alert command checks future price drops"]
```

## 3. Sequence Diagram

```mermaid
sequenceDiagram
    participant Cron
    participant CrawlCommand
    participant ImportService
    participant DB
    participant AlertCommand
    participant AlertService
    participant EmailLog

    Cron->>CrawlCommand: php spark dealsach:crawl all
    CrawlCommand->>ImportService: importRetailer(slug)
    ImportService->>DB: insert crawl_jobs, retailer_items, snapshots, changes
    Cron->>AlertCommand: php spark dealsach:alerts
    AlertCommand->>AlertService: sendDailyAlerts()
    AlertService->>DB: load latest two full snapshot batches
    AlertService->>DB: load active tracking_rules
    AlertService->>DB: insert alert_events if price dropped and not duplicate
    AlertService->>EmailLog: insert simulated alert email
```

## 4. Class Diagram

```mermaid
classDiagram
    class HomeController
    class BookController
    class TrackingController
    class RedirectController
    class AuthController
    class DashboardController
    class BookCrudController
    class ExportController
    class DealSachCrawlCommand
    class DealSachAlertsCommand
    class CatalogService
    class ImportService
    class OtpService
    class TrackingService
    class AlertService
    class BookModel
    class RetailerItemModel

    HomeController --> CatalogService
    BookController --> CatalogService
    TrackingController --> OtpService
    TrackingController --> TrackingService
    RedirectController --> RetailerItemModel
    AuthController --> AdminAuthFilter
    DashboardController --> AlertService
    BookCrudController --> BookModel
    ExportController --> BookModel
    DealSachCrawlCommand --> ImportService
    DealSachAlertsCommand --> AlertService
    TrackingService --> OtpService
```

## 5. ERD

```mermaid
erDiagram
    admin_users ||--o{ admin_auth_logs : logs
    publishers ||--o{ books : publishes
    books ||--o{ book_categories : has
    categories ||--o{ book_categories : classifies
    categories ||--o{ categories : parent
    books ||--o{ book_authors : has
    authors ||--o{ book_authors : writes
    retailers ||--o{ retailer_items : lists
    books ||--o{ retailer_items : compared_as
    retailer_items ||--o{ price_snapshots : snapshots
    crawl_jobs ||--o{ price_snapshots : captures
    retailer_items ||--o{ retailer_item_changes : changes
    crawl_jobs ||--o{ retailer_item_changes : detects
    crawl_jobs ||--o{ crawl_job_errors : reports
    books ||--o{ tracking_rules : tracks
    tracking_rules ||--o{ alert_events : emits
    books ||--o{ otp_requests : verifies
    retailer_items ||--o{ outbound_clicks : clicked
    books ||--o{ outbound_clicks : clicked_book
```

## 6. Component Diagram

```mermaid
flowchart TB
    Browser["Browser"]
    Nginx["Nginx container"]
    App["PHP-FPM / CodeIgniter app"]
    DB["MariaDB"]
    Import["Import JSON files"]
    SMTP["SMTP or email_logs fallback"]

    Browser --> Nginx --> App
    App --> DB
    App --> SMTP
    Import --> App

    subgraph AppModules["CodeIgniter modules"]
        Public["Public catalog/detail/tracking/redirect"]
        Admin["Admin auth/dashboard/Book CRUD/export"]
        CLI["CLI crawl and alerts"]
        Services["Catalog, Import, OTP, Tracking, Alert services"]
    end

    App --> AppModules
```

## 7. Deployment Diagram

```mermaid
flowchart LR
    subgraph LocalDocker["Local Docker"]
        Host["localhost:8080"]
        Nginx["dealsach-nginx"]
        PHP["dealsach-app PHP-FPM"]
        MariaDB["dealsach-db MariaDB 11"]
        PMA["phpMyAdmin localhost:8081"]
        Host --> Nginx --> PHP --> MariaDB
        PMA --> MariaDB
    end

    subgraph SharedHosting["Shared Hosting Option"]
        PublicHtml["public_html/ contents of public/"]
        AppDir["dealsach-app/ application files"]
        MySQL["Hosted MySQL/MariaDB"]
        Cron["Hosting cron jobs"]
        PublicHtml --> AppDir --> MySQL
        Cron --> AppDir
    end
```
