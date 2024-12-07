<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Kiểm tra nếu người dùng không có quyền truy cập
        if (Auth::check() && Auth::user()->role !== $role) {
            return response()->json(['message' => 'Forbidden'], 403); // Trả về lỗi 403 nếu không có quyền
        }

        return $next($request); // Cho phép tiếp tục nếu có quyền
    }
}
