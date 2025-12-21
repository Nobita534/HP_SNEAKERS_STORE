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

        <!-- Address Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Địa chỉ giao hàng</h2>
                    <button onclick="openAddressModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Thêm địa chỉ
                    </button>
                </div>
                
                @if($user->addresses && $user->addresses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($user->addresses as $address)
                    <div class="border rounded-lg p-4 hover:border-blue-500 transition {{ $address->is_default ? 'border-blue-500 bg-blue-50' : '' }}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <p class="font-semibold">{{ $address->name }}</p>
                                    @if($address->is_default)
                                    <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded">Mặc định</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                                <p class="text-sm text-gray-600 mt-2">{{ $address->address }}</p>
                                <p class="text-sm text-gray-600">{{ $address->ward }}, {{ $address->district }}, {{ $address->city }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-3 pt-3 border-t">
                            <button onclick="editAddress({{ $address->id }})" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-edit mr-1"></i>Sửa
                            </button>
                            @if(!$address->is_default)
                            <button onclick="setDefaultAddress({{ $address->id }})" class="text-green-600 hover:text-green-800 text-sm">
                                <i class="fas fa-check mr-1"></i>Đặt mặc định
                            </button>
                            <button onclick="deleteAddress({{ $address->id }})" class="text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash mr-1"></i>Xóa
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-600 text-center py-8">Bạn chưa có địa chỉ giao hàng nào</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div id="address-modal" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1);">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Thêm địa chỉ mới</h3>
                <button onclick="closeAddressModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="address-form" method="POST" action="{{ route('profile.addresses.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST" id="form-method">
                <input type="hidden" name="address_id" id="address-id">
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên người nhận <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" id="phone" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành phố <span class="text-red-500">*</span></label>
                            <select name="city" id="city" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Đang tải...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện</label>
                            <select name="district" id="district" disabled
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phường/Xã</label>
                            <select name="ward" id="ward" disabled
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ chi tiết <span class="text-red-500">*</span></label>
                        <textarea name="address" id="address" rows="3" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Số nhà, tên đường..."></textarea>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_default" id="is-default" value="1"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is-default" class="ml-2 text-sm text-gray-700">Đặt làm địa chỉ mặc định</label>
                    </div>
                </div>
                
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeAddressModal()" 
                            class="flex-1 px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Hủy
                    </button>
                    <button type="submit" 
                            class="flex-1 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                        Lưu địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// API địa chỉ Việt Nam
let provinces = [];

document.addEventListener('DOMContentLoaded', function() {
    loadProvinces();
    setupEventListeners();
});

async function loadProvinces() {
    try {
        const response = await fetch('https://provinces.open-api.vn/api/p/');
        provinces = await response.json();
        
        const citySelect = document.getElementById('city');
        citySelect.innerHTML = '<option value="">Chọn Tỉnh/Thành phố</option>';
        provinces.forEach(province => {
            citySelect.innerHTML += `<option value="${province.name}" data-code="${province.code}">${province.name}</option>`;
        });
    } catch (error) {
        console.error('Lỗi tải danh sách tỉnh/thành:', error);
        document.getElementById('city').innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
    }
}

async function loadDistricts(provinceCode) {
    try {
        const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
        const data = await response.json();
        
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');
        
        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        wardSelect.disabled = true;
        
        data.districts.forEach(district => {
            districtSelect.innerHTML += `<option value="${district.name}" data-code="${district.code}">${district.name}</option>`;
        });
        
        districtSelect.disabled = false;
    } catch (error) {
        console.error('Lỗi tải danh sách quận/huyện:', error);
    }
}

async function loadWards(districtCode) {
    try {
        const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
        const data = await response.json();
        
        const wardSelect = document.getElementById('ward');
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        
        data.wards.forEach(ward => {
            wardSelect.innerHTML += `<option value="${ward.name}">${ward.name}</option>`;
        });
        
        wardSelect.disabled = false;
    } catch (error) {
        console.error('Lỗi tải danh sách phường/xã:', error);
    }
}

function setupEventListeners() {
    const citySelect = document.getElementById('city');
    const districtSelect = document.getElementById('district');
    
    citySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const provinceCode = selectedOption.getAttribute('data-code');
        
        if (provinceCode) {
            loadDistricts(provinceCode);
        } else {
            document.getElementById('district').innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            document.getElementById('ward').innerHTML = '<option value="">Chọn Phường/Xã</option>';
            document.getElementById('district').disabled = true;
            document.getElementById('ward').disabled = true;
        }
    });
    
    districtSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const districtCode = selectedOption.getAttribute('data-code');
        
        if (districtCode) {
            loadWards(districtCode);
        } else {
            document.getElementById('ward').innerHTML = '<option value="">Chọn Phường/Xã</option>';
            document.getElementById('ward').disabled = true;
        }
    });
}

function openAddressModal() {
    document.getElementById('address-modal').classList.remove('hidden');
    document.getElementById('address-form').reset();
    document.getElementById('form-method').value = 'POST';
    document.getElementById('address-form').action = '{{ route("profile.addresses.store") }}';
    document.querySelector('#address-modal h3').textContent = 'Thêm địa chỉ mới';
    
    document.getElementById('district').disabled = true;
    document.getElementById('ward').disabled = true;
}

function closeAddressModal() {
    document.getElementById('address-modal').classList.add('hidden');
}

async function editAddress(id) {
    try {
        const response = await fetch(`/profile/addresses/${id}`);
        const data = await response.json();
        
        document.getElementById('address-modal').classList.remove('hidden');
        document.getElementById('form-method').value = 'PUT';
        document.getElementById('address-id').value = id;
        document.getElementById('address-form').action = `/profile/addresses/${id}`;
        document.querySelector('#address-modal h3').textContent = 'Chỉnh sửa địa chỉ';
        
        document.getElementById('name').value = data.name;
        document.getElementById('phone').value = data.phone;
        document.getElementById('address').value = data.address;
        document.getElementById('is-default').checked = data.is_default;
        
        // Chờ provinces load xong
        if (provinces.length === 0) {
            await new Promise(resolve => setTimeout(resolve, 1000));
        }
        
        // Set city
        const citySelect = document.getElementById('city');
        for (let i = 0; i < citySelect.options.length; i++) {
            if (citySelect.options[i].value === data.city) {
                citySelect.selectedIndex = i;
                const provinceCode = citySelect.options[i].getAttribute('data-code');
                
                // Load districts
                if (provinceCode) {
                    await loadDistricts(provinceCode);
                    
                    // Set district
                    const districtSelect = document.getElementById('district');
                    for (let j = 0; j < districtSelect.options.length; j++) {
                        if (districtSelect.options[j].value === data.district) {
                            districtSelect.selectedIndex = j;
                            const districtCode = districtSelect.options[j].getAttribute('data-code');
                            
                            // Load wards
                            if (districtCode) {
                                await loadWards(districtCode);
                                
                                // Set ward
                                const wardSelect = document.getElementById('ward');
                                for (let k = 0; k < wardSelect.options.length; k++) {
                                    if (wardSelect.options[k].value === data.ward) {
                                        wardSelect.selectedIndex = k;
                                        break;
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
                break;
            }
        }
    } catch (error) {
        console.error('Lỗi load địa chỉ:', error);
    }
}

function deleteAddress(id) {
    showConfirm(
        'Thông báo',
        'Bạn có chắc muốn xóa địa chỉ này?',
        () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/profile/addresses/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    );
}

function setDefaultAddress(id) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/profile/addresses/${id}/set-default`;
    form.innerHTML = `
        @csrf
        @method('PUT')
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
