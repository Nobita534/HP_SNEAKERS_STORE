<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSize;
use App\Models\VnpayTransaction;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function vnpay_payment(Request $request): RedirectResponse
    {
        try {
            $cart = $this->getCheckoutCart();
            $requestedQuantities = [];

            foreach ($cart->items as $item) {
                // Cùng variant có thể xuất hiện ở nhiều dòng giỏ hàng.
                $variantKey = $item->product_id.':'.$item->size;
                $requestedQuantities[$variantKey] = ($requestedQuantities[$variantKey] ?? 0) + $item->quantity;
                $this->getAvailableVariant($item, $requestedQuantities[$variantKey]);
            }
        } catch (Exception $exception) {
            return redirect()->route('cart.index')->with('error', $exception->getMessage());
        }

        $totalAmount = $cart->getTotal();
        $orderInfo = 'Thanh toan don hang HP Sneakers - '.$cart->items->count().' san pham';
        $productDetails = $cart->items->map(function (CartItem $item) {
            return $item->product->name.' (x'.$item->quantity.')';
        })->implode(', ');
        $txnRef = 'HP'.date('YmdHis').rand(100, 999);
        $bankCode = 'NCB';

        VnpayTransaction::create([
            'txn_ref' => $txnRef,
            'amount' => $totalAmount,
            'order_info' => $orderInfo.' | '.$productDetails,
            'bank_code' => $bankCode,
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        $parameters = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => 'FZFYVKAP',
            'vnp_Amount' => $totalAmount * 100,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $request->ip(),
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => $orderInfo,
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => route('vnpay.return'),
            'vnp_TxnRef' => $txnRef,
            'vnp_BankCode' => $bankCode,
        ];
        $query = $this->buildQuery($parameters);
        $paymentUrl = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?'
            .$query.'&vnp_SecureHash='.$this->signParameters($parameters);

        Log::info('VNPay Payment Created:', [
            'user_id' => Auth::id(),
            'txn_ref' => $txnRef,
            'amount' => $totalAmount,
            'items_count' => $cart->items->count(),
            'products' => $productDetails,
        ]);

        return redirect($paymentUrl);
    }

    public function vnpayReturn(Request $request): RedirectResponse
    {
        $parameters = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);
        if (! hash_equals($this->signParameters($parameters), (string) $request->input('vnp_SecureHash', ''))) {
            return redirect()->route('cart.index')->with('error', 'Chữ ký không hợp lệ!');
        }

        $transaction = VnpayTransaction::where('txn_ref', $request->vnp_TxnRef)->first();
        if (! $transaction) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy giao dịch!');
        }

        if ($request->vnp_ResponseCode !== '00') {
            return $this->handleFailedPayment($request, $transaction);
        }

        try {
            // Exception trong quá trình tạo đơn sẽ tự rollback toàn bộ thay đổi.
            $order = DB::transaction(fn () => $this->createPaidOrder($request, $transaction));
        } catch (Exception $exception) {
            Log::error('Order Creation Failed:', [
                'txn_ref' => $transaction->txn_ref,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('cart.index')
                ->with('error', 'Có lỗi khi tạo đơn hàng: '.$exception->getMessage());
        }

        Log::info('VNPay Payment Success:', [
            'txn_ref' => $transaction->txn_ref,
            'vnpay_transaction_no' => $request->vnp_TransactionNo,
            'amount' => $order->total,
            'order_id' => $order->id,
        ]);

        return redirect()->route('home')
            ->with('success', 'Thanh toán thành công! Mã đơn hàng: '.$transaction->txn_ref);
    }

    private function getCheckoutCart(): Cart
    {
        $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();

        if (! $cart || $cart->items->isEmpty()) {
            throw new Exception('Giỏ hàng trống! Vui lòng thêm sản phẩm.');
        }

        return $cart;
    }

    private function getAvailableVariant(CartItem $item, ?int $requestedQuantity = null): ProductSize
    {
        if (! $item->product) {
            throw new Exception('Sản phẩm không còn tồn tại.');
        }

        if ($item->quantity <= 0) {
            throw new Exception('Số lượng mua phải lớn hơn 0!');
        }

        $variant = ProductSize::where('product_id', $item->product_id)
            ->where('size', $item->size)
            ->first();

        if (! $variant) {
            throw new Exception('Size này không còn tồn tại.');
        }

        if (! $variant->isAvailable($requestedQuantity ?? $item->quantity)) {
            throw new Exception("Chỉ còn {$variant->quantity} đôi, không đủ số lượng yêu cầu.");
        }

        return $variant;
    }

    /** Chỉ gọi bên trong DB::transaction để order, kho và giỏ được cập nhật cùng nhau. */
    private function createPaidOrder(Request $request, VnpayTransaction $transaction): Order
    {
        $cart = $this->getCheckoutCart();
        $order = $this->createOrder($transaction->txn_ref, $request->vnp_Amount / 100);

        foreach ($cart->items as $item) {
            // Đọc lại tồn kho ngay trước mỗi lần trừ, kể cả khi nhiều dòng cùng variant.
            $variant = $this->getAvailableVariant($item);
            $this->createOrderItem($order, $item);
            $variant->decreaseQuantity($item->quantity);
        }

        $transaction->update([
            'status' => 'success',
            'vnpay_transaction_no' => $request->vnp_TransactionNo,
            'response_code' => $request->vnp_ResponseCode,
            'response_data' => $request->all(),
            'paid_at' => now(),
            'order_id' => $order->id,
        ]);

        $cart->items()->delete();
        $cart->delete();

        return $order;
    }

    private function createOrder(string $txnRef, float $amount): Order
    {
        $user = Auth::user();
        $address = $user->addresses()->where('is_default', true)->first()
            ?? $user->addresses()->first();

        return Order::create([
            'order_number' => $txnRef,
            'user_id' => $user->id,
            'address_id' => $address?->id,
            'shipping_name' => $address ? $address->name : $user->name,
            'shipping_phone' => $address ? $address->phone : ($user->phone ?? '0000000000'),
            'shipping_email' => $user->email,
            'shipping_address' => $address ? $address->address : ($user->address ?? 'Chưa cập nhật'),
            'shipping_city' => $address ? $address->city : ($user->city ?? 'TP.HCM'),
            'shipping_district' => $address ? $address->district : ($user->district ?? 'Quận 1'),
            'shipping_ward' => $address ? $address->ward : ($user->ward ?? 'Phường 1'),
            'subtotal' => $amount,
            'shipping_fee' => 0,
            'discount' => 0,
            'total' => $amount,
            'payment_method' => 'vnpay',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'status' => 'pending',
        ]);
    }

    private function createOrderItem(Order $order, CartItem $item): void
    {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item->product_id,
            'product_name' => $item->product->name,
            'product_image' => $item->product->images[0] ?? null,
            'quantity' => $item->quantity,
            'size' => $item->size,
            'color' => $item->color,
            'price' => $item->price,
            'total' => $item->price * $item->quantity,
        ]);
    }

    private function handleFailedPayment(Request $request, VnpayTransaction $transaction): RedirectResponse
    {
        $transaction->update([
            'status' => 'failed',
            'response_code' => $request->vnp_ResponseCode,
            'response_data' => $request->all(),
        ]);

        $errorMessages = [
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ',
            '09' => 'Thẻ chưa đăng ký InternetBanking',
            '10' => 'Xác thực sai quá 3 lần',
            '11' => 'Hết hạn chờ thanh toán (15 phút)',
            '12' => 'Thẻ bị khóa',
            '13' => 'Sai mật khẩu OTP',
            '24' => 'Khách hàng hủy giao dịch',
            '51' => 'Tài khoản không đủ số dư',
            '65' => 'Vượt quá hạn mức giao dịch',
            '75' => 'Ngân hàng đang bảo trì',
            '79' => 'Nhập sai mật khẩu quá số lần quy định',
        ];
        $message = $errorMessages[$request->vnp_ResponseCode] ?? 'Giao dịch thất bại';

        Log::warning('VNPay Payment Failed:', [
            'txn_ref' => $transaction->txn_ref,
            'response_code' => $request->vnp_ResponseCode,
            'message' => $message,
        ]);

        return redirect()->route('cart.index')->with('error', 'Thanh toán thất bại: '.$message);
    }

    private function buildQuery(array $parameters): string
    {
        ksort($parameters);

        return http_build_query($parameters, '', '&', PHP_QUERY_RFC1738);
    }

    private function signParameters(array $parameters): string
    {
        $vnp_HashSecret = 'MLYY11Y0RDVF4067JGVRS4L90DI5ZX5Z';

        return hash_hmac('sha512', $this->buildQuery($parameters), $vnp_HashSecret);
    }
}
