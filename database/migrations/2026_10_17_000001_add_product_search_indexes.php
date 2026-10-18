<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Không tự tạo SKU cho hàng cũ; mã thực tế sẽ được nhập từ CSV.
            $table->string('sku', 100)->nullable()->unique();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
            DB::statement('CREATE INDEX products_name_trgm_idx ON products USING GIN (name gin_trgm_ops)');
            DB::statement('CREATE INDEX products_brand_trgm_idx ON products USING GIN (brand gin_trgm_ops)');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS products_name_trgm_idx');
            DB::statement('DROP INDEX IF EXISTS products_brand_trgm_idx');
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn('sku');
        });
        // Giữ extension vì chức năng khác có thể đang sử dụng.
    }
};
