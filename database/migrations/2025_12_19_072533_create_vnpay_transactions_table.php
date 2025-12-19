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
        Schema::create('vnpay_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('txn_ref')->unique()->comment('Mã giao dịch HP Sneakers');
            $table->decimal('amount', 15, 2)->comment('Số tiền thanh toán');
            $table->text('order_info')->comment('Thông tin đơn hàng');
            $table->string('bank_code')->nullable()->comment('Mã ngân hàng');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('vnpay_transaction_no')->nullable();
            $table->string('response_code')->nullable();
            $table->json('response_data')->nullable();
            $table->string('ip_address');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vnpay_transactions');
    }
};
