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
        // Thêm trường vào bảng products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('average_cost', 10, 2)->default(0)->after('price')->comment('Giá vốn trung bình (WAC)');
            $table->decimal('profit_margin', 5, 2)->default(0)->after('average_cost')->comment('% Lợi nhuận');
        });

        // Thêm trường vào bảng product_sizes
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->integer('total_imported')->default(0)->after('quantity')->comment('Tổng số lượng đã nhập');
            $table->integer('total_sold')->default(0)->after('total_imported')->comment('Tổng số lượng đã bán');
            $table->decimal('average_cost_per_size', 10, 2)->default(0)->after('total_sold')->comment('Giá vốn TB theo size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['average_cost', 'profit_margin']);
        });

        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropColumn(['total_imported', 'total_sold', 'average_cost_per_size']);
        });
    }
};
