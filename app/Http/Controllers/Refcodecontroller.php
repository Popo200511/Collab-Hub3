<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Refcodecontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
{
    if ($request->has('export')) {
        $rows = DB::table('r_import_refcode')->get();
        $filePath = storage_path('app/refcode.csv');
        $file = fopen($filePath, 'w');

        // 🔥 ใส่ BOM เพื่อป้องกันภาษาไทยเพี้ยน
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // เขียนหัวตาราง
        fputcsv($file, ["Refcode", "Sitecode", "Office", "Project"]);

        // เขียนข้อมูลลงไฟล์
        foreach ($rows as $row) {
            fputcsv($file, [
                $row->refcode,
                $row->sitecode,
                $row->office,
                $row->project
            ]);
        }

        fclose($file);
        return response()->download($filePath);
    }

    // ดึงข้อมูล 50 รายการแรกจากฐานข้อมูล
    $refcode = DB::table('r_import_refcode')->limit(50)->get();

    // เช็คจำนวน Refcode
    $count = DB::table('r_import_refcode')->count('refcode');

    return view('refcode.home', compact('refcode', 'count'));
}

	public function searchRefcode(Request $request)
    {
        $refcodeQuery = DB::table('r_import_refcode');

        // ตรวจสอบว่ามีค่าค้นหาในแต่ละช่องหรือไม่
        if ($request->filled('refcode')) {
            $refcodeQuery->where('refcode', 'like', '%' . $request->input('refcode') . '%');
        }
        if ($request->filled('sitecode')) {
            $refcodeQuery->where('sitecode', 'like', '%' . $request->input('sitecode') . '%');
        }
        if ($request->filled('office')) {
            $refcodeQuery->where('office', 'like', '%' . $request->input('office') . '%');
        }
        if ($request->filled('project')) {
            $refcodeQuery->where('project', 'like', '%' . $request->input('project') . '%');
        }

        // ถ้าทุกช่องว่าง ให้ดึง 100 รายการแรก
        if (!$request->hasAny(['refcode', 'sitecode', 'office', 'project'])) {
            $refcodeQuery->limit(50);
        }

        $refcode = $refcodeQuery->get();

        return response()->json($refcode);
    }

    public function importrefcode(Request $request)
    {
        $refcode = DB::table('r_import_refcode')->get();

        $dataToSave = [];
		
		ini_set('max_execution_time', 600); // เพิ่มเป็น 5 นาที (300 วินาที)


        //dd($dataToSave);

        if ($request->isMethod('post')) {
            $request->validate([
                'csv_file_add' => 'required|file|mimes:csv,txt|max:5120',
            ]);
            $file = $request->file('csv_file_add');
            // เปิดไฟล์ CSV
            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                $isFirstRow = true;

                while (($data = fgetcsv($handle, 300000, ',')) !== false) {

                    //dd($data);

                    //เพิ่มมาใหม่
                    if (empty(array_filter($data))) {
                        continue;
                    }

                    // Read only the first two columns (index 0 and 1)
                    if (!$isFirstRow) {
                        $dataToSave[] = [
                            'refcode' => $data[0], // First column
                            'sitecode' => $data[1], // Second column
                            'office' => $data[2], // First column
                            'project' => $data[3], // Second column
                        ];
                    }
                    $isFirstRow = false;
                }

                //dd($dataToSave);

                fclose($handle);
            } else {
                return back()->withErrors(['message' => 'ไม่สามารถเปิดไฟล์ CSV']);
            }
        }

         //dd($refcode, $dataToSave);

        $countDataToSave = count($dataToSave);

       // dd($countDataToSave);


        return view('refcode.import', compact('refcode', 'dataToSave','countDataToSave'));

        }

    //SAVE IMPORT Refcode 
    public function saveAdd(Request $request)
    {
        $dataToSave = json_decode($request->data_add, true);

        // ดึงข้อมูล refcode จากฐานข้อมูล
        $refcode = DB::table('r_import_refcode')->get();


        // เริ่มต้น transaction
        DB::beginTransaction();

        // ตรวจสอบว่า $dataToSave เป็น array และมีข้อมูล
        if (!is_array($dataToSave)) {
            return redirect('/addrefcode')->withErrors(['error' => 'ข้อมูลไม่ถูกต้องหรือไม่มีข้อมูลที่จะบันทึก']);
        }

        // Loop ผ่านแต่ละแถวใน $dataToSave
        $newData = []; // สำหรับเก็บข้อมูลที่ไม่ซ้ำกัน
        foreach ($dataToSave as $row) {
            // เช็คว่า refcode ซ้ำหรือไม่
            $matched = false;
            foreach ($refcode as $item) {
                if ($item->refcode === $row['refcode']) {
                    $matched = true;
                    break;
                }
            }

            // ถ้า refcode ไม่ซ้ำกัน ก็เพิ่มข้อมูลใหม่
            if (!$matched) {
                $newData[] = [
                    'refCode' => $row['refcode'],
                    'sitecode' => $row['sitecode'],
                    'office' => $row['office'], 
                    'project' => $row['project']        
                    // เพิ่มคอลัมน์อื่นๆ ตามที่ต้องการ
                ];
            }
        }

        //dd($newData);

        // ถ้ามีข้อมูลที่ไม่ซ้ำกันให้ทำการบันทึก
        if (count($newData) > 0) {
            // Insert ข้อมูลที่ไม่ซ้ำ
            DB::table('r_import_refcode')->insert($newData);
            // Commit การทำธุรกรรม
            DB::commit();
            return redirect('refcode/home')->with('success', 'บันทึกข้อมูลสำเร็จ');
        } else {
            // ถ้าไม่มีข้อมูลใหม่ให้บันทึก
            DB::rollBack(); // ยกเลิกการทำธุรกรรม
            return redirect('refcode/home')->withErrors(['error' => 'ข้อมูล Refcode ซ้ำกัน']);
        }
    }

}
