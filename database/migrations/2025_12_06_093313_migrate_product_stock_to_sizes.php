<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\ProductSize;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migration không còn cần thiết vì đã bỏ cột stock và sizes
        // ProductSize được tạo thông qua nhập hàng inventory
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
