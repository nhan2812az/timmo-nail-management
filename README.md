# Timmo Nail Management System

Hệ thống quản lý tiệm nail được xây dựng bằng PHP và MySQL, hỗ trợ quản lý lịch hẹn, khách hàng, nhân viên, dịch vụ, thanh toán và doanh thu.

## Tính năng hiện có

### Đăng nhập & Phân quyền

* Đăng nhập hệ thống
* Đăng xuất
* Quản lý tài khoản người dùng
* Phân quyền:

  * Admin
  * Reception (Lễ tân)
  * Staff (Nhân viên)

### Quản lý lịch hẹn

* Tạo lịch hẹn
* Sửa lịch hẹn
* Xóa lịch hẹn
* Hủy lịch hẹn
* Xác nhận lịch hẹn
* Hoàn thành lịch hẹn
* Tìm kiếm và lọc lịch hẹn
* Kiểm tra trùng lịch cơ bản

### Quản lý khách hàng

* Thêm khách hàng
* Sửa khách hàng
* Xóa khách hàng
* Tìm kiếm khách hàng
* Xem lịch sử đặt lịch
* Thống kê chi tiêu khách hàng

### Quản lý dịch vụ

* Thêm dịch vụ
* Sửa dịch vụ
* Xóa dịch vụ
* Bật / tắt dịch vụ
* Quản lý giá dịch vụ
* Quản lý thời lượng dịch vụ

### Quản lý nhân viên

* Thêm nhân viên
* Sửa nhân viên
* Xóa nhân viên
* Bật / tắt trạng thái làm việc

### Thanh toán

* Thanh toán lịch hẹn
* Tiền mặt
* Chuyển khoản
* Thẻ
* Theo dõi trạng thái thanh toán

### Dashboard

* Tổng khách hàng
* Tổng lịch hẹn
* Tổng dịch vụ
* Tổng nhân viên
* Doanh thu hôm nay
* Doanh thu tháng
* Lịch hẹn hôm nay
* Khách hàng mới trong tháng

## Công nghệ sử dụng

* PHP 8+
* MySQL
* HTML5
* CSS3
* XAMPP
* Git
* GitHub

## Cấu trúc thư mục

```text
myproject/
│
├── actions/
├── assets/
├── config/
├── includes/
├── pages/
│
├── index.php
├── login.php
├── logout.php
├── database.sql
└── README.md
```

## Cài đặt

### 1. Clone source

```bash
git clone https://github.com/nhan2812az/timmo-nail-management.git
```

### 2. Tạo database

Tạo database:

```sql
CREATE DATABASE timmo_db;
```

### 3. Import database

Import file:

```text
database.sql
```

vào MySQL.

### 4. Cấu hình kết nối

Mở:

```text
config/database.php
```

Cập nhật thông tin MySQL nếu cần.

### 5. Chạy dự án

Khởi động:

* Apache
* MySQL

Truy cập:

```text
http://localhost/myproject
```

## Tài khoản mặc định

```text
Email: admin@timmo.local
Password: 123456
```

## Phiên bản

### v1.0

* Authentication
* Authorization
* Appointment Management
* Customer Management
* Staff Management
* Service Management
* Payment Management
* Revenue Dashboard
* User Management

## Kế hoạch phát triển

### Giai đoạn 2

* Calendar View - đã hoàn thành
* Chống trùng lịch theo thời lượng dịch vụ - đã hoàn thành
* Báo cáo doanh thu - đã hoàn thành
* Dashboard biểu đồ -  đã hoàn thành
* Upload ảnh mẫu nail - đang thực hiện
* Email/SMS nhắc lịch 
* Hồ sơ khách hàng nâng cao

## Tác giả

Thanh Nhân Ngô Đình

GitHub:
https://github.com/nhan2812az
