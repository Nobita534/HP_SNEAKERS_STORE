<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the user profile
     */
    public function show()
    {
        $user = Auth::user()->load('addresses', 'orders');
        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the profile
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Show the form for changing password
     */
    public function editPassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update the user password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')->with('success', 'Đổi mật khẩu thành công!');
    }

    /**
     * Store a new address
     */
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'address' => 'required|string',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // If this is default, remove default from other addresses
        if ($request->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create($validated);

        return redirect()->route('profile.show')->with('success', 'Thêm địa chỉ thành công!');
    }

    /**
     * Get address details
     */
    public function getAddress($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        return response()->json($address);
    }

    /**
     * Update an address
     */
    public function updateAddress(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'address' => 'required|string',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);

        // If this is default, remove default from other addresses
        if ($request->is_default) {
            $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()->route('profile.show')->with('success', 'Cập nhật địa chỉ thành công!');
    }

    /**
     * Delete an address
     */
    public function deleteAddress($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        
        if ($address->is_default) {
            return redirect()->route('profile.show')->with('error', 'Không thể xóa địa chỉ mặc định!');
        }

        $address->delete();

        return redirect()->route('profile.show')->with('success', 'Xóa địa chỉ thành công!');
    }

    /**
     * Set an address as default
     */
    public function setDefaultAddress($id)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);

        // Remove default from all addresses
        $user->addresses()->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        return redirect()->route('profile.show')->with('success', 'Đã đặt làm địa chỉ mặc định!');
    }
}
