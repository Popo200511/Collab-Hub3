<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function updateImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user(); // ใช้ object ตรง ๆ (ปลอดภัยกว่า)

        // ลบรูปเก่า
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // เก็บรูปใหม่
        $path = $request->file('profile_image')
            ->store('profile_images', 'public');

        // update DB
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'profile_image' => $path,
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'เปลี่ยนรูปโปรไฟล์เรียบร้อย');
    }
}