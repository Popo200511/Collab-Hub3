<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;



use function Laravel\Prompts\table;

class ImportItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('inventory.import_item');
    }

    //หน้านำของเข้า

    public function checkRefcode(Request $request)
    {
        $refcode = DB::table('w_refcode_data')->where('Refcode', $request->input('refcode'))->first();

        if ($refcode) {
            // Return JSON response with 'exists' and 'description'
            return response()->json(['exists' => true, 'description' => $refcode->description]);
        } else {
            return response()->json(['exists' => false]);
        }
    }

    public function material()
    {
        // Fetch all rows from the 'material_code' table
        $material = DB::table('w_material_code')->get();

        // Fetch all rows from the 'import_add' table
        $import_add = DB::table('w_import_add')
            ->orderByDesc('id')
            ->get();

        //echo $material;

        // Fetch all rows from the 'droppoint_import' table
        $droppoint = DB::table('w_droppoint_import')->get();

        //dd($import_add);

        // Return the 'import_item' view and pass the fetched data to the view
        return view('inventory.import_item', compact('material', 'import_add', 'droppoint'));
    }

    public function additem(Request $request)
    {

        $user = Auth::user()->name; // ดึงชื่อผู้ใช้จาก Auth

        //dd($user);

        // รับข้อมูลจาก request
        $materialCodes = $request->materialCode ?? [];
        $refcodenames = $request->refcodename ?? [];
        $droppoint = $request->droppoint ?? [];
        $date = $request->date ?? [];

        // ตรวจสอบจำนวนสูงสุดของ materialCode, refcodename, droppoint, และ date
        $maxCount = max(count($materialCodes), count($refcodenames), count($droppoint), count($date));

        // เติม refcodenames, droppoint และ date ให้มีจำนวนเท่ากันกับ materialCodes
        $refcodenames = array_pad($refcodenames, $maxCount, end($refcodenames));
        $droppoint = array_pad($droppoint, $maxCount, end($droppoint));
        $date = array_pad($date, $maxCount, end($date));

        // ตรวจสอบค่า session 'transaction_counter', ถ้าไม่มีให้ตั้งค่าเป็น 1
        $counter = session('transaction_counter', 1); // เริ่มต้นจาก 1 หากไม่มีค่าใน session

        // เพิ่มค่า counter ทุกครั้งที่บันทึก
        $counter++; // เพิ่มค่า counter ก่อนที่จะใช้ในการบันทึก

        // บันทึกค่า counter ใหม่ลงใน session
        session(['transaction_counter' => $counter]); // เก็บค่า counter ใหม่ใน session

        // สร้างอาเรย์ของข้อมูลที่ต้องการแทรก
        $data = [];
        $sum = [];

        $currentDate = $date[0];
        $id = "IN";

        for ($i = 0; $i < $maxCount; $i++) {

            // คำนวณ transaction ID
            $lastTransactionId = DB::table('w_import_add')
                ->whereDate('date', $date[$i]) // ตรวจสอบจากวันที่ปัจจุบัน
                ->orderBy('transaction', 'desc')
                ->value('transaction');

            if ($lastTransactionId) {
                $lastIdNumber = (int) substr($lastTransactionId, -3); // ดึงเฉพาะเลขท้าย 3 หลัก
                $newIdNumber = $lastIdNumber + 1;
            } else {
                $newIdNumber = 1; // ถ้าไม่พบ Transaction ID ให้เริ่มจาก 1
            }

            // สร้าง Transaction ID ใหม่ 
            $newTransactionId = $id . str_replace('-', '', $currentDate) . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);

            // คำนวณ available ทันที
            $quantity = $request->Amout[$i] ?? 0;
            $withdraw = $quantities_with[$i] ?? null ?? 0;
            $available = $quantity - $withdraw; // คำนวณจาก quantity และ maxCount
            $confirm = false;

            // ตรวจสอบว่ามีข้อมูลซ้ำใน `$data[]` หรือไม่
            $existingIndex = null;

            foreach ($data as $key => $item) {
                if (
                    $item['refcode_import'] === ($refcodenames[$i] ?? null) &&
                    $item['droppoint_import'] === ($droppoint[$i] ?? null) &&
                    $item['material_code_import'] === ($materialCodes[$i] ?? null) &&
                    $item['material_name_import'] === ($request->materialName[$i] ?? null) &&
                    $item['spec_size_import'] === ($request->specSize[$i] ?? null)
                ) {
                    $existingIndex = $key;
                    break;
                }
            }

            if ($existingIndex !== null) {
                // ถ้าข้อมูลซ้ำ ให้รวมค่า `quantity` และเพิ่มค่า `import_quantity` เพียงครั้งเดียว
                $data[$existingIndex]['quantity'] += $quantity;
            } else {


                // เตรียมข้อมูลสำหรับ insert ลงใน import_add
                $data[] = [
                    'refcode_import' => $refcodenames[$i],
                    'droppoint_import' => $droppoint[$i],
                    'material_code_import' => $materialCodes[$i],
                    'material_name_import' => $request->materialName[$i] ?? '',
                    'spec_size_import' => $request->specSize[$i] ?? '',
                    'brand' => $request->brand[$i] ?? '',
                    'unit' => $request->unit[$i] ?? '',
                    'quantity' => $quantity,
                    'remark' => $request->Remark[$i] ?? '',
                    'date' => $date[$i],
                    'transaction' => $newTransactionId,
                    'import_quantity' => 0, // ตั้งค่าจำนวนทั้งหมด
                    'name' => $user, // เพิ่มชื่อผู้ใช้งาน
                    'confirm' => $confirm
                ];

                $region[] = [
                    'refcode' => $refcodenames[$i],
                    'droppoint' => $droppoint[$i],
                    'material_code' => $materialCodes[$i],
                    'material_name' => $request->materialName[$i] ?? '',
                    'spec_size' => $request->specSize[$i] ?? '',
                    'brand' => $request->brand[$i] ?? '',
                    'unit' => $request->unit[$i] ?? '',
                    'quantity' => $quantity,
                    'remark' => $request->Remark[$i] ?? '',
                    'date' => $date[$i],
                    'transaction' => $newTransactionId,
                    'import_quantity' => 0, // ตั้งค่าจำนวนทั้งหมด
                    'name' => $user // เพิ่มชื่อผู้ใช้งาน
                ];
            }
            // เตรียมข้อมูลสำหรับตรวจสอบและอัปเดตใน sum
            $sum[] = [
                'refcode' => $refcodenames[$i],
                'droppoint' => $droppoint[$i],
                'material_code' => $materialCodes[$i],
                'material_name' => $request->materialName[$i] ?? '',
                'spec' => $request->specSize[$i] ?? '',
                'unit' => $request->unit[$i] ?? '',
                'quantity' => $quantity,
                'withdraw' => $withdraw,
                'available' => $available // ใส่ available ที่คำนวณไว้แล้ว
            ];
        }

        // ตั้งค่า import_quantity = จำนวนรายการที่เหลือใน $data และ $region
        $importQuantity = count($data);

        foreach ($data as &$item) {
            $item['import_quantity'] = $importQuantity;
        }

        foreach ($region as &$item) {
            $item['import_quantity'] = $importQuantity;
        }

        //dd($data, $region);

        //dd($data,$lastTransactionId,$lastIdNumber,$newIdNumber );

        //dd($materialCodes, $refcodenames, $droppoint, $date);

        // Insert data ลงใน table import_add
        DB::table('w_import_add')->insert($data);
        DB::table('w_region')->insert($region);

        // ตรวจสอบและอัปเดตข้อมูลใน table sum
        foreach ($sum as $sumItem) {
            $existingSum = DB::table('w_sum')
                ->where('refcode', $sumItem['refcode'])
                ->where('droppoint', $sumItem['droppoint'])
                ->where('material_code', $sumItem['material_code'])
                ->where('material_name', $sumItem['material_name'])
                ->where('spec', $sumItem['spec'])
                ->first();

            if ($existingSum) {
                // ถ้ามีข้อมูลตรงกัน ให้อัปเดต quantity และ available
                $newQuantity = $existingSum->quantity + $sumItem['quantity'];
                $newAvailable = $existingSum->available + $sumItem['available']; // เพิ่ม available ใหม่

                DB::table('w_sum')
                    ->where('id', $existingSum->id)
                    ->update([
                        'quantity' => $newQuantity,
                        'available' => $newAvailable
                    ]);
            } else {
                // ถ้าไม่มีข้อมูลตรงกัน ให้แทรกใหม่
                DB::table('w_sum')->insert($sumItem);
            }
        }

        return redirect('import')->with('success', 'ทำรายการสำเร็จ !');
    }

    //หน้า Refcode

    //import  Refcode
    public function import_refcode(Request $request)
    {

        $dataToSave = [];

        // ดึงข้อมูล 20 แถวจากตาราง refcode_data
        $refcode = DB::table('w_refcode_data')
            ->orderBy('id', 'desc')
            ->get();
        // ->paginate(1000);

        // dd($refcode);

        if ($request->isMethod('post')) {
            $request->validate([
                'csv_file_add' => 'required|file|mimes:csv,txt|max:2048',
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
                            'description' => $data[1], // Second column
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
        // Pass both $refcode and $dataToSave to the view
        return view('inventory.refcode', compact('refcode', 'dataToSave'));
    }

    //SAVE IMPORT Refcode 
    public function saveAdd(Request $request)
    {
        $dataToSave = json_decode($request->data_add, true);

        // ดึงข้อมูล refcode จากฐานข้อมูล
        $refcode = DB::table('w_refcode_data')->get();


        // เริ่มต้น transaction
        DB::beginTransaction();

        // ตรวจสอบว่า $dataToSave เป็น array และมีข้อมูล
        if (!is_array($dataToSave)) {
            return redirect('addrefcode')->withErrors(['error' => 'ข้อมูลไม่ถูกต้องหรือไม่มีข้อมูลที่จะบันทึก']);
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
                    'description' => $row['description'],
                    // เพิ่มคอลัมน์อื่นๆ ตามที่ต้องการ
                ];
            }
        }

        // ถ้ามีข้อมูลที่ไม่ซ้ำกันให้ทำการบันทึก
        if (count($newData) > 0) {
            // Insert ข้อมูลที่ไม่ซ้ำ
            DB::table('w_refcode_data')->insert($newData);
            // Commit การทำธุรกรรม
            DB::commit();
            return redirect('addrefcode')->with('success', 'บันทึกข้อมูลสำเร็จ');
        } else {
            // ถ้าไม่มีข้อมูลใหม่ให้บันทึก
            DB::rollBack(); // ยกเลิกการทำธุรกรรม
            return redirect('addrefcode')->withErrors(['error' => 'ข้อมูล Refcode ซ้ำกัน']);
        }
    }

    //Add Refcode
    public function addrefcodemanual(Request $request)
    {
        $request->validate([
            'refcode' => 'required',
            'description' => 'required',
        ], [
            'refcode.required' => 'กรุณากรอก Refcode',
            'description.required' => 'กรุณากรอก Description',
        ]);

        //
        $checkRefcode = DB::table('w_refcode_data')
            ->where('refcode', $request->input('refcode'))
            ->first();

        if ($checkRefcode) {
            return redirect()->back()->withErrors(['refcode' => 'Refcode นี้มีอยู่ในระบบอยู่แล้ว'])->withInput();
        }

        $add = [
            'refcode' => $request->input('refcode'), //ดึงค่า Refcode จากที่กรอก request
            'description' => $request->input('description') //ดึงค่า Description จากที่กรอก request
        ];

        //dd($add);

        // เก็บข้อมูลลงฐานข้อมูล
        DB::table('w_refcode_data')->insert($add);

        // Redirect พร้อมข้อความแจ้งเตือนความสำเร็จ
        return redirect()->back()->with('success', 'เพิ่ม Refcode สำเร็จ!');
    }

    //หน้า Material

    //Import Material
    public function import_material(Request $request)
    {

        $dataToSave = [];

        $material = DB::table('w_material_code')
            ->orderBy('id', 'Asc')
            ->get();

        if ($request->isMethod('post')) {
            $request->validate([
                'csv_file_material' => 'required|file|mimes:csv,txt|max:2048',
            ]);
            $file = $request->file('csv_file_material');
            // เปิดไฟล์ CSV
            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                $isFirstRow = true;

                while (($data = fgetcsv($handle, 30000, ',')) !== false) {

                    //Dubug ดูค่าที่อ่านได้จาก CSV

                    //dd($data);

                    //เพิ่มมาใหม่
                    if (empty(array_filter($data))) {
                        continue;
                    }

                    if (!$isFirstRow) {
                        $dataToSave[] = [
                            'material_c' => $data[0],
                            'material_n' => $data[1],
                            'spec_size' => $data[2],
                            'brand' => $data[3],
                            'unit' => $data[4],
                            'price' => $data[5],
                        ]; // เก็บข้อมูลที่ไม่ใช่แถวแรก
                    }
                    $isFirstRow = false;
                }

                //dd($dataToSave);

                fclose($handle);
            } else {
                return back()->withErrors(['message' => 'ไม่สามารถเปิดไฟล์ CSV']);
            }
        }

        // Pass both $refcode and $dataToSave to the view
        return view('inventory.material', compact('material', 'dataToSave'));
    }

    //SAVE IMPORT Savematerial 
    public function savematerial(Request $request)
    {

        $dataToSave = json_decode($request->data_add, true);
        //dd($dataToSave);
        $material = DB::table('w_material_code')->get();
        // Begin the transaction
        DB::beginTransaction();

        // Decode the JSON data from the request
        $dataToSave = json_decode($request->data_add, true);

        // Check if decoding was successful and if data is an array
        if (!is_array($dataToSave)) {
            return redirect('material')->withErrors(['error' => 'ข้อมูลไม่ถูกต้องหรือไม่มีข้อมูลที่จะบันทึก']);
        }

        $newData = []; // สำหรับเก็บข้อมูลที่ไม่ซ้ำกัน

        foreach ($dataToSave as $row) {
            // เช็คว่า Material ซ้ำหรือไม่
            $check = false;
            foreach ($material as $item) {
                if ($item->material_c === $row['material_c']) {
                    $check = true;
                    break;
                }
            }

            //ถ้า Material ไม่ซ้ำกัน จะเพิ่มข้อมูลใหม่
            if (!$check) {
                $newData[] = [
                    'material_c' => $row['material_c'],
                    'material_n' => $row['material_n'],
                    'spec_size' => $row['spec_size'],
                    'brand' => $row['brand'],
                    'unit' => $row['unit'],
                    'price' => $row['price'] !== '' ? $row['price'] : null // ✅ เช็คค่าว่างให้เป็น NULL

                ];
            }
        }
        //dd($newData);

        if (count($newData) > 0) {

            // Insert related data into associated tables using the generated ID
            DB::table('w_material_code')->insert($newData);

            // Commit the transaction
            DB::commit();
            // Redirect back with a success message
            return redirect('material')->with('success', 'บันทึกข้อมูลสำเร็จ');;
        } else {
            // ถ้าไม่มีข้อมูลใหม่ให้บันทึก
            DB::rollBack(); // ยกเลิกการทำธุรกรรม
            return redirect('material')->withErrors(['error' => 'ข้อมูล material ซ้ำกัน']);
        }
    }

    //ADD Material
    public function addmaterialmanual(Request $request)
    {
        $request->validate([
            'material_c' => 'required',
            'material_n' => 'required',
            'spec_size' => 'required',
            /*  'brand' => 'required',
            'unit' => 'required',
            'price' => 'required',  
            */
        ], [
            'material_c.required' => 'กรุณากรอก Material_Code',
            'material_n.required' => 'กรุณากรอก Material_Name',
            'spec_size.required' => 'กรุณากรอก required',
        ]);

        $checkMaterial = DB::table('w_material_code')
            ->where('material_c', $request->input('material_c'))
            ->first();

        if ($checkMaterial) {
            return redirect()->back()->withErrors(['material_c' => 'Material_Code นี้มีอยู่ในระบบอยู่แล้ว'])->withInput();
        }

        $add = [
            'material_c' => $request->input('material_c'),
            'material_n' => $request->input('material_n'),
            'spec_size' => $request->input('spec_size'),
            'brand' => $request->input('brand'),
            'unit' => $request->input('unit'),
            'price' => $request->input('price')
        ];

        //dd($add);

        DB::table('w_material_code')->insert($add);

        return redirect()->back()->with('success', 'เพิ่ม Material สำเร็จ');
        // 
    }
   

    //Droppoint
    public function droppoint(Request $request)
    {

        $droppoint = DB::table('w_droppoint_import')->get();

        //dd($droppoint);
        return view('inventory.droppoint', compact('droppoint'));
    }

    // AddDroppoint
    public function addDroppoint(Request $request)
    {
        // ตรวจสอบข้อมูลที่รับเข้ามา
        $request->validate([
            'droppoint' => 'required',
            'coordinate' => 'required',
            'contact' => 'required',
        ], [
            'droppoint.required' => 'กรุณากรอกจุดส่งสินค้า',
            'coordinate.required' => 'กรุณากรอกพิกัด',
            'contact.required' => 'กรุณากรอกข้อมูลติดต่อ',
        ]);

        // ตรวจสอบว่ามี droppoint ซ้ำในฐานข้อมูลหรือไม่
        $existingDroppoint = DB::table('w_droppoint_import')
            ->where('droppoint', $request->input('droppoint'))
            ->first();

        if ($existingDroppoint) {
            // ส่งข้อความแจ้งเตือนว่า droppoint ซ้ำกัน
            return redirect()->back()->withErrors(['droppoint' => 'Droppoint นี้มีอยู่ในระบบแล้ว'])->withInput();
        }

        // สร้างข้อมูลสำหรับการบันทึก
        $add = [
            'droppoint' => $request->input('droppoint'), // ดึงค่า droppoint จาก request
            'coordinate' => $request->input('coordinate'),  // ดึงค่า coordinate จาก request
            'contact' => $request->input('contact') // ดึงค่า contact จาก request
        ];

        //dd($add);

        // เก็บข้อมูลลงฐานข้อมูล
        DB::table('w_droppoint_import')->insert($add);

        // Redirect พร้อมข้อความแจ้งเตือนความสำเร็จ
        return redirect()->back()->with('success', 'เพิ่ม Droppoint สำเร็จ!');
    }

    public function import_droppoint(Request $request)
    {
        $dataToSave = [];

        $droppoint = DB::table('w_droppoint_import')->get();

        if ($request->isMethod('post')) {
            $request->validate([
                'csv_file_droppoint' => 'required|file|mimes:csv,txt|max:2048',
            ]);
            $file = $request->file('csv_file_droppoint');

            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                $isFirstRow = true;

                while (($data = fgetcsv($handle, 30000, ',')) !== false) {

                    // ข้ามแถวที่เป็นค่าว่าง
                    if (empty(array_filter($data))) {
                        continue;
                    }

                    // เพิ่มข้อมูลลงใน dataToSave
                    if (!$isFirstRow) {
                        // ใช้ trim() เพื่อเอาช่องว่างออกจากชื่อคีย์
                        $dataToSave[] = [
                            'droppoint' => trim($data[0]),  // ลบช่องว่างและแท็บออกจากค่าของ 'droppoint'
                            'coordinate' => trim($data[1]),  // ลบช่องว่างและแท็บออกจากค่าของ 'droppoint'
                            'contact' => trim($data[2]),  // ลบช่องว่างและแท็บออกจากค่าของ 'droppoint'
                        ];
                    }
                    $isFirstRow = false;
                }

                fclose($handle);
            } else {
                return back()->withErrors(['message' => 'ไม่สามารถเปิดไฟล์ CSV']);
            }
        }

        return view('inventory.droppoint', compact('droppoint', 'dataToSave'));
    }

    public function savedroppoint(Request $request)
    {

        $dataToSave = json_decode($request->data_add, true);
        //dd($dataToSave);
        $droppoint = DB::table('w_droppoint_import')->get();
        // Begin the transaction
        DB::beginTransaction();

        // Decode the JSON data from the request
        $dataToSave = json_decode($request->data_add, true);

        // Check if decoding was successful and if data is an array
        if (!is_array($dataToSave)) {
            return redirect('droppoint')->withErrors(['error' => 'ข้อมูลไม่ถูกต้องหรือไม่มีข้อมูลที่จะบันทึก']);
        }

        $newData = []; // สำหรับเก็บข้อมูลที่ไม่ซ้ำกัน

        //dd($dataToSave);

        foreach ($dataToSave as $row) {

            // เช็คว่า Material ซ้ำหรือไม่
            $check = false;
            foreach ($droppoint as $item) {
                if ($item->droppoint === $row['droppoint']) {
                    $check = true;
                    break;
                }
            }

            //dd($row);

            //ถ้า Material ไม่ซ้ำกัน จะเพิ่มข้อมูลใหม่
            if (!$check) {
                $newData[] = [
                    'droppoint' => $row['droppoint'],
                    'coordinate' => $row['coordinate'],
                    'contact' => $row['contact'],
                ];
            }
        }


        if (count($newData) > 0) {


            //dd($newData);

            // Insert related data into associated tables using the generated ID
            DB::table('w_droppoint_import')->insert($newData);

            // Commit the transaction
            DB::commit();
            // Redirect back with a success message
            return redirect('droppoint')->with('success', 'บันทึกข้อมูลสำเร็จ');;
        } else {
            // ถ้าไม่มีข้อมูลใหม่ให้บันทึก
            DB::rollBack(); // ยกเลิกการทำธุรกรรม
            return redirect('droppoint')->withErrors(['error' => 'ข้อมูล Droppoint ซ้ำกัน']);
        }
    }


    //Withdraw
    public function withdraw(Request $request)
    {

        $withdraw = DB::table('w_withdraw')
            ->orderBy('id', 'desc') // เรียงลำดับจากล่าสุดไปเก่าสุด
            ->paginate(10); // แบ่งหน้า 20 แถวต่อหน้า


        //แสดงใน Modal
        $summary = DB::table('w_sum')
            ->join('w_refcode_data', 'w_sum.refcode', '=', 'w_refcode_data.refcode')
            ->select(
                'w_sum.refcode',
                'w_sum.droppoint',
                'w_sum.material_code',
                'w_sum.material_name',
                'w_sum.spec',
                'w_sum.unit',
                'w_sum.quantity',
                'w_sum.available',
                'w_refcode_data.description'
            )
            ->get();

        //dd($summary);

        $droppoint = DB::table('w_droppoint_import')->get();
        $refcode = $request->input('w_refcode');

        //dd($droppoint);
        return view('inventory.withdraw', compact('withdraw', 'droppoint', 'refcode', 'summary'));
    }

    //Check Withdraw
    public function checkImport_add(Request $request)
    {
        // รับค่า refcode จาก request
        $inputRefcode = $request->input('refcode');

        
        // ตรวจสอบว่า refcode มีอยู่ใน refcode_data หรือไม่
        $refcode = DB::table('w_refcode_data')->where('Refcode', $inputRefcode)->first();

        if ($refcode) {
            // ดึงข้อมูลเฉพาะจาก sum ที่เกี่ยวข้องกับ Refcode นั้น
            $imports = DB::table('w_sum')->where('refcode', $inputRefcode)->get();

            // ส่งข้อมูลกลับในรูปแบบ JSON
            return response()->json([
                'exists' => true,
                'description' => $refcode->description,
                'imports' => $imports,
            ]);
        } else {
            // หากไม่พบ Refcode
            return response()->json(['exists' => false]);
        }
    }


    public function addWithdraw(Request $request)
    {

        $user = Auth::user()->name; // ดึงชื่อผู้ใช้จาก Auth

        // รับข้อมูลจาก Request
        $dates = $request->date ?? [];
        $refcodes_before = $request->refcodename ?? [];
        $refcodes_with = $request->refcode_import ?? [];
        $material_codes = $request->material_code_import ?? [];
        $material_names = $request->material_name_import ?? [];
        $droppoints = $request->droppoint ?? [];
        $quantities_with = $request->Amout ?? [];
        $spec = $request->specSize ?? [];
        $unit = $request->unit ?? [];
        $avai = $request->available ?? [];

        $rowCount = count($refcodes_with); // จำนวน rows ทั้งหมด
        $data = [];
        $sum = [];
        $transactionIdBase = 'OUT';

        for ($i = 0; $i < $rowCount; $i++) {
            // ดึงวันที่ของแถวนี้
            $currentDate = $dates[$i] ?? date('Y-m-d');

            // คำนวณ total
            $total = ($avai[$i] ?? 0) - ($quantities_with[$i] ?? 0);

            // ดึง Transaction ID ล่าสุดสำหรับวันที่นี้
            $lastTransactionId = DB::table('w_withdraw')
                ->whereDate('date', $currentDate)
                ->where('transaction_id', 'LIKE', $transactionIdBase . str_replace('-', '', $currentDate) . '%')
                ->orderBy('transaction_id', 'desc')
                ->value('transaction_id');

            // ตรวจสอบและสร้าง Transaction ID ใหม่
            if ($lastTransactionId) {
                $lastIdNumber = (int) substr($lastTransactionId, -3); // เอาเลขท้าย 3 หลัก
                $newIdNumber = $lastIdNumber + 1;
            } else {
                $newIdNumber = 1; // ถ้าไม่มี Transaction ID ให้เริ่มที่ 1
            }

            // สร้าง Transaction ID ใหม่
            $newTransactionId = $transactionIdBase . str_replace('-', '', $currentDate) . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);

            // ตรวจสอบข้อมูลซ้ำใน $data[]
            $existingIndex = null;

            foreach ($data as $key => $item) {
                if (
                    $item['refcode_with'] === ($refcodes_with[$i] ?? null) &&
                    $item['material_code'] === ($material_codes[$i] ?? null) &&
                    $item['material_name'] === ($material_names[$i] ?? null) &&
                    $item['droppoint'] === ($droppoints[$i] ?? null) &&
                    $item['spec'] === ($spec[$i] ?? null)
                ) {
                    $existingIndex = $key;
                    break;
                }
            }

            if ($existingIndex !== null) {
                // ถ้าข้อมูลซ้ำ ให้รวมค่า quantity_with และ quantity_before
                $data[$existingIndex]['quantity_with'] += ($quantities_with[$i] ?? 0);
            } else {
                // เพิ่มข้อมูลใหม่ใน array $data
                $data[] = [
                    'refcode_before' => $refcodes_before[0] ?? null,
                    'refcode_with' => $refcodes_with[$i] ?? null,
                    'material_code' => $material_codes[$i] ?? null,
                    'material_name' => $material_names[$i] ?? null,
                    'spec' => $spec[$i] ?? null,
                    'unit' => $unit[$i] ?? null,
                    'droppoint' => $droppoints[$i] ?? null,
                    'date' => $currentDate,
                    'transaction_id' => $newTransactionId,
                    'quantity_before' => $avai[$i] ?? null,
                    'quantity_with' => $quantities_with[$i] ?? null,
                    'name' => $user
                ];
            }


            // เตรียมข้อมูลสำหรับ table sum
            $sum[] = [
                'refcode' => $refcodes_with[$i] ?? null,
                'material_code' => $material_codes[$i] ?? null,
                'material_name' => $material_names[$i] ?? null,
                'droppoint' => $droppoints[$i] ?? null,
                'spec' => $spec[$i] ?? null,
                'unit' => $unit[$i] ?? null,
                'quantity' => $avai[$i] ?? 0,
                'withdraw' => $quantities_with[$i] ?? 0,
                'available' => $total,
            ];
        };

        //dd($data);

        // ใช้ transaction สำหรับการบันทึกข้อมูล
        DB::transaction(function () use ($data, $sum) {
            DB::table('w_withdraw')->insert($data);

            foreach ($sum as $sumItem) {
                $existingSum = DB::table('w_sum')
                    ->where('refcode', $sumItem['refcode'])
                    ->where('material_code', $sumItem['material_code'])
                    ->where('material_name', $sumItem['material_name'])
                    ->first();

                if ($existingSum) {
                    $newWithdraw = ($existingSum->withdraw ?? 0) + $sumItem['withdraw'];

                    DB::table('w_sum')
                        ->where('id', $existingSum->id)
                        ->update([
                            'withdraw' => $newWithdraw,
                            'available' => $existingSum->quantity - $newWithdraw,
                        ]);
                } else {
                    DB::table('w_sum')->insert($sumItem);
                }
            }
        });

        return redirect('withdraw')->with('success', 'ทำรายการเบิกของสำเร็จ !');
    }

    //หน้า ผลรวม
    public function summary(Request $request)
    {

        $withdraw = DB::table('w_withdraw')->get();
        $summary =  DB::table('w_sum')
            ->orderBy('available', 'desc') // ใช้คำสั่ง orderBy เพื่อเรียงลำดับ มาก - น้อย
            ->get(); // ดึงข้อมูลทั้งหมดจาก table sum
        $droppoint = DB::table('w_droppoint_import')->get();

        // dd($summary);

        return view('inventory.sum', compact('summary', 'droppoint'));
    }


    // หน้า Region

    public function region(Request $request)
    {

        // ดึงข้อมูลจาก region โดยแสดงเฉพาะ Transaction, Date, และ Import_Quantity
        $group = DB::table('w_region')
            ->select('transaction', 'date', 'import_quantity')
            ->groupBy('transaction', 'date', 'import_quantity') // จัดกลุ่มตาม transaction, date, และ import_quantity
            ->get();

        // ดึงข้อมูลทั้งหมดจากตาราง region
        $reqion = DB::table('w_region')->get();

        // ส่งข้อมูลไปยัง view 'region'
        return view('inventory.region', compact('reqion', 'group'));
    }
}
