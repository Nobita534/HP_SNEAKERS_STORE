@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin HP Sneakers')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Người dùng</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_users']) }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-users text-3xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Sản phẩm</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_products']) }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-box text-3xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Đơn hàng</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_orders']) }}</p>
                <p class="text-sm text-orange-600 mt-1">{{ $stats['pending_orders'] }} chờ xử lý</p>
            </div>
            <div class="bg-orange-100 rounded-full p-4">
                <i class="fas fa-shopping-cart text-3xl text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Profit Margin & Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Doanh thu</p>
                <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_revenue'], 0, ',', '.') }}đ</p>
                <p class="text-sm text-green-600 mt-1">Tháng: {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}đ</p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <i class="fas fa-dollar-sign text-3xl text-purple-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Tổng nhập hàng</p>
                <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_imported'], 0, ',', '.') }}đ</p>
                <p class="text-sm text-green-600 mt-1">Tháng: {{ number_format($stats['monthly_imported'], 0, ',', '.') }}đ</p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-warehouse text-3xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Lãi</p>
                <p class="text-2xl font-bold text-green-600 mt-2">{{ number_format($stats['total_profit'], 0, ',', '.') }}đ</p>
                <p class="text-sm text-gray-600 mt-1">Tháng: {{ number_format($stats['monthly_profit'], 0, ',', '.') }}đ</p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-chart-line text-3xl text-green-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Revenue Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Doanh thu 7 ngày qua</h3>
        <div class="space-y-3">
            @forelse($revenueChart as $item)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</span>
                        <span class="font-semibold text-gray-800">{{ number_format($item->revenue, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($item->revenue / $revenueChart->max('revenue')) * 100 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">Chưa có dữ liệu doanh thu</p>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Thao tác nhanh</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('admin.products.create') }}" class="flex flex-col items-center justify-center p-6 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                <i class="fas fa-plus-circle text-4xl text-blue-600 mb-3"></i>
                <span class="text-base font-medium text-gray-700">Thêm sản phẩm mới</span>
            </a>
            
            <a href="{{ route('admin.inventory.imports.create') }}" class="flex flex-col items-center justify-center p-6 bg-green-50 hover:bg-green-100 rounded-lg transition">
                <i class="fas fa-warehouse text-4xl text-green-600 mb-3"></i>
                <span class="text-base font-medium text-gray-700">Nhập hàng mới</span>
            </a>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800">Đơn hàng gần đây</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã đơn</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khách hàng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổng tiền</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày đặt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($recent_orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $order->user->name }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ number_format($order->total, 0, ',', '.') }}đ</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status === 'shipping') bg-purple-100 text-purple-800
                                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                @elseif($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($order->status === 'pending') Chờ xử lý
                                @elseif($order->status === 'confirmed') Đã xác nhận
                                @elseif($order->status === 'shipping') Đang giao hàng
                                @elseif($order->status === 'delivered') Đã giao hàng
                                @elseif($order->status === 'completed') Hoàn thành
                                @elseif($order->status === 'cancelled') Đã hủy
                                @else {{ ucfirst($order->status) }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Chưa có đơn hàng nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
