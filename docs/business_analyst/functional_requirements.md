# Functional Requirements

## 1. Thông tin tài liệu

| Thuộc tính | Giá trị |
| --- | --- |
| Tên tài liệu | Functional Requirements |
| Hệ thống | HP Sneakers Store |
| Phiên bản | 1.0 |
| Ngày cập nhật | 2026-07-23 |

## 2. Mục tiêu

Tài liệu này mô tả các yêu cầu chức năng cốt lõi của hệ thống HP Sneakers Store, làm cơ sở cho thiết kế, triển khai và kiểm thử.

## 3. Danh sách yêu cầu chức năng

### 3.1 Authentication

| ID | Requirement | Priority |
| --- | --- | --- |
| FR-01 | Hệ thống cho phép người dùng đăng ký tài khoản bằng email và mật khẩu. | High |
| FR-02 | Hệ thống xác thực thông tin đăng nhập trước khi cấp quyền truy cập. | High |
| FR-03 | Hệ thống cho phép người dùng đăng xuất khỏi hệ thống. | Medium |
| FR-04 | Hệ thống chỉ cho phép người dùng đã đăng nhập truy cập các chức năng quản lý tài khoản và đặt hàng. | High |

### 3.2 Product Catalog

| ID | Requirement | Priority |
| --- | --- | --- |
| FR-05 | Hệ thống hiển thị danh sách các sản phẩm đang kinh doanh. | High |
| FR-06 | Hệ thống cho phép người dùng xem thông tin chi tiết của từng sản phẩm. | High |
| FR-07 | Hệ thống cho phép người dùng tìm kiếm sản phẩm theo từ khóa. | High |
| FR-08 | Hệ thống hỗ trợ gợi ý từ khóa trong quá trình tìm kiếm sản phẩm. | Medium |
| FR-09 | Hệ thống cho phép lọc sản phẩm theo thương hiệu. | Medium |
| FR-10 | Hệ thống cho phép lọc sản phẩm theo giới tính hoặc nhóm đối tượng sử dụng. | Medium |

### 3.3 Shopping Cart

| ID | Requirement | Priority |
| --- | --- | --- |
| FR-11 | Hệ thống cho phép người dùng thêm sản phẩm vào giỏ hàng. | High |
| FR-12 | Hệ thống cho phép người dùng cập nhật số lượng sản phẩm trong giỏ hàng. | High |
| FR-13 | Hệ thống cho phép người dùng xóa từng sản phẩm khỏi giỏ hàng. | High |
| FR-14 | Hệ thống cho phép người dùng xóa toàn bộ sản phẩm trong giỏ hàng. | Medium |
| FR-15 | Hệ thống tự động tính tổng giá trị đơn hàng dựa trên nội dung giỏ hàng. | High |

### 3.4 Payment (VNPay)

| ID | Requirement | Priority |
| --- | --- | --- |
| FR-16 | Hệ thống cho phép người dùng thanh toán đơn hàng thông qua cổng thanh toán VNPay. | High |
| FR-17 | Hệ thống chuyển hướng người dùng đến cổng thanh toán VNPay sau khi xác nhận thanh toán. | High |
| FR-18 | Hệ thống tiếp nhận kết quả giao dịch trả về từ VNPay. | High |
| FR-19 | Hệ thống cập nhật trạng thái đơn hàng theo kết quả thanh toán thành công hoặc thất bại. | High |

### 3.5 Order Management

| ID | Requirement | Priority |
| --- | --- | --- |
| FR-20 | Hệ thống cho phép người dùng xem danh sách các đơn hàng đã đặt. | Medium |
| FR-21 | Hệ thống cho phép người dùng xem chi tiết từng đơn hàng. | Medium |
| FR-22 | Hệ thống cho phép người dùng hủy đơn hàng khi đơn hàng chưa được xử lý. | Medium |
| FR-23 | Quản trị viên có thể xem danh sách đơn hàng của toàn hệ thống. | High |
| FR-24 | Quản trị viên có thể cập nhật trạng thái xử lý đơn hàng. | High |

### 3.6 Inventory Management

| ID | Requirement | Priority |
| --- | --- | --- |
| FR-25 | Hệ thống quản lý số lượng tồn kho của từng sản phẩm. | High |
| FR-26 | Quản trị viên có thể tạo phiếu nhập kho để cập nhật số lượng tồn. | High |
| FR-27 | Quản trị viên có thể xem lịch sử các phiếu nhập kho. | Medium |
| FR-28 | Hệ thống tự động cập nhật số lượng tồn kho sau khi nhập hàng. | High |

### 3.7 Administration

| ID | Requirement | Priority |
| --- | --- | --- |
| FR-29 | Quản trị viên có thể thêm mới sản phẩm. | High |
| FR-30 | Quản trị viên có thể chỉnh sửa thông tin sản phẩm. | High |
| FR-31 | Quản trị viên có thể xóa sản phẩm khỏi hệ thống. | Medium |
| FR-32 | Quản trị viên có thể quản lý thông tin người dùng. | High |
| FR-33 | Hệ thống cung cấp trang Dashboard dành cho quản trị viên. | Medium |

