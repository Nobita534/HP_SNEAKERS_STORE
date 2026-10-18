# Tìm kiếm sản phẩm

## Phạm vi

- Admin `/admin/products`: chọn SKU hoặc ID, so sánh chính xác bằng `=`. SKU là mã của sản phẩm (không phải mã riêng theo size), tối đa 100 ký tự, có unique B-tree index trên PostgreSQL. ID dùng primary key hiện có. SKU phân biệt hoa/thường trên PostgreSQL.
- Sản phẩm cũ giữ `sku = NULL`; không tự sinh mã. Khi nhập CSV sau này, lưu mã thực tế vào `products.sku`. Form tạo/sửa và logic tồn kho hiện tại không thay đổi.
- Khách hàng `/tim-kiem` và `/api/search-suggestions`: tìm tên/thương hiệu, chỉ lấy `is_active = true`. Không tìm trong mô tả.
- PostgreSQL dùng `ILIKE` và toán tử `pg_trgm` `<%`, cùng hai GIN index `gin_trgm_ops`. Tìm gần đúng áp dụng từ 3 ký tự chữ/số trở lên (cho phép khoảng trắng, dấu gạch ngang); dấu đặc biệt được tìm nguyên văn. Ngưỡng gần đúng dùng `pg_trgm.word_similarity_threshold` của database (mặc định 0.6).
- Mặc định ưu tiên kết quả chứa từ khóa, rồi xếp theo `word_similarity`, cuối cùng theo ID để phân trang ổn định. Vẫn hỗ trợ sắp xếp giá, tên, mới nhất.
- Autocomplete nhận từ 2 ký tự, tối đa 8 sản phẩm và 3 thương hiệu. Phản hồi cũ không ghi đè gợi ý của từ khóa mới.
- Chưa có lịch sử tìm kiếm, tìm không dấu hay xử lý đồng nghĩa.

## Cài đặt

Ứng dụng dùng kết nối `pgsql` trong `.env` và PHP có `pdo_pgsql`. Chạy:

```sh
php artisan migrate --path=database/migrations/2026_10_17_000001_add_product_search_indexes.php
```

Migration thêm cột SKU, unique index, extension `pg_trgm` và hai GIN index. Tài khoản database cần quyền tạo extension/index. Rollback gỡ các index và cột SKU, giữ extension để không ảnh hưởng chức năng khác. Trên MySQL/SQLite, migration chỉ thêm SKU; search dùng chuỗi con, không có tìm gần đúng. Đây là phương án tương thích cho môi trường cũ và test SQLite, không thay thế kiểm thử PostgreSQL.

## Kiểm thử

```sh
php -d xdebug.mode=off vendor/bin/pest tests/Feature/ProductSearchTest.php
php -d xdebug.mode=off tests/Support/run-postgres-search.php
```

Lệnh thứ hai đọc cấu hình PostgreSQL hiện tại, yêu cầu quyền `CREATEDB`, tự tạo database tên ngẫu nhiên `hp_search_test_*`, chạy migration và test trong database đó rồi xóa nó trong `finally`. Không chạy `RefreshDatabase` trên database cửa hàng. Hai test riêng về fuzzy search và GIN/B-tree được bỏ qua trên SQLite và chạy thật trên PostgreSQL.

Các tình huống gồm: tên/thương hiệu không phân biệt hoa thường, loại sản phẩm ẩn, ký tự đặc biệt, từ khóa rỗng/`0`, validation, phân trang/sắp xếp, autocomplete, tìm chính xác SKU/ID, phân quyền admin, xếp hạng kết quả gõ sai và cấu trúc index.

## Kiểm tra hiệu năng khi dữ liệu tăng

```sql
ANALYZE products;
EXPLAIN (ANALYZE, BUFFERS)
SELECT id, name FROM products
WHERE is_active = true
  AND (name ILIKE '%converse%' OR brand ILIKE '%converse%'
       OR 'converse' <% name OR 'converse' <% brand);
```

Planner có thể chọn quét bảng khi bảng nhỏ; việc có GIN không đảm bảo mọi truy vấn đều dùng index. Từ khóa 1–2 ký tự ít hưởng lợi từ trigram index. Chưa có benchmark tải lớn.

Tham khảo: https://www.postgresql.org/docs/current/pgtrgm.html
