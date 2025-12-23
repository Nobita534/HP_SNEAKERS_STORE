@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa sản phẩm - Admin')
@section('page-title', 'Chỉnh sửa sản phẩm')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tên sản phẩm -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tên sản phẩm <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
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
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                        <option value="Nike" {{ old('brand', $product->brand) == 'Nike' ? 'selected' : '' }}>Nike</option>
                        <option value="Adidas" {{ old('brand', $product->brand) == 'Adidas' ? 'selected' : '' }}>Adidas</option>
                        <option value="Puma" {{ old('brand', $product->brand) == 'Puma' ? 'selected' : '' }}>Puma</option>
                        <option value="Converse" {{ old('brand', $product->brand) == 'Converse' ? 'selected' : '' }}>Converse</option>
                        <option value="Vans" {{ old('brand', $product->brand) == 'Vans' ? 'selected' : '' }}>Vans</option>
                    </select>
                    @error('brand')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Giá gốc -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Giá gốc (VNĐ) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0" step="1000"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <br>
            <!-- Kích thước và Số lượng -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Kích thước và Số lượng <span class="text-red-500">*</span>
                </label>
                
                <div id="sizes-container" class="space-y-3">
                    @forelse($product->productSizes as $index => $productSize)
                    <div class="size-row flex gap-3">
                        <div class="flex-1">
                            <input type="text" name="sizes[{{ $index }}][size]" 
                                   value="{{ $productSize->size }}"
                                   placeholder="Kích thước (VD: 38, 39, 40)" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="flex-1">
                            <input type="number" name="sizes[{{ $index }}][quantity]" 
                                   value="{{ $productSize->quantity }}"
                                   placeholder="Số lượng" 
                                   min="0" 
                                   readonly
                                   data-quantity="{{ $productSize->quantity }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <button type="button" class="remove-size px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition {{ $productSize->quantity > 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $productSize->quantity > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    @empty
                    <div class="size-row flex gap-3">
                        <div class="flex-1">
                            <input type="text" name="sizes[0][size]" placeholder="Kích thước (VD: 38, 39, 40)" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="flex-1">
                            <input type="number" name="sizes[0][quantity]" 
                                   value="0"
                                   placeholder="Số lượng" 
                                   min="0" 
                                   readonly
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <button type="button" class="remove-size px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition opacity-50 cursor-not-allowed" disabled>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    @endforelse
                </div>
                
                <button type="button" id="add-size" class="mt-3 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                    <i class="fas fa-plus mr-2"></i>Thêm kích thước
                </button>
                @error('sizes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <!-- Trạng thái -->
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Trạng thái
                    </label>
                    <select name="is_featured"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="0" {{ old('is_featured', $product->is_featured) == 0 ? 'selected' : '' }}>Bình thường</option>
                        <option value="1" {{ old('is_featured', $product->is_featured) == 1 ? 'selected' : '' }}>Nổi bật</option>
                    </select>
                </div>

                <!-- Ảnh hiện tại -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ảnh hiện tại
                    </label>
                    @if($product->image)
                        <div class="mb-3">
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg border">
                        </div>
                    @endif
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Thay đổi hình ảnh (tùy chọn)
                    </label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('image') border-red-500 @enderror">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-1">Để trống nếu không muốn thay đổi ảnh. Chọn file ảnh mới (JPG, PNG, JPEG) để cập nhật.</p>
                </div>

                <!-- Mô tả -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mô tả sản phẩm
                    </label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                              placeholder="Nhập mô tả chi tiết về sản phẩm...">{{ old('description', $product->description) }}</textarea>
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
                    Cập nhật sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let sizeIndex = {{ $product->productSizes->count() }};

// Add new size row
document.getElementById('add-size').addEventListener('click', function() {
    const container = document.getElementById('sizes-container');
    const newRow = document.createElement('div');
    newRow.className = 'size-row flex gap-3';
    newRow.innerHTML = `
        <div class="flex-1">
            <input type="text" name="sizes[${sizeIndex}][size]" placeholder="Kích thước (VD: 38, 39, 40)" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div class="flex-1">
            <input type="number" name="sizes[${sizeIndex}][quantity]" 
                   value="0"
                   placeholder="Số lượng" 
                   min="0" 
                   readonly
                   data-quantity="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-blue-500" required>
        </div>
        <button type="button" class="remove-size px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newRow);
    sizeIndex++;
    updateRemoveButtons();
});

// Remove size row
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-size')) {
        const btn = e.target.closest('.remove-size');
        if (btn.disabled) return; // Don't remove if button is disabled
        
        const row = e.target.closest('.size-row');
        const quantityInput = row.querySelector('input[name*="[quantity]"]');
        const quantity = parseInt(quantityInput.value) || 0;
        
        // Only allow deletion if quantity is 0
        if (quantity === 0 && document.querySelectorAll('.size-row').length > 1) {
            row.remove();
            updateRemoveButtons();
        }
    }
});

// Update remove buttons state
function updateRemoveButtons() {
    const rows = document.querySelectorAll('.size-row');
    rows.forEach((row, index) => {
        const btn = row.querySelector('.remove-size');
        const quantityInput = row.querySelector('input[name*="[quantity]"]');
        const quantity = parseInt(quantityInput.value) || 0;
        
        // Disable if only 1 row OR if quantity > 0
        if (rows.length === 1 || quantity > 0) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.classList.remove('hover:bg-red-600');
        } else {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.classList.add('hover:bg-red-600');
        }
    });
}

// Initial state
updateRemoveButtons();
</script>
@endsection
