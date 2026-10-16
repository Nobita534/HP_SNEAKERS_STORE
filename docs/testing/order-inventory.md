# Kiểm thử Order và Inventory

Các test nằm trong `tests/Feature/PurchaseInventoryTest.php`, `tests/Feature/PaymentInventoryTest.php` và `tests/Feature/ProductSizeAtomicTest.php`. Dữ liệu mẫu và hỗ trợ chạy hai process nằm trong `tests/Support/`.

## Các yêu cầu được kiểm tra

| FR | Điều kiện cần giữ | Các test chính |
| --- | --- | --- |
| FR-15 — Stock Availability Check | Product, size và số lượng hợp lệ; không cho bắt đầu thanh toán hoặc hoàn tất mua khi thiếu hàng | Kiểm tra ở model, khi POST bắt đầu thanh toán, và khi callback VNPay đến |
| FR-16 — Concurrent Inventory Control | Tổng lượng bán không vượt tồn thực tế; một lượt mua dùng dữ liệu cũ phải bị từ chối khi không còn đủ hàng | Hai instance đọc cùng tồn cũ; hai process dùng chung SQLite cùng thực hiện cập nhật |
| FR-17 — Order–Inventory Consistency | Order, OrderItem, Inventory và trạng thái giao dịch cùng thành công hoặc cùng rollback | Callback mua nhiều sản phẩm; lỗi tại bước trừ kho sau khi sản phẩm trước đã được ghi |

Luồng tạo đơn của code hiện tại nằm trong `PaymentController::vnpayReturn()`. Các test đi qua route thật với chữ ký callback hợp lệ, không gọi ra VNPay.

## Các điểm kiểm tra tồn kho

1. `CartController::add()` và `CartController::update()` kiểm tra để báo sớm khi khách thêm hoặc sửa giỏ. Thêm vào giỏ không giữ hàng.
2. `PaymentController::vnpay_payment()` kiểm tra lại toàn bộ giỏ, cộng nhu cầu của các dòng cùng variant, rồi mới chuyển khách sang VNPay.
3. `PaymentController::createPaidOrder()` kiểm tra lại khi callback đến, vì tồn có thể thay đổi trong lúc khách thanh toán. Tạo Order/OrderItem và trừ kho chạy trong cùng database transaction.
4. `ProductSize::decreaseQuantity()` dùng một câu `UPDATE` có điều kiện `quantity >= số_lượng_mua`. Nếu không có dòng nào được cập nhật, hàm ném exception để transaction tạo đơn rollback. Đây là quyết định cuối cùng về việc đủ tồn tại thời điểm ghi.

Kiểm tra sớm giúp trả thông báo kịp thời; kết quả kiểm tra sớm không thay thế điều kiện nguyên tử tại bước trừ kho.

## Tình huống test

| Nhóm | Tình huống | Kỳ vọng |
| --- | --- | --- |
| Giá trị biên | Tồn bằng 0, bằng lượng mua, nhỏ hơn hoặc lớn hơn lượng mua | Chỉ mua được khi đủ hàng |
| Dữ liệu không hợp lệ | Product/variant bị xóa, giỏ rỗng, số lượng 0 hoặc âm | Từ chối, không tạo đơn hoặc trừ kho |
| Nhiều dòng giỏ | Hai dòng cùng variant, mỗi dòng có vẻ đủ nhưng tổng vượt tồn | Từ chối cả trước thanh toán và tại callback |
| Tạo đơn thành công | Giỏ có một hoặc nhiều sản phẩm | Order/OrderItem đúng số lượng; tồn giảm tương ứng; giao dịch thành công, giỏ được xóa |
| Trừ kho thất bại | Món đầu đã giảm kho, món tiếp theo không thể giảm | Rollback toàn bộ Order, OrderItem, tồn kho, giao dịch và giỏ |
| Dữ liệu đã cũ | A và B cùng đọc tồn 1; A trừ trước | B bị từ chối, tồn cuối là 0 |
| Hai process | Hai process cùng đọc tồn từ một SQLite file rồi được cho cập nhật cùng lúc | Khi tranh đôi cuối chỉ một process thành công; khi tổng nhu cầu vừa đủ, cả hai thành công |
| Kiểm tra payment liên quan | Sai chữ ký hoặc thanh toán bị từ chối | Không tạo Order, không trừ kho |

Trong test hai process, SQLite file là cơ sở dữ liệu dùng chung; mỗi process có Laravel connection riêng. Rào đồng bộ bảo đảm cả hai đã đọc số tồn cũ trước khi được phép cập nhật. Test xen kẽ trong một process vẫn được giữ để tái hiện chính xác trường hợp đọc cũ theo thứ tự A đọc, B đọc, A ghi, B ghi.

Test lỗi ở món sau dùng Eloquent event để giảm tồn của món đó ngay sau bước kiểm tra và trước câu cập nhật nguyên tử. Đây là cách gây lỗi có kiểm soát để kiểm tra rollback, không phải một giao dịch cạnh tranh độc lập.

## Cách chạy

```powershell
php artisan test
php artisan test --group=fr15
php artisan test --group=fr16
```

Phép thử hai process đã chạy thành công trên SQLite. Hành vi khóa, chờ và deadlock của MySQL trong môi trường triển khai vẫn cần một bài kiểm thử tích hợp riêng trên MySQL. Các vấn đề khác của callback như xử lý lặp và đối chiếu số tiền thanh toán thuộc phạm vi payment, không được kết luận từ các test tồn kho này.
