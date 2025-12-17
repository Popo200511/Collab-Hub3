<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStatus
{
    public function handle(Request $request, Closure $next)
    {
        // เช็คว่าผู้ใช้ล็อกอินหรือไม่
        if (!Auth::check()) {
            // ถ้ายังไม่ได้ล็อกอิน จะให้ redirect ไปหน้า login
            return redirect('/login')->with('error', 'กรุณาล็อกอิน');
        }

        // เช็คว่าผู้ใช้มี status เป็น 1, 2, 3, หรือ 4
        if (!in_array(Auth::user()->status, [1, 2, 3, 4])) {
            return redirect('/home')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        // ถ้าผู้ใช้ผ่านเงื่อนไข ให้ส่ง request ต่อไป
        return $next($request);
    }
}