<?php

use App\Models\ProductSize;
use Tests\Support\InventoryRace;
use Tests\Support\PurchaseScenario;

test('FR-16 two processes cannot sell more than shared stock', function (int $stock, int $first, int $second, int $successful) {
    [$results, $remaining] = InventoryRace::run($stock, $first, $second);

    expect(array_count_values($results)['success'] ?? 0)->toBe($successful);
    expect(array_count_values($results)['rejected'] ?? 0)->toBe(2 - $successful);
    expect($remaining)->toBe($stock - ($successful === 2 ? $first + $second : $first));
})->with([
    'hai khách tranh đôi cuối' => [1, 1, 1, 1],
    'tổng lượng mua vừa đủ tồn' => [3, 2, 1, 2],
])->group('fr16');

test('FR-16 rejects a stale purchase when another buyer consumed the stock', function (int $stock, int $first, int $second) {
    $variant = PurchaseScenario::variant($stock);
    $buyerA = ProductSize::findOrFail($variant->id);
    $buyerB = ProductSize::findOrFail($variant->id);
    expect($buyerA->isAvailable($first))->toBeTrue();
    expect($buyerB->isAvailable($second))->toBeTrue();

    // Xen kẽ có kiểm soát: A đọc, B đọc, A ghi, B ghi; chưa phải hai request song song.
    $buyerA->decreaseQuantity($first);

    expect(fn () => $buyerB->decreaseQuantity($second))
        ->toThrow(RuntimeException::class, 'Không đủ tồn kho hoặc variant không còn tồn tại.');
    expect($variant->fresh()->quantity)->toBe($stock - $first);
})->with([
    'last pair' => [1, 1, 1],
    'not enough remains' => [3, 2, 2],
    'first buyer consumes everything' => [5, 5, 1],
])->group('fr16');

test('FR-16 permits both stale buyers when combined demand fits inventory', function () {
    $variant = PurchaseScenario::variant(3);
    $buyerA = ProductSize::findOrFail($variant->id);
    $buyerB = ProductSize::findOrFail($variant->id);

    $buyerA->decreaseQuantity(2);
    $buyerB->decreaseQuantity(1);

    expect($variant->fresh()->quantity)->toBe(0);
    expect($buyerB->quantity)->toBe(0);
})->group('fr16');

test('FR-16 uses database stock when the loaded model is older than a restock', function () {
    $variant = PurchaseScenario::variant(0);
    ProductSize::whereKey($variant->id)->update(['quantity' => 2]);

    $variant->decreaseQuantity(1);

    expect($variant->quantity)->toBe(1);
    expect($variant->fresh()->quantity)->toBe(1);
})->group('fr16');

test('stock deductions reject non-positive amounts without changing inventory', function (int $amount) {
    $variant = PurchaseScenario::variant(3);
    $before = PurchaseScenario::snapshot();

    expect(fn () => $variant->decreaseQuantity($amount))->toThrow(InvalidArgumentException::class);

    expect(PurchaseScenario::snapshot())->toEqual($before);
})->with([0, -1])->group('fr16');

test('FR-16 rejects a deduction if the variant was deleted after reading it', function () {
    $variant = PurchaseScenario::variant(3);
    ProductSize::whereKey($variant->id)->delete();

    expect(fn () => $variant->decreaseQuantity(1))->toThrow(RuntimeException::class);
    $this->assertDatabaseMissing('product_sizes', ['id' => $variant->id]);
})->group('fr16');

test('stock deductions affect only the selected variant and support repeated calls', function () {
    $variant = PurchaseScenario::variant(3);
    $other = PurchaseScenario::variant(7, '43', $variant->product);

    $variant->decreaseQuantity(1);
    $variant->decreaseQuantity(2);

    expect($variant->quantity)->toBe(0);
    expect($variant->fresh()->quantity)->toBe(0);
    expect($other->fresh()->quantity)->toBe(7);
})->group('fr16');
