<?php

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\VnpayTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Tests\Support\PurchaseScenario;

test('FR-15 rejects purchase when stock is insufficient', function () {

    $category = Category::create([
        'name' => 'Sneakers',
        'slug' => 'test-sneakers',
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Test sneaker',
        'slug' => 'test-sneaker',
        'price' => 1500000,
        'brand' => 'Test brand',
    ]);

    $productSize = ProductSize::create([
        'product_id' => $product->id,
        'size' => '42',
        'quantity' => 1,
        'total_imported' => 1,
        'total_sold' => 0,
        'average_cost_per_size' => 1000000,
    ]);

    expect(
        $productSize->isAvailable(2)
    )->toBeFalse();

    expect(
        $productSize->fresh()->quantity
    )->toBe(1);
});

test('FR-15 allows purchase when stock is sufficient', function () {

    $category = Category::create([
        'name' => 'Sneakers',
        'slug' => 'test-sneakers',
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Test sneaker',
        'slug' => 'test-sneaker',
        'price' => 1500000,
        'brand' => 'Test brand',
    ]);

    $productSize = ProductSize::create([
        'product_id' => $product->id,
        'size' => '42',
        'quantity' => 5,
        'total_imported' => 5,
        'total_sold' => 0,
        'average_cost_per_size' => 1000000,
    ]);

    expect(
        $productSize->isAvailable(2)
    )->toBeTrue();
});

test('FR-17 commits order and inventory together', function () {
    $first = PurchaseScenario::variant(5);
    $second = PurchaseScenario::variant(3);
    $purchase = new PurchaseScenario($first, 2);
    $purchase->addItem($second, 1);

    $this->actingAs($purchase->user)->get($purchase->callbackUrl())
        ->assertRedirect(route('home'))->assertSessionHas('success');

    $order = Order::sole();
    expect($first->fresh()->quantity)->toBe(3);
    expect($second->fresh()->quantity)->toBe(2);
    expect($order->items()->sum('quantity'))->toEqual(3);
    expect($order->total)->toBe('3000000.00');
    $this->assertDatabaseCount('order_items', 2);
    $this->assertDatabaseHas('vnpay_transactions', [
        'id' => $purchase->transaction->id,
        'order_id' => $order->id,
        'status' => 'success',
    ]);
    $this->assertDatabaseMissing('carts', ['id' => $purchase->cart->id]);
});

test('FR-17 rolls back order when inventory update fails', function () {
    $first = PurchaseScenario::variant(5);
    $second = PurchaseScenario::variant(1);
    $purchase = new PurchaseScenario($first, 2);
    $purchase->addItem($second, 1);
    $before = PurchaseScenario::snapshot();
    $transactionLevel = DB::transactionLevel();
    $injected = false;
    $earlierStock = null;
    $dispatcher = Model::getEventDispatcher();
    Model::setEventDispatcher(clone $dispatcher);

    // Gây lỗi sau khi kiểm tra tồn, ngay trước bước cập nhật nguyên tử.
    // Đây không phải giao dịch cạnh tranh thật; thay đổi tạm thời cũng bị rollback.
    OrderItem::created(function (OrderItem $item) use ($first, $second, &$injected, &$earlierStock) {
        if ($item->product_id === $second->product_id) {
            $earlierStock = $first->fresh()->quantity;
            ProductSize::whereKey($second->id)->update(['quantity' => 0]);
            $injected = true;
        }
    });
    try {
        $response = $this->actingAs($purchase->user)->get($purchase->callbackUrl());
    } finally {
        Model::setEventDispatcher($dispatcher);
    }

    $response->assertRedirect(route('cart.index'))
        ->assertSessionHas('error', fn ($message) => str_contains($message, 'Không đủ tồn kho hoặc variant không còn tồn tại.'));
    expect($injected)->toBeTrue();
    expect($earlierStock)->toBe(3);
    expect(PurchaseScenario::snapshot())->toEqual($before);
    expect(DB::transactionLevel())->toBe($transactionLevel);
});

test('FR-17 restores all purchase data when a workflow step fails', function (
    string $modelClass,
    string $event,
    array $expectedProgress,
) {
    $first = PurchaseScenario::variant(5);
    $second = PurchaseScenario::variant(3);
    $purchase = new PurchaseScenario($first, 2);
    $purchase->addItem($second, 1);
    $before = PurchaseScenario::snapshot();
    $transactionLevel = DB::transactionLevel();
    $progress = null;
    $failureMessage = 'Lỗi giả lập tại bước ghi dữ liệu mua hàng.';
    $dispatcher = Model::getEventDispatcher();
    Model::setEventDispatcher(clone $dispatcher);

    // Gây lỗi ở từng bước và ghi nhận dữ liệu đã thay đổi trước khi rollback.
    $modelClass::$event(function (Model $model) use ($first, $second, &$progress, $failureMessage) {
        if ($model instanceof OrderItem && $model->product_id !== $second->product_id) {
            return;
        }

        $progress = [
            'orders' => Order::count(),
            'items' => OrderItem::count(),
            'stock' => [$first->fresh()->quantity, $second->fresh()->quantity],
            'payment' => VnpayTransaction::sole()->status,
            'carts' => Cart::count(),
            'cart_items' => DB::table('cart_items')->count(),
        ];

        throw new RuntimeException($failureMessage);
    });

    try {
        $response = $this->actingAs($purchase->user)->get($purchase->callbackUrl());
    } finally {
        // Khôi phục bộ sự kiện để lỗi giả lập không ảnh hưởng các test khác.
        Model::setEventDispatcher($dispatcher);
    }

    $response->assertRedirect(route('cart.index'))
        ->assertSessionHas('error', fn ($message) => str_contains($message, $failureMessage))
        ->assertSessionMissing('success');

    // Xác nhận đã đi đến đúng bước gây lỗi, tránh test pass khi bị chặn từ trước.
    expect($progress)->toBe($expectedProgress);
    expect(PurchaseScenario::snapshot())->toEqual($before);
    expect(DB::transactionLevel())->toBe($transactionLevel);
})->with([
    'lỗi sau khi ghi đơn hàng' => [Order::class, 'created', [
        'orders' => 1, 'items' => 0, 'stock' => [5, 3],
        'payment' => 'pending', 'carts' => 1, 'cart_items' => 2,
    ]],
    'lỗi sau khi ghi chi tiết sản phẩm thứ hai' => [OrderItem::class, 'created', [
        'orders' => 1, 'items' => 2, 'stock' => [3, 3],
        'payment' => 'pending', 'carts' => 1, 'cart_items' => 2,
    ]],
    'lỗi trước khi lưu trạng thái thanh toán' => [VnpayTransaction::class, 'updating', [
        'orders' => 1, 'items' => 2, 'stock' => [3, 2],
        'payment' => 'pending', 'carts' => 1, 'cart_items' => 2,
    ]],
    'lỗi sau khi lưu trạng thái thanh toán' => [VnpayTransaction::class, 'updated', [
        'orders' => 1, 'items' => 2, 'stock' => [3, 2],
        'payment' => 'success', 'carts' => 1, 'cart_items' => 2,
    ]],
    'lỗi sau khi xóa giỏ hàng và các dòng sản phẩm' => [Cart::class, 'deleted', [
        'orders' => 1, 'items' => 2, 'stock' => [3, 2],
        'payment' => 'success', 'carts' => 0, 'cart_items' => 0,
    ]],
])->group('fr17');
