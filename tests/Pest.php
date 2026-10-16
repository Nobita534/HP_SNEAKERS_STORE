<?php

/*
|--------------------------------------------------------------------------
| Lớp kiểm thử
|--------------------------------------------------------------------------
|
| Mỗi hàm test được gắn với một lớp test của PHPUnit. Mặc định là
| PHPUnit\Framework\TestCase; dùng pest() để chọn lớp và trait phù hợp.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Các phép kiểm tra
|--------------------------------------------------------------------------
|
| Dùng expect() để kiểm tra các điều kiện mong đợi trong test.
| Có thể mở rộng bộ phép kiểm tra khi cần.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Hàm hỗ trợ
|--------------------------------------------------------------------------
|
| Định nghĩa các hàm dùng chung để tránh lặp lại mã trong nhiều file test.
|
*/

function something()
{
    // Chỗ dành cho hàm hỗ trợ dùng chung khi cần.
}
