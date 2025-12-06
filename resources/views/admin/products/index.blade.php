@extends('admin.layouts.app')

@section('title', 'Quản lý sản phẩm - Admin')
@section('page-title', 'Quản lý sản phẩm')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-gray-700">Danh sách sản phẩm</h3>
    </div>
    <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition">
        <i class="fas fa-plus mr-2"></i>
        Thêm sản phẩm mới
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên sản phẩm</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Danh mục</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giá</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tồn kho</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $product->id }}</td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-gray-800">{{ $product->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ number_format($product->price, 0, ',', '.') }}đ</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $product->stock }}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Xóa sản phẩm này?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        Chưa có sản phẩm nào
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($products->hasPages())
    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endif
@endsection
