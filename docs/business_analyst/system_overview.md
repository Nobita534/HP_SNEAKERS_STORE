# System Overview

## 1. Thông tin tài liệu

| Thuộc tính | Giá trị |
| --- | --- |
| Tên tài liệu | System Overview |
| Hệ thống | HP Sneakers Store |
| Phiên bản | 1.0 |
| Ngày cập nhật | 2026-07-23 |

## 2. Giới thiệu hệ thống

HP Sneakers Store là hệ thống thương mại điện tử được phát triển để hỗ trợ bán giày trực tuyến, đồng thời cung cấp nền tảng quản lý tập trung cho cửa hàng. Hệ thống cho phép khách hàng tìm kiếm sản phẩm, xem chi tiết, quản lý giỏ hàng, đặt hàng và thanh toán trực tuyến qua VNPay. Ở phía quản trị, hệ thống hỗ trợ quản lý danh mục, sản phẩm, tồn kho, đơn hàng và thông tin người dùng nhằm nâng cao hiệu quả vận hành và giảm thao tác thủ công.

## 3. Mục tiêu tài liệu

Tài liệu mô tả tổng quan hệ thống và thống nhất phạm vi yêu cầu chức năng trước khi triển khai các tài liệu phân tích chi tiết như Functional Requirements, Use Case Diagram và Activity Diagram.

## 4. Đối tượng sử dụng

| Đối tượng | Vai trò |
| --- | --- |
| Guest | Xem, tìm kiếm và tham khảo thông tin sản phẩm mà không cần đăng nhập. |
| Customer | Đăng ký tài khoản, đăng nhập, quản lý thông tin cá nhân, mua hàng, thanh toán và theo dõi đơn hàng. |
| Administrator | Quản lý sản phẩm, danh mục, đơn hàng, tồn kho và tài khoản người dùng. |
| VNPay | Hệ thống thanh toán bên thứ ba, tiếp nhận và xử lý giao dịch trực tuyến. |

## 5. Phạm vi dự án

### 5.1 Trong phạm vi

- Quản lý tài khoản người dùng (Authentication)
- Quản lý danh mục và sản phẩm (Product Management)
- Hiển thị và tra cứu sản phẩm (Product Catalog)
- Quản lý giỏ hàng (Shopping Cart)
- Quy trình đặt hàng (Checkout)
- Thanh toán trực tuyến qua VNPay (Payment)
- Quản lý đơn hàng (Order Management)
- Quản lý tồn kho (Inventory Management)
- Quản lý người dùng (User Management)

### 5.2 Ngoài phạm vi

- Ứng dụng di động (Mobile Application)
- Mô hình sàn thương mại điện tử nhiều nhà bán (Multi-vendor Marketplace)
- Chương trình khách hàng thân thiết (Loyalty Program)
- Hệ thống mã giảm giá và khuyến mãi (Coupon and Promotion)
- Hệ thống gợi ý sản phẩm (Recommendation System)
- Hỗ trợ đa ngôn ngữ (Multi-language Support)

