<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSize;
use App\Models\VnpayTransaction;
use Illuminate\Http\Request;


class PaymentController extends Controller
{
    public function vnpay_payment()
    {
        // if (!Auth::check()) {
        //     return redirect()->route('login')
        //         ->with('error', 'Vui lòng đăng nhập dể thanh toán');
        // }

        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart || $cart->items()->count() == 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng trống! Vui lòng thêm sản phẩm.');
        }

        $cart_Item = $cart->items()->with('product')->get();

        $total_Amount = $cart->getTotal();

        $orderInfo = 'Thanh toan don hang HP Sneakers - ' . $cart_Item->count() . ' san pham';

        $productDetails = $cart_Item->map(function ($item) {
            return $item->product->name . ' (x' . $item->quantity . ')';
        })->implode(', ');

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.return'); // ✅ Đổi thành route callback
        $vnp_TmnCode = "FZFYVKAP";
        $vnp_HashSecret = "MLYY11Y0RDVF4067JGVRS4L90DI5ZX5Z";

        $vnp_TxnRef = 'HP' . date('YmdHis') . rand(100, 999); // ✅ Sửa 'Hp' thành 'HP'
        $vnp_OrderInfo = $orderInfo; // ✅ Dùng orderInfo có thông tin chi tiết
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $total_Amount * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        // ✅ Lưu transaction vào DB
        VnpayTransaction::create([
            'txn_ref' => $vnp_TxnRef,
            'amount' => $total_Amount,
            'order_info' => $orderInfo . ' | ' . $productDetails,
            'bank_code' => $vnp_BankCode,
            'status' => 'pending',
            'ip_address' => $vnp_IpAddr,
        ]);

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // ✅ Log để debug
        Log::info('VNPay Payment Created:', [
            'user_id' => Auth::id(),
            'txn_ref' => $vnp_TxnRef,
            'amount' => $total_Amount,
            'items_count' => $cart_Item->count(),
            'products' => $productDetails,
        ]);

        // ✅ Redirect đến VNPay (dùng Laravel redirect thay vì header)
        return redirect($vnp_Url);
    }

    /**
     * ✅ XỬ LÝ CALLBACK TỪ VNPAY
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = "MLYY11Y0RDVF4067JGVRS4L90DI5ZX5Z";

        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        // Remove hash params để verify
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        // Validate signature
        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra chữ ký
        if ($secureHash !== $vnp_SecureHash) {
            return redirect()->route('cart.index')
                ->with('error', 'Chữ ký không hợp lệ!');
        }

        // Lấy thông tin giao dịch
        $vnp_TxnRef = $request->vnp_TxnRef;
        $vnp_ResponseCode = $request->vnp_ResponseCode;
        $vnp_TransactionNo = $request->vnp_TransactionNo ?? null;
        $vnp_Amount = $request->vnp_Amount / 100; // Chia 100 để về số tiền thực

        // Tìm transaction trong DB
        $transaction = VnpayTransaction::where('txn_ref', $vnp_TxnRef)->first();

        if (!$transaction) {
            return redirect()->route('cart.index')
                ->with('error', 'Không tìm thấy giao dịch!');
        }

        // ✅ THANH TOÁN THÀNH CÔNG
        if ($vnp_ResponseCode == '00') {
            try {
                DB::beginTransaction();

                // Lấy giỏ hàng
                $cart = Cart::where('user_id', Auth::id())->first();
                if (!$cart) {
                    throw new \Exception('Không tìm thấy giỏ hàng');
                }

                $cartItems = $cart->items()->with('product')->get();

                // Lấy thông tin user để làm shipping info
                $user = Auth::user();
                
                // Lấy địa chỉ mặc định của user
                $defaultAddress = $user->addresses()->where('is_default', true)->first();
                
                // Nếu không có địa chỉ mặc định, lấy địa chỉ đầu tiên
                if (!$defaultAddress) {
                    $defaultAddress = $user->addresses()->first();
                }

                // Tạo Order
                $order = Order::create([
                    'order_number' => $vnp_TxnRef,
                    'user_id' => Auth::id(),
                    'address_id' => $defaultAddress ? $defaultAddress->id : null,
                    'shipping_name' => $defaultAddress ? $defaultAddress->name : $user->name,
                    'shipping_phone' => $defaultAddress ? $defaultAddress->phone : ($user->phone ?? '0000000000'),
                    'shipping_email' => $user->email,
                    'shipping_address' => $defaultAddress ? $defaultAddress->address : ($user->address ?? 'Chưa cập nhật'),
                    'shipping_city' => $defaultAddress ? $defaultAddress->city : ($user->city ?? 'TP.HCM'),
                    'shipping_district' => $defaultAddress ? $defaultAddress->district : ($user->district ?? 'Quận 1'),
                    'shipping_ward' => $defaultAddress ? $defaultAddress->ward : ($user->ward ?? 'Phường 1'),
                    'subtotal' => $vnp_Amount,
                    'shipping_fee' => 0,
                    'discount' => 0,
                    'total' => $vnp_Amount,
                    'payment_method' => 'vnpay',
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'status' => 'pending',
                ]);

                // Tạo OrderItems và giảm stock
                foreach ($cartItems as $item) {
                    // Tạo OrderItem
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

                    // Giảm tồn kho
                    $productSize = ProductSize::where('product_id', $item->product_id)
                        ->where('size', $item->size)
                        ->first();

                    if ($productSize) {
                        $productSize->decreaseQuantity($item->quantity);
                    }
                }

                // Cập nhật transaction với order_id
                $transaction->update([
                    'status' => 'success',
                    'vnpay_transaction_no' => $vnp_TransactionNo,
                    'response_code' => $vnp_ResponseCode,
                    'response_data' => json_encode($request->all()),
                    'paid_at' => now(),
                    'order_id' => $order->id,
                ]);

                // Xóa giỏ hàng
                $cart->items()->delete();
                $cart->delete();

                DB::commit();

                // Log success
                Log::info('VNPay Payment Success:', [
                    'txn_ref' => $vnp_TxnRef,
                    'vnpay_transaction_no' => $vnp_TransactionNo,
                    'amount' => $vnp_Amount,
                    'order_id' => $order->id,
                ]);

                return redirect()->route('home')
                    ->with('success', 'Thanh toán thành công! Mã đơn hàng: ' . $vnp_TxnRef);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Order Creation Failed:', [
                    'txn_ref' => $vnp_TxnRef,
                    'error' => $e->getMessage(),
                ]);

                return redirect()->route('cart.index')
                    ->with('error', 'Có lỗi khi tạo đơn hàng: ' . $e->getMessage());
            }
        }
        // ✅ THANH TOÁN THẤT BẠI
        else {
            $transaction->update([
                'status' => 'failed',
                'response_code' => $vnp_ResponseCode,
                'response_data' => json_encode($request->all()),
            ]);

            // Danh sách mã lỗi VNPay
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

            $message = $errorMessages[$vnp_ResponseCode] ?? 'Giao dịch thất bại';

            // Log failed
            Log::warning('VNPay Payment Failed:', [
                'txn_ref' => $vnp_TxnRef,
                'response_code' => $vnp_ResponseCode,
                'message' => $message,
            ]);

            return redirect()->route('cart.index')
                ->with('error', 'Thanh toán thất bại: ' . $message);
        }
    }
}
