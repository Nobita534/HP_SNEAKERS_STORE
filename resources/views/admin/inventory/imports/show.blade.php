@extends('admin.layouts.app')

@section('title', 'Nhập hàng - Admin')
@section('page-title', 'Nhập hàng')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Chi tiết phiếu nhập hàng</h1>
        <a href="{{ route('admin.inventory.imports.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-blue-600 mb-4">Thông tin phiếu nhập</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 font-medium">Mã phiếu:</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold">{{ $importCode }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 font-medium">Ngày nhập:</span>
                    <span class="text-gray-800">{{ $transactions->first()->imported_at->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 font-medium">Người nhập:</span>
                    <span class="text-gray-800">{{ $transactions->first()->user->name ?? 'N/A' }}</span>
                </div>
                @if($transactions->first()->note)
                <div class="flex justify-between">
                    <span class="text-gray-600 font-medium">Ghi chú:</span>
                    <span class="text-gray-800">{{ $transactions->first()->note }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-green-600 mb-4">Tổng quan</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 font-medium">Tổng số lượng:</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold">{{ $totalItems }} đôi</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 font-medium">Tổng số mặt hàng:</span>
                    <span class="text-gray-800">{{ $transactions->count() }} mặt hàng</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">Tổng tiền:</span>
                    <span class="text-green-600 text-2xl font-bold">{{ number_format($totalAmount, 0, ',', '.') }}đ</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Chi tiết sản phẩm nhập</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Giá nhập</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transactions as $index => $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-center text-gray-700">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($transaction->product->image)
                                <img src="{{ asset('images/products/' . $transaction->product->image) }}" 
                                     alt="{{ $transaction->product->name }}" 
                                     class="w-12 h-12 rounded-lg object-cover mr-3">
                                @endif
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $transaction->product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->product->brand }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800">
                                {{ $transaction->size }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center font-semibold text-gray-800">
                            {{ $transaction->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-gray-700">
                            {{ number_format($transaction->import_price, 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-green-600">
                            {{ number_format($transaction->total_cost, 0, ',', '.') }}đ
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-800">TỔNG CỘNG:</td>
                        <td class="px-6 py-4 text-center font-bold text-gray-800">{{ $totalItems }}</td>
                        <td></td>
                        <td class="px-6 py-4 text-right text-2xl font-bold text-green-600">{{ number_format($totalAmount, 0, ',', '.') }}đ</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .bg-blue-600, .bg-gray-500, .bg-gray-600, .topbar, nav {
        display: none !important;
    }
}
</style>
@endpush
@endsection
