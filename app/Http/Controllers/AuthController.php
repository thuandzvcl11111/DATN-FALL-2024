<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;
class AuthController extends Controller
{
    // Đăng ký

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // Ensure "confirmed" works with password_confirmation field
            'phone' => 'nullable|string|max:20',
            'gmail' => 'nullable|string|email|max:255|unique:users,gmail',
            'address' => 'nullable|string|max:255',
        ]);
    
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'gmail' => $validated['gmail'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => 'customer',  // Default role
        ]);
    
        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }


    // Đăng nhập
    public function login(Request $request)
    {
        // Xử lý đăng nhập bằng Google
        if ($request->has('google_token')) {
            $googleToken = $request->input('google_token');
    
            // Xác thực Google ID Token
            $client = new GoogleClient([
                'client_id' => env('GOOGLE_CLIENT_ID'), // Lấy từ .env
            ]);
            
            $payload = $client->verifyIdToken($googleToken);
    
            if (!$payload) {
                return response()->json(['message' => 'Invalid Google token'], 401);
            }
    
            // Lấy email và tên từ Google
            $email = $payload['email'];
            $name = $payload['name'] ?? 'Unknown User'; // Đề phòng trường hợp không có tên
    
            // Tìm hoặc tạo người dùng dựa trên email
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => bcrypt(uniqid()), // Đặt mật khẩu ngẫu nhiên vì Google login không cần mật khẩu
                ]
            );
    
            // Tạo token cho người dùng
            $token = $user->createToken('API Token')->plainTextToken;
    
            return response()->json([
                'token' => $token,
                'user' => $user,
            ], 200);
        }
    
        // Xử lý đăng nhập bằng email/password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }
    
        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;
    
        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        // Xóa token của người dùng hiện tại
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
    public function getAllUsers()
    {
        $user = auth()->user();
        $users = User::all(); // Lấy tất cả người dùng
        return response()->json( $users, 200);
    }

    // Hiển thị chi tiết người dùng
    public function getUserDetail($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }
    // thay đổi mật khẩu
        public function resetPassword(Request $request)
        {
            // Validate thông tin cần thiết
            $request->validate([
                'token' => 'required',
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            Log::info('Reset Password Request', [
                'email' => $request->email,
                'token' => $request->token,
            ]);

            // Đặt lại mật khẩu
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();
                }
            );

            Log::info('Reset Password Status', ['status' => $status]);

            return $status === Password::PASSWORD_RESET
                ? response()->json(['message' => __($status)], 200)
                : response()->json(['message' => __($status)], 500);
        }
    // qiên mật khẩu
    public function sendResetLinkEmail(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Gửi email reset password
        $status = Password::sendResetLink($request->only('email'));
        
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 500);
    }
    public function delete_user($id)
    {
        $user = auth()->user();
        $users_delete = User::find($id);
        if (!$users_delete) {
            return response()->json(['message' => 'user not found'], 404);
        }

        $users_delete->delete();
        return response()->json(['message' => 'user deleted successfully']);
    }
}
