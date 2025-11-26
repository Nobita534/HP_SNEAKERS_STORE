<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //khai bao class, services
        //bind() tạo mới mỗi lần gọi
        //singleton() chỉ tạo một instance duy nhất
        //instance() đăng ký một instance cụ thể
        //make() khởi tạo một class đã đăng ký
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //share dữ liệu, cấu hình view, events
    }
}
