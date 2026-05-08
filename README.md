# 👟 HP Sneakers - Website Bán Giày Thể Thao

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

## 🎨 Stack công nghệ

- **Backend:** Laravel 11
- **Frontend:** Blade Templates
- **CSS:** Tailwind CSS 4
- **JavaScript:** Vanilla JS
- **Icons:** Font Awesome 6 + SVG Icons
- **Database:** MySQL 8
- **Build Tool:** Vite

