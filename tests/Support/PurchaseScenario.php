<?php

namespace Tests\Support;

use App\Http\Controllers\PaymentController;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\User;
use App\Models\VnpayTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;
use ReflectionClass;

/** Dữ liệu mẫu cho luồng tạo đơn VNPay hiện tại; không gọi ra mạng. */
class PurchaseScenario
{
    public User $user;

    public Cart $cart;

    public VnpayTransaction $transaction;

    public function __construct(ProductSize $variant, int $quantity)
    {
        $this->user = User::factory()->create();
        $this->cart = Cart::create(['user_id' => $this->user->id]);
        $this->transaction = VnpayTransaction::create([
            'txn_ref' => 'TEST-'.Str::uuid(),
            'amount' => 0,
            'order_info' => 'Purchase inventory acceptance test',
            'status' => 'pending',
            'ip_address' => '127.0.0.1',
        ]);
        $this->addItem($variant, $quantity);
    }

    public static function variant(int $stock, string $size = '42', ?Product $product = null): ProductSize
    {
        if ($product === null) {
            $category = Category::create(['name' => 'Test sneakers', 'slug' => 'test-'.Str::uuid()]);
            $product = Product::create([
                'category_id' => $category->id,
                'name' => 'Inventory test sneaker',
                'slug' => 'test-'.Str::uuid(),
                'price' => 1000000,
                'brand' => 'Test brand',
                'images' => ['test-sneaker.jpg'],
            ]);
        }

        return ProductSize::create([
            'product_id' => $product->id,
            'size' => $size,
            'quantity' => $stock,
            'total_imported' => $stock,
            'total_sold' => 0,
            'average_cost_per_size' => 500000,
        ]);
    }

    public function addItem(ProductSize $variant, int $quantity): void
    {
        $this->cart->items()->create([
            'product_id' => $variant->product_id,
            'size' => $variant->size,
            'quantity' => $quantity,
            'price' => 1000000,
        ]);
        $this->transaction->update([
            'amount' => $this->cart->items()->get()->sum(fn ($item) => $item->price * $item->quantity),
        ]);
    }

    public function callbackUrl(array $overrides = []): string
    {
        // Controller hiện ghi trực tiếp khóa ký; đọc tại chỗ để không sao chép khóa
        // hoặc bỏ qua bước kiểm tra chữ ký. Chuyển sang config() khi ứng dụng đổi cấu hình.
        $source = file_get_contents((new ReflectionClass(PaymentController::class))->getFileName());
        if (! preg_match('/\$vnp_HashSecret\s*=\s*[\'"]([^\'"]+)[\'"]\s*;/', $source, $match)) {
            throw new LogicException('Update the test signing adapter to match VNPay key configuration.');
        }

        $parameters = array_replace([
            'vnp_Amount' => (string) (int) round((float) $this->transaction->fresh()->amount * 100),
            'vnp_ResponseCode' => '00',
            'vnp_TransactionNo' => '123456789',
            'vnp_TxnRef' => $this->transaction->txn_ref,
        ], $overrides);
        ksort($parameters);
        $parameters['vnp_SecureHash'] = hash_hmac(
            'sha512', http_build_query($parameters, '', '&', PHP_QUERY_RFC1738), $match[1]
        );

        return route('vnpay.return', $parameters);
    }

    /** So sánh dữ liệu đã lưu để phát hiện bản ghi sót, liên kết sai và giỏ bị mất. */
    public static function snapshot(): array
    {
        $snapshot = [];
        foreach (['orders', 'order_items', 'product_sizes', 'vnpay_transactions', 'carts', 'cart_items'] as $table) {
            $snapshot[$table] = DB::table($table)->orderBy('id')->get()->map(fn ($row) => (array) $row)->all();
        }

        return $snapshot;
    }
}
