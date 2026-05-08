# TÀI LIỆU KIẾN TRÚC VÀ KỸ THUẬT DỰ ÁN DEALSACH 📚

Tài liệu này cung cấp cái nhìn chi tiết, tường tận về toàn bộ kiến trúc phần mềm, cấu trúc dữ liệu, và quy trình nghiệp vụ của dự án **DealSach** – một hệ thống web so sánh giá sách trực tuyến dành riêng cho thị trường Việt Nam.

---

## TÓM TẮT KIẾN THỨC CỐT LÕI (EXECUTIVE SUMMARY)

Dành cho các thành viên cần nắm bắt nhanh cấu trúc dự án:
- **Bản chất:** Hệ thống cập nhật dữ liệu tĩnh (Batch Import) để so sánh giá sách từ 4 sàn (Fahasa, Phương Nam, Tiki, Shopee), không dùng bot scraping trực tiếp lúc runtime.
- **Công nghệ:** PHP 8.4, CodeIgniter 4 (MVC + Service Layer), MariaDB (19 bảng chuẩn hóa 3NF), Bootstrap 5, Docker. Thiết kế ưu tiên tốc độ và sự đơn giản, không dùng các framework SPA/Node.js cho frontend.
- **Điểm nhấn Kiến trúc:** 
  - Toàn bộ nghiệp vụ (tính toán, xử lý file, mail) nằm ở `app/Services/`. Controller chỉ điều phối.
  - Lịch sử biến động giá được lưu vết chuyên nghiệp qua hệ thống Snapshot và bảng Delta (`retailer_item_changes`).
  - Tích hợp hệ thống theo dõi và báo động giảm giá (Price Drop Alert) qua email với xác thực OTP bảo mật.

---

## 1. TỔNG QUAN DỰ ÁN (PROJECT OVERVIEW)

**DealSach** được xây dựng như một Proof of Concept (POC) thực hiện trong 2 tuần (đồ án Capstone nhóm 4 sinh viên). Mục tiêu của dự án là thu thập, đối chiếu và theo dõi biến động giá sách từ 4 nhà bán lẻ lớn: Fahasa, Nhà Sách Phương Nam, Tiki, và Shopee.

Dự án áp dụng chặt chẽ các nguyên tắc phát triển phần mềm hiệu quả: sử dụng kiến trúc MVC tiêu chuẩn, ưu tiên tốc độ, giới hạn phạm vi tính năng (Scope Lock) để tập trung hoàn thiện trọn vẹn luồng dữ liệu E2E (End-to-End) thay vì dàn trải tính năng. Hệ thống KHÔNG bao gồm module thanh toán, giỏ hàng, hay phân quyền đa luồng người dùng (Multi-role authentication).

---

## 2. CÔNG NGHỆ NỀN TẢNG (TECH STACK)

Các công nghệ được lựa chọn trong dự án đều hướng tới sự ổn định, nhẹ, và tiêu chuẩn hoá cao:

1. **PHP 8.4**: Ngôn ngữ backend chính. Sử dụng các tính năng mới nhất của PHP 8 như strict typing, readonly properties, constructor property promotion để đảm bảo mã nguồn an toàn và dễ bảo trì.
2. **CodeIgniter 4 (CI4)**: Web Framework PHP được sử dụng làm bộ khung kiến trúc chính. CI4 cung cấp routing, bảo mật (CSRF, XSS filtering), thư viện thao tác cơ sở dữ liệu (Query Builder) và công cụ dòng lệnh (`spark`).
3. **MySQL / MariaDB (utf8mb4)**: Hệ quản trị cơ sở dữ liệu quan hệ (RDBMS). Sử dụng chuẩn mã hoá utf8mb4 để hỗ trợ lưu trữ toàn vẹn văn bản tiếng Việt có dấu.
4. **Bootstrap 5**: CSS Framework xây dựng giao diện hiển thị (Frontend) theo hướng Server-rendered UI. Dự án sử dụng trực tiếp qua CDN, không phụ thuộc vào hệ sinh thái Node.js hay các build tools (Webpack/Vite) để giảm độ phức tạp hạ tầng.
5. **Docker & Docker Compose**: Hạ tầng container hoá dành cho môi trường Local Development. Giúp đóng gói toàn bộ Nginx, PHP-FPM, và MariaDB vào chung một môi trường nhất quán, loại bỏ triệt để lỗi xung đột hệ điều hành giữa các thành viên trong nhóm.

---

## 3. KIẾN TRÚC PHẦN MỀM (SOFTWARE ARCHITECTURE)

Dự án DealSach áp dụng kiến trúc **MVC (Model-View-Controller)** tiêu chuẩn của CodeIgniter 4, kết hợp với **Service Layer Pattern** để tách bạch logic nghiệp vụ.

*   **Thin Controllers (Tầng Điều Phối)**: Các Controller (ví dụ: `PublicController`, `BookController`) đóng vai trò tiếp nhận HTTP Request, xác thực đầu vào sơ bộ (`$this->validate()`), và gọi các Service tương ứng. Controller hoàn toàn không chứa các vòng lặp xử lý logic kinh doanh hay thao tác trực tiếp với Database.
*   **Service Layer (Tầng Nghiệp Vụ)**: Toàn bộ Business Logic được cô lập tại `app/Services/`. Tại đây, hệ thống tính toán giá trị so sánh, xác thực mã hash OTP, xử lý logic nạp dữ liệu JSON, và gửi email. Việc tách Service giúp code dễ dàng tái sử dụng và phục vụ tốt cho Unit Test.
*   **Models & Query Builder (Tầng Dữ Liệu)**: Các lớp Model trong `app/Models/` thao tác với Database. DealSach không áp dụng cấu trúc Domain-Driven Design (DDD) hay Repository Pattern quá cồng kềnh, mà sử dụng trực tiếp sức mạnh của CI4 Model và Query Builder để tối ưu hiệu năng các câu truy vấn phức tạp (JOIN nhiều bảng, tính giá nhỏ nhất).
*   **Views (Tầng Hiển Thị)**: Các file PHP sinh ra mã HTML thuần tuý dựa trên dữ liệu Controller trả về.

> **Diagnostic Protocol (Quy thức gỡ lỗi)**: Dự án vận hành với quy tắc nghiêm ngặt về truy xuất lỗi: *Xác minh (Verify) → Truy vết luồng dữ liệu (Trace Data Flow) → Sửa đổi (Build) → Xác nhận (Confirm)*. Tuyệt đối không tự ý thay đổi cấu trúc bảng cơ sở dữ liệu nếu vấn đề có thể được xử lý tại tầng Frontend hoặc Controller.

---

## 4. MÔ HÌNH CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)

Hệ thống sở hữu 19 bảng (Tables) được thiết kế theo chuẩn Normalization (Chuẩn hoá 3NF) nhằm loại bỏ triệt để hiện tượng dư thừa dữ liệu và đảm bảo tính nhất quán (Data Integrity).

**Nhóm 1: Quản Trị Hệ Thống (Admin & Auth)**
*   `admin_users`: Lưu trữ thông tin tài khoản quản trị viên duy nhất của hệ thống. Sử dụng thuật toán băm mật khẩu chuẩn của PHP (`password_hash`).
*   `admin_auth_logs`: Bảng lưu vết (Audit log) các phiên đăng nhập thành công/thất bại theo IP và User Agent để phục vụ bảo mật.

**Nhóm 2: Siêu Dữ Liệu Sách (Book Metadata)**
*   `books`: Lưu trữ thông tin định danh cốt lõi (Tựa sách, ISBN, format).
*   `publishers`, `categories`, `authors`: Các bảng chuẩn hoá danh mục độc lập (Nhà xuất bản, Thể loại, Tác giả).
*   `book_categories`, `book_authors`: Các bảng trung gian (Junction tables) giải quyết mối quan hệ Nhiều - Nhiều (Many-to-Many). Cho phép một cuốn sách thuộc nhiều thể loại và có nhiều tác giả đồng sáng tác.

**Nhóm 3: Dữ Liệu Thị Trường (Retailer Items)**
*   `retailers`: Bảng danh mục nhà bán (Fahasa, Tiki, Shopee, Nhasachphuongnam).
*   `retailer_items`: Điểm neo kết nối giữa 1 Cuốn sách và 1 Nhà bán cụ thể. Lưu trữ các thuộc tính định danh theo nguồn cào (Original ID, URL) và trạng thái hiện hành (Giá niêm yết, Giá sau giảm, Tình trạng tồn kho). Đây là bảng tham chiếu chính để xuất ra giao diện so sánh.

**Nhóm 4: Lịch Sử Và Biến Động Giá (History & Tracking)**
*   `crawl_jobs`: Lưu trữ thông tin tổng quan của mỗi đợt chạy tiến trình cào dữ liệu (Thời gian bắt đầu/kết thúc, số lượng xử lý).
*   `crawl_job_errors`: Bảng phân tích lỗi chi tiết ở mức độ từng dòng (Row-level error) nếu quá trình parse JSON gặp sự cố.
*   `price_snapshots`: Bản chụp (Snapshot) bất biến toàn bộ mức giá của tất cả sản phẩm tại thời điểm chạy xong một đợt Crawl. Đây là dữ liệu dùng để vẽ biểu đồ và phân tích đối chiếu sau này.
*   `retailer_item_changes`: Bảng Delta. Hệ thống tự động so sánh giá trị đợt Crawl hiện tại với đợt trước đó. Nếu có sự chênh lệch (tăng giá, giảm giá, hết hàng), hệ thống ghi lại trường thông tin thay đổi (field-level change) với Giá trị cũ (`old_value`) và Giá trị mới (`new_value`).

**Nhóm 5: Hệ Thống Cảnh Báo & Tương Tác (Alert & Tracking Workflows)**
*   `tracking_rules`: Cấu hình nhận thông báo (Email đăng ký, ID cuốn sách quan tâm, Mức giá kỳ vọng).
*   `otp_requests`: Quản lý tiến trình xác thực danh tính qua email (Lưu trữ hash OTP, trạng thái, thời gian hết hạn). Hạn chế spam thông qua các giới hạn attempt/resend.
*   `alert_events`: Hàng đợi (Queue) các sự kiện báo động. Khi giá thoả mãn điều kiện tracking, một bản ghi được tạo ra chờ tác vụ gửi email xử lý.
*   `email_logs`: Lưu toàn bộ lịch sử gửi thư (Cả OTP lẫn Alert) cùng mã thông điệp nhà cung cấp (`provider_message_id`) nhằm phục vụ truy xuất nguyên nhân nếu xảy ra lỗi rớt mail.
*   `outbound_clicks`: Bảng theo dõi tương tác, ghi nhận mỗi lượt người dùng click "Đến trang bán" chứa thông tin IP và Timestamp, phục vụ cho mô phỏng báo cáo Affiliate.

---

## 5. QUY TRÌNH NGHIỆP VỤ CỐT LÕI (CORE BUSINESS WORKFLOWS)

### 5.1. Luồng Thu Thập Dữ Liệu (Import-First Data Collection)
Hệ thống DealSach loại bỏ kỹ thuật Live Web Scraping do tính không ổn định, thay vào đó áp dụng cơ chế Batch Import Processing:
1. **Khởi tạo Job**: Admin kích hoạt tiến trình thông qua Command Line (CLI).
2. **Đọc tệp tin**: Hệ thống parse cấu trúc JSON giả lập đặt tại thư mục `writable/import/`.
3. **So khớp & Lưu trữ**: Hệ thống tra cứu theo `original_id`. Nếu sản phẩm đã tồn tại, tiến hành ghi bản ghi mới vào `price_snapshots`. 
4. **Đối soát biến động**: So sánh `effective_price` (giá cuối cùng) mới với dữ liệu hiện hành trong `retailer_items`. Các sai lệch sẽ kích hoạt lệnh chèn vào `retailer_item_changes`.
5. **Cập nhật trạng thái**: Override các cột giá hiện hành trong `retailer_items` và hoàn tất `crawl_jobs`.

### 5.2. Luồng Truy Vấn Danh Mục & Chi Tiết Sách
1. **Truy vấn danh mục**: Khi người dùng vào trang Catalog, Query Builder của hệ thống thực thi phép `LEFT JOIN` bảng `books` với `retailer_items` nhằm tính toán ra `lowest_available_price` (chỉ tính những item có `in_stock = 1`). Kết quả được phân trang (Pagination) thông qua CI4 Pager.
2. **Truy vấn chi tiết**: Tại trang chi tiết của 1 cuốn sách cụ thể, hệ thống truy xuất thông tin từ bảng `books`, kèm mảng (array) các object `retailer_items` được map tương ứng để xây dựng bảng đối chiếu 4 nhà cung cấp.

### 5.3. Luồng Theo Dõi & Cảnh Báo Giảm Giá (Price Drop Alert)
1. **Đăng ký theo dõi**: Người dùng thiết lập `target_price` cho một cuốn sách. Hệ thống chèn bản ghi vào `otp_requests` sinh mã ngẫu nhiên và gửi tới Email.
2. **Kích hoạt Tracking**: Người dùng nhập OTP. Hệ thống đối chiếu `otp_hash`, nếu hợp lệ, trạng thái tại `tracking_rules` được kích hoạt (`is_active = true`).
3. **Quét định kỳ (Cron Job)**: Tiến trình tự động rà soát sau mỗi đợt Crawl Data. Thực hiện truy vấn kiểm tra các bản ghi trong `tracking_rules` có `target_price >= current_effective_price`. 
4. **Xử lý sự kiện**: Các cặp ghép thoả điều kiện sinh ra bản ghi `alert_events` (Trạng thái `queued`). Trình gửi email sẽ quét hàng đợi này, phát hành thông báo bằng thư điện tử, cập nhật `alert_events` sang `sent`, và lưu bản lưu vào `email_logs`.

---

## 6. CẤU TRÚC THƯ MỤC VÀ TỆP TIN HỆ THỐNG (PROJECT DIRECTORY STRUCTURE)

Hệ thống tệp tin tuân thủ cấu trúc của CodeIgniter 4, được tổ chức phục vụ mục đích an toàn bảo mật và khả năng phân tách vai trò:

### Cấu Trúc Thư Mục Lõi (Core Directories)
*   **`app/`**: Không gian làm việc chính, chứa toàn bộ kiến trúc MVC và config hệ thống. Cấm truy cập trực tiếp từ trình duyệt bằng mọi hình thức.
    *   `Controllers/`: Nơi định nghĩa các endpoint và nhận/trả HTTP Request.
    *   `Models/`: Các file kế thừa từ `CodeIgniter\Model`, định nghĩa table name, primary key, allowed fields.
    *   `Views/`: Cấu trúc file giao diện PHP tĩnh kết hợp biến động, mở rộng thông qua thư mục con (`layouts`, `public`, `admin`).
    *   `Services/`: Lớp cô lập Business Logic độc lập (ví dụ `ComparisonService`).
    *   `Filters/`: Cấu hình Middleware quản trị chặn bắt các Route yêu cầu xác thực (`AdminAuthFilter`).
    *   `Database/`: Chứa `Migrations/` (Mã PHP tạo cấu trúc 19 bảng) và `Seeds/` (Kịch bản khởi tạo dữ liệu mẫu hàng loạt phục vụ test).
    *   `Config/`: Tập hợp các tệp thiết lập hằng số và quy tắc ứng dụng (như `Routes.php`).
*   **`public/`**: Thư mục Front Controller (Web Root). Nơi duy nhất mà Web Server (Nginx) định tuyến tới. File `index.php` tại đây là Entry Point bắt đầu chu kỳ sống của mọi Request. Các tài sản hiển thị (CSS, JS, Images) bắt buộc đặt ở đây.
*   **`writable/`**: Không gian I/O (Input/Output). Cung cấp quyền cho server PHP ghi dữ liệu cục bộ. Chứa thư mục `import/` (File nguồn JSON), `logs/` (File theo vết lỗi CI4 Error Logging), `session/`, và `cache/`.
*   **`vendor/`**: Nơi lưu trữ mã nguồn của chính Framework CI4 cùng các Dependency bên thứ ba. Quản lý nghiêm ngặt qua Composer, tuyệt đối không được chỉnh sửa mã bằng tay.
*   **`tests/`**: Chứa các kịch bản kiểm thử (Test Cases) do PHPUnit vận hành để nghiệm thu chất lượng phần mềm.
*   **`docker/`**: Thư mục lưu cấu hình Container hoá (File cấu hình vhost cho Nginx, cấu hình tuỳ chỉnh cho PHP-FPM).

### Các Tệp Tin Cốt Lõi Tại Gốc Ứng Dụng (Root Level Files)
*   **`.env` / `.env.example`**: Tệp tin định nghĩa Biến Môi Trường (Environment Variables). Chứa các tham số cấu hình tĩnh mang tính chất bảo mật cao (DB Host, DB Pass, SMTP Secret).
*   **`spark`**: Tệp thực thi CLI chính thức của CodeIgniter 4. Đây là công cụ dòng lệnh tiếp nhận thao tác khởi tạo DB, chạy Migration, Server dev cục bộ.
*   **`composer.json` / `composer.lock`**: Tập tin siêu dữ liệu (Metadata) quản lý thư viện dự án bằng PHP Composer. Định nghĩa chính xác tên và phiên bản của các package phụ thuộc để đảm bảo tính đồng nhất trên mọi máy trạm.
*   **`docker-compose.yml`**: Kịch bản triển khai hạ tầng. Cấu hình mạng lưới gồm 3 container độc lập (Web, App, Database) và các ánh xạ volume liên kết.
*   **`database.dbml`**: Tệp đặc tả thiết kế cơ sở dữ liệu (Database Markup Language). Là đầu ra tự động từ các file migration, có thể kết nối với DB Diagram tool để kết xuất lưu đồ ERD.
*   **`PROJECT_EXPLANATION.md`**: Tài liệu kỹ thuật bao quát toàn bộ đặc tả kiến trúc hiện hành của dự án (Tài liệu bạn đang đọc).
*   **`phpunit.xml.dist`**: Hồ sơ cấu hình môi trường chạy kiểm thử phần mềm cho thư viện PHPUnit.
*   **`preload.php`**: Kịch bản nạp trước bộ nhớ (Opcache Preloading) hỗ trợ bởi PHP 7.4+ giúp tăng đáng kể hiệu suất khởi động ứng dụng.

---

DealSach đại diện cho một mô hình phần mềm tập trung mạnh vào chất lượng dữ liệu và sự chuẩn hoá kỹ thuật. Từ thiết kế cơ sở dữ liệu 3NF, kiến trúc Service Layer cô lập, đến cách tiếp cận Batch-Import, tất cả đều tạo nên một hệ thống bền bỉ, dễ mở rộng và đáp ứng tốt các yêu cầu nghiệp vụ chuyên sâu của quy trình so sánh giá.
