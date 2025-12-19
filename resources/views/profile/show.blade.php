@extends('layouts.app')

@section('title', 'Thông tin tài khoản')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Thông tin tài khoản</h1>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Thông tin cá nhân</h2>
                    <a href="{{ route('profile.edit') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="border-b pb-4">
                        <label class="text-gray-600 text-sm">Họ và tên</label>
                        <p class="text-lg font-medium">{{ $user->name }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <label class="text-gray-600 text-sm">Email</label>
                        <p class="text-lg font-medium">{{ $user->email }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <label class="text-gray-600 text-sm">Số điện thoại</label>
                        <p class="text-lg font-medium">{{ $user->phone ?? 'Chưa cập nhật' }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <label class="text-gray-600 text-sm">Ngày tham gia</label>
                        <p class="text-lg font-medium">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('profile.edit-password') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-key mr-2"></i>Đổi mật khẩu
                    </a>
                </div>
            </div>
        </div>

        <!-- Order History Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Lịch sử đơn hàng</h2>
                @if($user->orders && $user->orders->count() > 0)
                <div class="space-y-4">
                    @foreach($user->orders as $order)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium">Đơn hàng #{{ $order->id }}</p>
                                <p class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-lg">{{ number_format($order->total) }}đ</p>
                                <span class="inline-block px-2 py-1 text-xs rounded 
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-600">Bạn chưa có đơn hàng nào</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
