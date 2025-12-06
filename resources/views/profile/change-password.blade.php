@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>

        <h1 class="text-3xl font-bold mb-6">Đổi mật khẩu</h1>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <form action="{{ route('profile.update-password') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="current_password" class="block text-gray-700 font-medium mb-2">
                        Mật khẩu hiện tại <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="current_password" name="current_password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('current_password') border-red-500 @enderror"
                        required>
                    @error('current_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium mb-2">
                        Mật khẩu mới <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                        required>
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-sm mt-1">Mật khẩu phải có ít nhất 8 ký tự</p>
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">
                        Xác nhận mật khẩu mới <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('profile.show') }}" 
                        class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                        Hủy
                    </a>
                    <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-key mr-2"></i>Đổi mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
