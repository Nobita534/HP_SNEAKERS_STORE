<?php

use App\Models\Order;
use App\Models\VnpayTransaction;
use Illuminate\Support\Facades\DB;
use Tests\Support\PurchaseScenario;

test('FR-15 payment routes reject invalid purchases without changing data', function (string $stage, string $problem) {
    $variant = PurchaseScenario::variant(1);
    $quantity = match ($problem) {
        'insufficient stock' => 2,
        'zero quantity' => 0,
        'negative quantity' => -1,
        default => 1,
    };
    $purchase = new PurchaseScenario($variant, $quantity);

    match ($problem) {
        'missing variant' => $variant->delete(),
        'deleted product' => $variant->product->delete(),
        'sold out' => $variant->update(['quantity' => 0]),
        default => null,
    };
    $before = PurchaseScenario::snapshot();
    $transactionLevel = DB::transactionLevel();
    $this->actingAs($purchase->user);

    $response = $stage === 'before payment'
        ? $this->post(route('vnpayment'))
        : $this->get($purchase->callbackUrl());

    $response->assertRedirect(route('cart.index'))
        ->assertSessionHas('error')->assertSessionMissing('success');
    expect(PurchaseScenario::snapshot())->toEqual($before);
    expect(DB::transactionLevel())->toBe($transactionLevel);

    if (in_array($problem, ['insufficient stock', 'sold out'])) {
        $response->assertSessionHas('error', fn ($message) => str_contains($message, "Chỉ còn {$variant->fresh()->quantity} đôi"));
    }
})->with(['before payment', 'callback'])->with([
    'insufficient stock', 'sold out', 'missing variant', 'deleted product', 'zero quantity', 'negative quantity',
])->group('fr15');

test('FR-15 valid checkout builds the payment URL without deducting inventory', function () {
    $variant = PurchaseScenario::variant(3);
    $purchase = new PurchaseScenario($variant, 2);

    $response = $this->actingAs($purchase->user)->post(route('vnpayment'));

    $response->assertRedirect()->assertSessionMissing('error');
    $url = $response->headers->get('Location');
    expect(parse_url($url, PHP_URL_HOST))->toBe('sandbox.vnpayment.vn');
    parse_str(parse_url($url, PHP_URL_QUERY), $parameters);
    expect($parameters['vnp_Amount'])->toBe('200000000');
    expect($parameters['vnp_ReturnUrl'])->toBe(route('vnpay.return'));
    expect($parameters['vnp_OrderInfo'])->toBe('Thanh toan don hang HP Sneakers - 1 san pham');

    // Xác minh chữ ký gửi đi bằng bộ ký độc lập trong dữ liệu kiểm thử.
    $actualHash = $parameters['vnp_SecureHash'];
    unset($parameters['vnp_SecureHash']);
    parse_str(parse_url($purchase->callbackUrl(array_merge($parameters, [
        'vnp_ResponseCode' => null,
        'vnp_TransactionNo' => null,
    ])), PHP_URL_QUERY), $expected);
    expect($actualHash)->toBe($expected['vnp_SecureHash']);

    expect($variant->fresh()->quantity)->toBe(3);
    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseHas('vnpay_transactions', [
        'txn_ref' => $parameters['vnp_TxnRef'], 'status' => 'pending', 'amount' => 2000000,
    ]);
    $this->assertDatabaseHas('carts', ['id' => $purchase->cart->id]);
})->group('fr15');

test('FR-15 callback creates the order and deducts the exact quantity', function (int $stock, int $quantity) {
    $variant = PurchaseScenario::variant($stock);
    $purchase = new PurchaseScenario($variant, $quantity);
    $transactionLevel = DB::transactionLevel();

    $this->actingAs($purchase->user)->get($purchase->callbackUrl())
        ->assertRedirect(route('home'))->assertSessionHas('success')->assertSessionMissing('error');

    $order = Order::sole();
    expect($variant->fresh()->quantity)->toBe($stock - $quantity);
    expect($order->total)->toBe($purchase->transaction->fresh()->amount);
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id, 'product_id' => $variant->product_id,
        'size' => $variant->size, 'quantity' => $quantity,
    ]);
    expect($purchase->transaction->fresh()->status)->toBe('success');
    expect($purchase->transaction->fresh()->response_data)->toBeArray();
    $this->assertDatabaseMissing('carts', ['id' => $purchase->cart->id]);
    expect(DB::transactionLevel())->toBe($transactionLevel);
})->with(['partial stock' => [5, 2], 'exact stock' => [2, 2]])->group('fr15');

test('FR-15 a later shortage rolls back an earlier item and its inventory deduction', function () {
    $first = PurchaseScenario::variant(5);
    $second = PurchaseScenario::variant(1);
    $purchase = new PurchaseScenario($first, 2);
    $purchase->addItem($second, 2);
    $before = PurchaseScenario::snapshot();
    $transactionLevel = DB::transactionLevel();

    $this->actingAs($purchase->user)->get($purchase->callbackUrl())
        ->assertRedirect(route('cart.index'))->assertSessionHas('error');

    expect(PurchaseScenario::snapshot())->toEqual($before);
    expect(DB::transactionLevel())->toBe($transactionLevel);
})->group('fr15');

test('FR-15 multiple cart lines cannot exceed the stock of the same variant', function (string $stage) {
    $variant = PurchaseScenario::variant(3);
    $purchase = new PurchaseScenario($variant, 2);
    $purchase->addItem($variant, 2);
    $before = PurchaseScenario::snapshot();
    $this->actingAs($purchase->user);

    $response = $stage === 'before payment'
        ? $this->post(route('vnpayment'))
        : $this->get($purchase->callbackUrl());

    $response->assertRedirect(route('cart.index'))->assertSessionHas('error');
    expect(PurchaseScenario::snapshot())->toEqual($before);
})->with(['before payment', 'callback'])->group('fr15');

test('payment callback rejects an invalid signature without changing purchase data', function () {
    $purchase = new PurchaseScenario(PurchaseScenario::variant(3), 1);
    $before = PurchaseScenario::snapshot();

    $this->actingAs($purchase->user)->get($purchase->callbackUrl().'tampered')
        ->assertRedirect(route('cart.index'))->assertSessionHas('error', 'Chữ ký không hợp lệ!');

    expect(PurchaseScenario::snapshot())->toEqual($before);
});

test('declined payment keeps the cart and inventory and records the failure', function () {
    $variant = PurchaseScenario::variant(3);
    $purchase = new PurchaseScenario($variant, 1);

    $this->actingAs($purchase->user)->get($purchase->callbackUrl(['vnp_ResponseCode' => '24']))
        ->assertRedirect(route('cart.index'))
        ->assertSessionHas('error', 'Thanh toán thất bại: Khách hàng hủy giao dịch');

    expect($variant->fresh()->quantity)->toBe(3);
    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseHas('carts', ['id' => $purchase->cart->id]);
    $transaction = VnpayTransaction::findOrFail($purchase->transaction->id);
    expect($transaction->status)->toBe('failed');
    expect($transaction->response_data['vnp_ResponseCode'])->toBe('24');
});
