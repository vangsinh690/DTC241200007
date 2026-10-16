# HỆ THỐNG QUẢN LÝ SINH VIÊN

* **Sinh viên thực hiện:** Vàng Thị Sinh
* **Mã số sinh viên:** DTC241200007
* **Môn học:** Triển khai và quản trị hệ thống phần mềm
* **Đề tài:** Hệ thống Quản lý Sinh viên (Đề 2)

---

## 1. Yêu cầu môi trường hệ thống
* **Hệ điều hành:** Linux / Ubuntu
* **Công cụ bắt buộc:** Docker & Docker Compose đã được cài đặt sẵn trên máy.

---

## 2. Danh sách các Dịch vụ & Port truy cập

| Tên Dịch Vụ | Cổng (Port) | Mô Tả Chức Năng |
| :--- | :--- | :--- |
| **Web App (Nginx Reverse Proxy)** | http://localhost:80 | Giao diện chính Hệ thống Quản lý Sinh viên |
| **phpMyAdmin** | http://localhost:8080 | Công cụ quản trị Cơ sở dữ liệu MySQL |
| **Grafana** | http://localhost:3000 | Giao diện giám sát Metrics và Quản lý Log/Alert |
| **Prometheus** | http://localhost:9090 | Hệ thống thu thập các chỉ số (Metrics) container |
| **Loki & Promtail** | http://localhost:3100 | Hệ thống thu thập và lưu trữ Log tập trung |

---

## 3. Hướng dẫn khởi chạy hệ thống (Step-by-Step)

### Bước 1: Clone repository về máy
Mở Terminal và chạy lệnh sau để tải mã nguồn:
git clone https://github.com/vangsinh690/DTC241200007.git
cd DTC241200007

### Bước 2: Khởi chạy toàn bộ hệ thống bằng Docker Compose
Chạy lệnh dựng và khởi động các container ở chế độ nền:
docker compose up -d 

### Bước 3: Kiểm tra trạng thái các container
Đảm bảo tất cả các dịch vụ (Web, MySQL, Prometheus, Grafana, Loki,...) đang chạy ổn định:
docker compose ps

---

## 4. Hướng dẫn truy vấn Log và Giám sát (LogQL & Alerting)

Truy cập vào Grafana tại http://localhost:3000, vào mục Explore, chọn Data Source là Loki và sử dụng các câu lệnh LogQL sau:

* **Truy vấn xem log lỗi 404 (Request không tồn tại):**
  {container="web_app"} |= "404"

* **Chuyển đổi log sang dạng số để làm biểu đồ và Alert:**
  count_over_time({container="web_app"} |= "404" [1m])

* **Quy tắc cảnh báo (Alert Rule):** Đã thiết lập rule Canh-bao-loi-404-tan-cong với ngưỡng Is above 2 (kích hoạt trạng thái Firing khi phát hiện hành vi quét lỗi liên tục vượt quá 2 lần/phút).

---

## 5. Kịch bản mô phỏng sự cố An toàn thông tin (Security Simulation)

Thực hiện các câu lệnh curl dưới đây trên terminal để cố tình tạo lưu lượng lỗi 404 (mô phỏng tấn công dò đường / Directory Scanning), kích hoạt hệ thống cảnh báo tự động:

curl http://localhost/admin
curl http://localhost/wp-login.php
curl http://localhost/test-attack

---

## 6. Các biện pháp Hardening bảo mật hệ thống đã áp dụng

1. **Network Isolation (Cô lập mạng Docker):** Phân tách rõ ràng các mạng nội bộ giữa Web App, Database và hệ thống giám sát để ngăn ngừa truy cập trái phép chéo giữa các container.
2. **Linux Capabilities:** Lược bỏ bớt các đặc quyền hệ thống Linux nguy hiểm không cần thiết cho container (`cap_drop: - ALL` / chỉ giữ lại các quyền tối thiểu cần thiết).
3. **Security Headers cho Nginx:** Bổ sung các tiêu chuẩn bảo mật HTTP Headers (`X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`) tại tầng Reverse Proxy để chống các cuộc tấn công Clickjacking và XSS.
4. **Sử dụng Mật khẩu mạnh:** Cấu hình mật khẩu quản trị và truy cập cơ sở dữ liệu MySQL/phpMyAdmin/Grafana bằng chuỗi ký tự phức tạp, độ dài cao, tránh các mật khẩu mặc định rủi ro.
