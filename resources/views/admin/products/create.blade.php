@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm - Admin')
@section('page-title', 'Thêm sản phẩm mới')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tên sản phẩm -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tên sản phẩm <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Danh mục -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Danh mục <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_id') border-red-500 @enderror">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Thương hiệu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Thương hiệu <span class="text-red-500">*</span>
                    </label>
                    <select name="brand" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('brand') border-red-500 @enderror">
                        <option value="">-- Chọn thương hiệu --</option>
                        <option value="Nike" {{ old('brand') == 'Nike' ? 'selected' : '' }}>Nike</option>
                        <option value="Adidas" {{ old('brand') == 'Adidas' ? 'selected' : '' }}>Adidas</option>
                        <option value="Puma" {{ old('brand') == 'Puma' ? 'selected' : '' }}>Puma</option>
                        <option value="Converse" {{ old('brand') == 'Converse' ? 'selected' : '' }}>Converse</option>
                        <option value="Vans" {{ old('brand') == 'Vans' ? 'selected' : '' }}>Vans</option>
                    </select>
                    @error('brand')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Giá bán -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Giá bán (VNĐ) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="1000"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Trạng thái -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Trạng thái
                    </label>
                    <select name="is_featured"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="0" {{ old('is_featured', 0) == 0 ? 'selected' : '' }}>Bình thường</option>
                        <option value="1" {{ old('is_featured') == 1 ? 'selected' : '' }}>Nổi bật</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Hình ảnh sản phẩm <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="image" required accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('image') border-red-500 @enderror">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-1">Chọn file ảnh (JPG, PNG, JPEG). Ảnh sẽ được lưu vào thư mục theo danh mục sản phẩm.</p>
                </div>

                <!-- Mô tả -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mô tả sản phẩm
                    </label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                              placeholder="Nhập mô tả chi tiết về sản phẩm...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('admin.products.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Hủy
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>
                    Lưu sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
