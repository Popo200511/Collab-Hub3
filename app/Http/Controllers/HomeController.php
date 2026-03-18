<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  // เพิ่มการใช้ DB ที่นี่

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    // ก่อนเข้าหน้า home 
    public function __construct()
    {
        $this->middleware('auth');

        
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    // ถ้าต้องการเข้า home จะต้องผ่านการเข้ารหัสก่อน
    public function index()
    {
        // ดึงข้อมูลจากตาราง 'users'
        $users = DB::table('users')
            ->orderBy('status')  // หรือใช้ 'status' เป็นคอลัมน์ที่ต้องการจัดเรียง
            ->get();

        // ส่งข้อมูลไปที่ view 'home'
        return view('home', compact('users'));
    }

    // เมธอดสำหรับลบผู้ใช้
    public function destroy($id)
    {
        // ค้นหาผู้ใช้ตาม ID และลบ
        $user = DB::table('users')->where('id', $id)->first();  // หาผู้ใช้ที่มี id ตรง
        if ($user) {
            // ลบผู้ใช้
            DB::table('users')->where('id', $id)->delete();
        }

        // Redirect กลับไปยังหน้าหลักพร้อมกับข้อความ
        return redirect()->route('sda.register')->with('success', 'ลบผู้ใช้สำเร็จ');
    }


    public function updateStatus(Request $request, $userId)
    {
        // ใช้ update() เพื่ออัปเดตข้อมูล
        $updated = DB::table('users')  // ใช้ 'users' แทน 'User' ถ้าตาม convention
                        ->where('id', $userId)
                        ->update(['status' => $request->status]);
    
        if ($updated) {
            return response()->json(['success' => 'สถานะของผู้ใช้ถูกอัปเดตแล้ว']);
        }
    
        return response()->json(['error' => 'ไม่สามารถอัปเดตสถานะได้']);
    }
    

}
