<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Get or create cart for current user/session
     */
    private function getCart()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => Session::getId()]
            );
        } else {
            $cart = Cart::firstOrCreate(
                ['session_id' => Session::getId()],
                ['user_id' => null]
            );
        }

        return $cart;
    }

    /**
     * Display cart page
     */
    public function index()
    {
        $cart = $this->getCart();
        $cartItems = $cart->items()->with('product')->get();

        return view('cart.index', compact('cart', 'cartItems'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'size' => 'required|string',
            'quantity' => 'integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $size = $validated['size'];
        $quantity = $validated['quantity'] ?? 1;

        // Check size availability
        $productSize = ProductSize::where('product_id', $product->id)
            ->where('size', $size)
            ->first();

        if (!$productSize || $productSize->quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không đủ số lượng trong kho'
            ], 400);
        }

        $cart = $this->getCart();

        // Check if item already exists in cart
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('size', $size)
            ->first();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $quantity;

            if ($productSize->quantity < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không đủ hàng trong kho'
                ], 400);
            }

            $existingItem->quantity = $newQuantity;
            $existingItem->save();
        } else {
            // Create new cart item
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'size' => $size,
                'price' => $product->sale_price ?? $product->price,
            ]);
        }

        // Refresh cart to get updated data
        $cart->refresh();
        $latestItems = $cart->items()
            ->with('product')
            ->latest()
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => $cart->getTotalItems(),
            'cart_total' => $cart->getTotal(),
            'cart_items' => $latestItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'product_image' => asset($item->product->image),
                    'product_url' => route('products.show', $item->product->id),
                    'size' => $item->size,
                    'quantity' => $item->quantity,
                    'price' => $item->product->sale_price ?? $item->product->price,
                    'subtotal' => $item->quantity * ($item->product->sale_price ?? $item->product->price)
                ];
            })
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($id);

        // Check stock
        $productSize = ProductSize::where('product_id', $cartItem->product_id)
            ->where('size', $cartItem->size)
            ->first();

        if (!$productSize || $productSize->quantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Không đủ hàng trong kho'
            ], 400);
        }

        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng',
            'subtotal' => $cartItem->getSubtotal(),
            'total' => $cartItem->cart->getTotal()
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cart = $cartItem->cart;
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi giỏ hàng',
            'cart_count' => $cart->getTotalItems(),
            'total' => $cart->getTotal()
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $cart = $this->getCart();
        $cart->items()->delete();

        return redirect()->route('cart.index')->with('success', 'Đã xóa toàn bộ giỏ hàng');
    }
}
