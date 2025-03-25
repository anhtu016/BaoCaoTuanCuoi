<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function index()
    {
        // Lấy tất cả người dùng
        return response()->json(User::all(), 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }
    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required',
        'email' => "required|email|unique:users,email,$id", // Không trùng email của người khác
        'password' => 'nullable|min:6'
    ]);

    $user->name = $request->name;
    $user->email = $request->email;
    
    // Chỉ cập nhật mật khẩu nếu có nhập mới
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return response()->json($user, 200);
}
public function destroy($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'Người dùng không tồn tại'], 404);
    }

    $user->delete();

    return response()->json(['message' => 'Xóa người dùng thành công'], 200);
}

}
