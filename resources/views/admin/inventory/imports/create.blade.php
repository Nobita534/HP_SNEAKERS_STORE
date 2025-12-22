@extends('admin.layouts.app')

@section('title', 'Nhập hàng mới')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Nhập hàng mới</h1>
        <a href="{{ route('admin.inventory.imports.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
            <strong class="font-semibold">Có lỗi xảy ra:</strong>
            <ul class="mt-2 ml-4 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.inventory.imports.store') }}" method="POST" id="importForm">
        @csrf
        
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Thông tin nhập hàng</h3>
            </div>
            <div class="p-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                    <textarea name="note" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="2" placeholder="Nhập ghi chú (không bắt buộc)">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Danh sách sản phẩm nhập</h3>
                <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm inline-flex items-center" onclick="addItem()">
                    <i class="fas fa-plus mr-2"></i> Thêm sản phẩm
                </button>
            </div>
            <div class="p-6">
                <div id="items-container" class="space-y-4">
                    <!-- Items will be added here -->
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="w-64 space-y-2">
                            <div class="flex justify-between text-gray-700">
                                <span class="font-medium">Tổng số lượng:</span>
                                <span id="total-quantity" class="font-semibold">0</span>
                            </div>
                            <div class="flex justify-between text-lg">
                                <span class="font-semibold text-gray-800">Tổng tiền:</span>
                                <span id="total-amount" class="font-bold text-green-600">0đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition font-semibold inline-flex items-center">
                <i class="fas fa-save mr-2"></i> Lưu phiếu nhập
            </button>
            <a href="{{ route('admin.inventory.imports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition font-semibold inline-flex items-center">
                <i class="fas fa-times mr-2"></i> Hủy
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemIndex = 0;
const products = @json($products);

function addItem() {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="item-row p-4 bg-gray-50 border border-gray-200 rounded-lg" data-index="${itemIndex}">
            <div class="grid grid-cols-12 gap-4 items-end">
                <div class="col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sản phẩm</label>
                    <select name="items[${itemIndex}][product_id]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required onchange="loadSizes(this, ${itemIndex})">
                        <option value="">-- Chọn sản phẩm --</option>
                        ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                    <select name="items[${itemIndex}][size]" class="size-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required disabled>
                        <option value="">-- Chọn size --</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                    <input type="number" name="items[${itemIndex}][quantity]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="1" value="1" required onchange="calculateRow(${itemIndex})">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá nhập (đ)</label>
                    <input type="number" name="items[${itemIndex}][import_price]" class="price-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="0" step="1000" required onchange="calculateRow(${itemIndex})">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thành tiền</label>
                    <input type="text" class="subtotal-display w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg" readonly value="0đ">
                </div>
                <div class="col-span-1">
                    <button type="button" class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition" onclick="removeItem(${itemIndex})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="mt-2">
                <small class="stock-info text-gray-600"></small>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

function loadSizes(select, index) {
    const productId = select.value;
    const row = select.closest('.item-row');
    const sizeSelect = row.querySelector('.size-select');
    const stockInfo = row.querySelector('.stock-info');
    
    sizeSelect.disabled = true;
    sizeSelect.innerHTML = '<option value="">-- Chọn size --</option>';
    stockInfo.textContent = '';
    
    if (!productId) return;
    
    const product = products.find(p => p.id == productId);
    
    if (product && product.product_sizes && product.product_sizes.length > 0) {
        product.product_sizes.forEach(size => {
            const option = document.createElement('option');
            option.value = size.size;
            option.textContent = `${size.size} (Tồn: ${size.quantity})`;
            sizeSelect.appendChild(option);
        });
        sizeSelect.disabled = false;
    } else {
        // Nếu chưa có size, cho phép nhập size mới
        const commonSizes = [35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45];
        commonSizes.forEach(size => {
            const option = document.createElement('option');
            option.value = size;
            option.textContent = size;
            sizeSelect.appendChild(option);
        });
        sizeSelect.disabled = false;
        stockInfo.textContent = 'Sản phẩm chưa có size, sẽ tạo mới';
        stockInfo.classList.add('text-blue-600');
    }
}

function calculateRow(index) {
    const row = document.querySelector(`[data-index="${index}"]`);
    const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const subtotal = quantity * price;
    
    row.querySelector('.subtotal-display').value = formatCurrency(subtotal);
    calculateTotal();
}

function calculateTotal() {
    let totalQuantity = 0;
    let totalAmount = 0;
    
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        totalQuantity += quantity;
        totalAmount += quantity * price;
    });
    
    document.getElementById('total-quantity').textContent = totalQuantity;
    document.getElementById('total-amount').textContent = formatCurrency(totalAmount);
}

function removeItem(index) {
    const row = document.querySelector(`[data-index="${index}"]`);
    row.remove();
    calculateTotal();
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND' 
    }).format(amount);
}

// Thêm 1 item mặc định khi load trang
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});

// Validate form trước khi submit
document.getElementById('importForm').addEventListener('submit', function(e) {
    const items = document.querySelectorAll('.item-row');
    if (items.length === 0) {
        e.preventDefault();
        alert('Vui lòng thêm ít nhất một sản phẩm!');
        return false;
    }
});
</script>
@endpush
@endsection
