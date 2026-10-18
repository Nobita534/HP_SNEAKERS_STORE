<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

beforeEach(function () {
    // Test HTTP không phụ thuộc việc đã build tài nguyên frontend hay chưa.
    $this->withoutVite();
});

function searchableProduct(array $attributes = []): Product
{
    $category = Category::create(['name' => 'Giày', 'slug' => (string) Str::uuid()]);

    return Product::create(array_merge([
        'category_id' => $category->id,
        'name' => 'Nike Air Force 1',
        'slug' => (string) Str::uuid(),
        'brand' => 'Nike',
        'price' => 1000000,
        'is_active' => true,
    ], $attributes));
}

test('search finds names and brands without exposing inactive or description-only matches', function () {
    $name = searchableProduct();
    $brand = searchableProduct(['name' => 'Running shoes']);
    searchableProduct(['is_active' => false]);
    searchableProduct(['name' => 'Classic', 'brand' => 'Vans', 'description' => 'Nike']);

    $this->get(route('products.search', ['q' => '  nIkE  ']))->assertOk()
        ->assertViewHas('keyword', 'nIkE')
        ->assertViewHas('products', fn ($products) => $products->pluck('id')->all() === [$name->id, $brand->id]);
});

test('search treats wildcard and escape characters as literal text', function (string $keyword) {
    $expected = searchableProduct(['name' => 'Special '.$keyword.' edition', 'brand' => 'Other']);
    searchableProduct(['name' => 'Special regular edition', 'brand' => 'Other']);

    $this->get(route('products.search', ['q' => $keyword]))->assertOk()
        ->assertViewHas('products', fn ($products) => $products->pluck('id')->all() === [$expected->id]);
})->with(['%', '_', '!', "' OR 1=1 --"]);

test('search keeps sorting pagination and the full result count', function () {
    foreach (range(1, 12) as $price) {
        searchableProduct(['price' => $price * 100000]);
    }

    $this->get(route('products.search', ['q' => 'Nike', 'sort' => 'price_desc', 'page' => 2]))
        ->assertOk()->assertViewHas('totalProducts', 12)
        ->assertViewHas('products', fn ($products) => $products->count() === 3
            && $products->first()->price === '300000.00'
            && str_contains($products->url(1), 'sort=price_desc')
            && str_contains($products->url(1), 'q=Nike'));
});

test('empty search lists active products and zero is a valid keyword', function () {
    $zero = searchableProduct(['name' => 'Runner 0']);
    searchableProduct(['name' => 'Runner 1']);
    searchableProduct(['is_active' => false]);

    $this->get(route('products.search', ['q' => ' ']))->assertOk()->assertViewHas('totalProducts', 2);
    $this->get(route('products.search', ['q' => '0']))->assertOk()
        ->assertViewHas('products', fn ($products) => $products->pluck('id')->all() === [$zero->id]);
});

test('autocomplete filters hidden products and brands and limits results', function () {
    foreach (range(1, 10) as $index) {
        searchableProduct(['name' => 'Nike '.$index]);
    }
    $hidden = searchableProduct(['name' => 'Hidden Nike', 'brand' => 'Nike Secret', 'is_active' => false]);

    $response = $this->getJson(route('products.search-suggestions', ['q' => 'ni']))
        ->assertOk()->assertJsonCount(8, 'products')->assertJsonCount(1, 'brands')
        ->assertJsonPath('brands.0.name', 'Nike');
    expect(collect($response->json('products'))->pluck('id'))->not->toContain($hidden->id);

    $this->getJson(route('products.search-suggestions', ['q' => 'ế']))
        ->assertExactJson(['products' => [], 'brands' => []]);
    $this->getJson(route('products.search-suggestions'))
        ->assertExactJson(['products' => [], 'brands' => []]);
});

test('search endpoints reject invalid input', function () {
    $this->getJson(route('products.search', ['q' => ['Nike']]))->assertUnprocessable()->assertJsonValidationErrors('q');
    $this->getJson(route('products.search', ['sort' => 'invalid']))->assertUnprocessable()->assertJsonValidationErrors('sort');
    $this->getJson(route('products.search-suggestions', ['q' => str_repeat('a', 101)]))
        ->assertUnprocessable()->assertJsonValidationErrors('q');
});

test('admin searches SKU or ID exactly including inactive products', function () {
    $product = searchableProduct(['sku' => 'HP-001', 'is_active' => false]);
    searchableProduct(['sku' => 'HP-001-EXTRA']);
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    foreach ([['search_by' => 'sku', 'q' => ' HP-001 '], ['search_by' => 'id', 'q' => (string) $product->id]] as $query) {
        $this->get(route('admin.products.index', $query))->assertOk()
            ->assertViewHas('products', fn ($products) => $products->pluck('id')->all() === [$product->id]);
    }
    foreach ([['search_by' => 'sku', 'q' => 'HP-00'], ['search_by' => 'id', 'q' => '1 OR 1=1'], ['search_by' => 'id', 'q' => str_repeat('9', 30)]] as $query) {
        $this->get(route('admin.products.index', $query))->assertOk()
            ->assertViewHas('products', fn ($products) => $products->isEmpty());
    }
});

test('admin search remains protected by authentication and admin role', function () {
    $this->get(route('admin.products.index'))->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create(['role' => 'user']))
        ->get(route('admin.products.index'))->assertForbidden();
});

test('SKU permits legacy products without a code but rejects duplicate codes', function () {
    searchableProduct();
    searchableProduct();
    searchableProduct(['sku' => 'HP-UNIQUE']);

    $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);
    searchableProduct(['sku' => 'HP-UNIQUE']);
});

test('postgres search ranks literal matches before typos and shares fuzzy matching with suggestions', function () {
    if (DB::getDriverName() !== 'pgsql') {
        $this->markTestSkipped('Cần PostgreSQL thật để kiểm tra pg_trgm.');
    }

    // Tạo kết quả gần giống trước để chứng minh thứ tự không chỉ dựa vào ID.
    $fuzzy = searchableProduct(['name' => 'Converse Chuck Taylor', 'brand' => 'Converse']);
    $literal = searchableProduct(['name' => 'Conversee Edition', 'brand' => 'Other']);
    searchableProduct(['name' => 'Converse Hidden', 'brand' => 'Converse', 'is_active' => false]);
    searchableProduct(['name' => 'Unrelated runner', 'brand' => 'Other']);

    $this->get(route('products.search', ['q' => 'conversee']))->assertOk()
        ->assertViewHas('products', fn ($products) => $products->pluck('id')->all() === [$literal->id, $fuzzy->id]);
    $this->getJson(route('products.search-suggestions', ['q' => 'conversee']))->assertOk()
        ->assertJsonPath('products.0.id', $literal->id)->assertJsonPath('products.1.id', $fuzzy->id)
        ->assertJsonPath('brands.0.name', 'Converse');
});

test('postgres search migration creates GIN and unique B-tree indexes', function () {
    if (DB::getDriverName() !== 'pgsql') {
        $this->markTestSkipped('Cần PostgreSQL thật để kiểm tra index.');
    }

    $indexes = collect(DB::select("SELECT indexname, indexdef FROM pg_indexes WHERE tablename = 'products' AND schemaname = current_schema()"))
        ->pluck('indexdef', 'indexname');
    expect($indexes['products_sku_unique'])->toContain('UNIQUE INDEX', 'USING btree (sku)');
    expect($indexes['products_name_trgm_idx'])->toContain('USING gin (name gin_trgm_ops)');
    expect($indexes['products_brand_trgm_idx'])->toContain('USING gin (brand gin_trgm_ops)');
});
