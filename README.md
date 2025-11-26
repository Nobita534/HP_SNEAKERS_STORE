# 👟 HP Sneakers - Website Bán Giày Thể Thao

Website thương mại điện tử bán giày thể thao chính hãng được xây dựng bằng Laravel 11 và Tailwind CSS 4.

## 📋 Yêu cầu hệ thống

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js >= 18.x & NPM
- XAMPP/WAMP (hoặc PHP development server)

## 🚀 Hướng dẫn cài đặt nhanh

### Cách 1: Tự động (Windows)

```bash
# Chạy file INSTALL.bat
INSTALL.bat
```

### Cách 2: Thủ công

```bash
# 1. Cài đặt dependencies
composer install
npm install

# 2. Setup môi trường
copy .env.example .env
php artisan key:generate

# 3. Cấu hình database trong .env
# DB_DATABASE=sneakershop
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Tạo database và dữ liệu mẫu
php artisan migrate:fresh --seed

# 5. Chạy servers (2 terminals)
npm run dev
php artisan serve

# 6. Truy cập: http://127.0.0.1:8000
```

## 🎯 Tính năng

### ✅ Đã hoàn thành
- 🏠 Trang chủ responsive với hero banner
- 📦 Hiển thị danh mục sản phẩm (Nam, Nữ, Trẻ em, Sale)
- 👟 Grid sản phẩm mới nhất với phân trang
- ⭐ Sản phẩm nổi bật
- 🔐 Hệ thống đăng ký/đăng nhập
- 👁️ Toggle hiển thị mật khẩu
- 💬 Flash messages đẹp (4 loại)
- 📱 Responsive design
- 🎨 UI tone màu xanh đơn sắc

### 🚧 Đang phát triển
- 🛒 Giỏ hàng
- 💳 Thanh toán
- 📋 Quản lý đơn hàng
- ❤️ Danh sách yêu thích

## 📊 Dữ liệu mẫu

Sau khi chạy `php artisan migrate:fresh --seed`:

- **4 danh mục:** Nam, Nữ, Trẻ em, Sale
- **14 sản phẩm** từ các thương hiệu:
  - Nike (Air Max 270, Air Force 1, Air Max SC Kids)
  - Adidas (Ultraboost 22, Stan Smith, Superstar Kids)
  - Puma (RS-X, Cali Sport)
  - New Balance (574, 327)
  - Asics (Gel-Kayano 29)
  - Reebok (Classic Leather)
  - Vans (Old Skool)
  - Converse (Chuck Taylor)

## 🎨 Stack công nghệ

- **Backend:** Laravel 11
- **Frontend:** Blade Templates
- **CSS:** Tailwind CSS 4
- **JavaScript:** Vanilla JS
- **Icons:** Font Awesome 6 + SVG Icons
- **Database:** MySQL 8
- **Build Tool:** Vite

## 📁 Cấu trúc quan trọng

```
HPSNEAKERS-NEW/
├── app/Http/Controllers/
│   ├── AuthController.php      # Đăng nhập/ký
│   └── HomeController.php      # Trang chủ
├── app/Models/
│   ├── Category.php
│   ├── Product.php
│   └── User.php
├── database/
│   ├── migrations/
│   │   ├── 2025_11_26_135834_create_categories_table.php
│   │   └── 2025_11_26_135841_create_products_table.php
│   └── seeders/
│       ├── CategorySeeder.php
│       └── ProductSeeder.php
├── resources/views/
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── partials/
│   │       ├── header.blade.php
│   │       └── footer.blade.php
│   └── home.blade.php
└── routes/web.php
```

## 🔧 Lệnh hữu ích

```bash
# Routes
php artisan route:list

# Reset database
php artisan migrate:fresh --seed

# Clear cache
php artisan optimize:clear

# Build production
npm run build
```

## 🐛 Xử lý lỗi

### "Class not found"
```bash
composer dump-autoload
```

### "No application encryption key"
```bash
php artisan key:generate
```

### "Access denied for user"
- Kiểm tra `.env`: DB_USERNAME, DB_PASSWORD
- Đảm bảo MySQL đang chạy

### "Unknown database"
- Tạo database `sneakershop` trong phpMyAdmin
- Hoặc: `mysql -u root -e "CREATE DATABASE sneakershop"`

### Không thấy CSS
- Chạy: `npm run dev`
- Hoặc: `npm run build`

## 📞 Liên hệ

- **Email:** support@hpsneakers.com
- **Phone:** 1900 xxxx
- **Website:** http://127.0.0.1:8000

## 📄 License

Developed by HP Sneakers Team - 2025

---

### 🎉 Happy Coding!

Nếu gặp vấn đề, hãy kiểm tra file log: `storage/logs/laravel.log`
