<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('import_code')->comment('Mã phiếu nhập');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('size', 10)->comment('Size giày');
            $table->integer('quantity')->comment('Số lượng nhập');
            $table->decimal('import_price', 10, 2)->comment('Giá nhập/đôi');
            $table->decimal('total_cost', 15, 2)->comment('Tổng tiền');
            $table->enum('type', ['import', 'adjustment'])->default('import')->comment('Loại giao dịch');
            $table->text('note')->nullable()->comment('Ghi chú');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Người nhập');
            $table->timestamp('imported_at')->useCurrent()->comment('Thời gian nhập');
            $table->timestamps();
            
            // Index cho tìm kiếm
            $table->index('import_code');
            $table->index(['product_id', 'size']);
            $table->index('imported_at');
            
            // Đảm bảo mỗi phiếu nhập chỉ có 1 bản ghi cho mỗi sản phẩm + size
            $table->unique(['import_code', 'product_id', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
