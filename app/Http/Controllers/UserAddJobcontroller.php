<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class UserAddJobcontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home(Request $request)
    {

        $projectCodes = DB::table('collab_projectcode')->get();
        $officeCodes  = DB::table('collab_officecode')->get();

        return view('user.about.about', compact('officeCodes', 'projectCodes'));
    }

    public function markAsRead($id)
    {
        DB::table('collab_newjob')
            ->where('id', $id)
            ->update([
                'is_read' => 1,
            ]);

        return redirect()->route('addjob.user');
    }

    public function index(Request $request)
    {
        $requester = Auth::user()->name;

        // ✔ แจ้งเตือนเฉพาะ Approved / Rejected ที่ยังไม่อ่าน
        $countNotifications = DB::table('collab_newjob')
            ->where('Requester', $requester)
            ->where('is_read', 0)
            ->whereIn('Job_Adding_Status', ['Approved', 'Rejected'])
            ->count(); // <-- ดึงเฉพาะจำนวน

        // 🔵 แต่ถ้าคุณยังอยากโชว์ list ใน dropdown ให้คงไว้
        $notifications = DB::table('collab_newjob')
            ->where('Requester', $requester)
            ->whereIn('Job_Adding_Status', ['Approved', 'Rejected'])
            ->orderBy('is_read', 'asc') // 🔥 is_read = 0 มาก่อน
            ->orderBy('id', 'desc')     // อันใหม่กว่าอยู่บน
            ->get();

        // ส่วนอื่นคงเดิม…
        $newjob = DB::table('collab_newjob')
            ->where('Requester', $requester)
            ->orderByRaw("
            CASE Job_Adding_Status
                WHEN 'Approved' THEN 1
                WHEN 'Pending' THEN 2
                WHEN 'Rejected' THEN 3
                ELSE 4
            END
        ")
            ->orderBy('Refcode', 'asc')
            ->get();

        $countApproved = DB::table('collab_newjob')
            ->where('Requester', $requester)
            ->where('Job_Adding_Status', 'Approved')
            ->count();

        $countPending = DB::table('collab_newjob')
            ->where('Requester', $requester)
            ->where('Job_Adding_Status', 'Pending')
            ->count();

        $countRejected = DB::table('collab_newjob')
            ->where('Requester', $requester)
            ->where('Job_Adding_Status', 'Rejected')
            ->count();

        $countAll = DB::table('collab_newjob')
            ->where('Requester', $requester)
            ->count();

        $projectCodes = DB::table('collab_projectcode')->get();
        $officeCodes  = DB::table('collab_officecode')->get();

        return view('user.newjobassignment.addjob', compact(
            'newjob',
            'officeCodes',
            'projectCodes',
            'countApproved',
            'countPending',
            'countRejected',
            'countAll',
            'notifications',
            'countNotifications'
        ));
    }

    public function sda(Request $request)
    {
        $newjob = DB::table('collab_newjob')
            ->orderByRaw("
        CASE Job_Adding_Status
            WHEN 'Approved' THEN 2
            WHEN 'Pending' THEN 1
            WHEN 'Rejected' THEN 3
            ELSE 4
        END
    ")
            ->orderBy('Refcode', 'asc')
            ->get();

        // ดึงงานที่ Pending
        $pendingJobs = DB::table('collab_newjob')
            ->where('Job_Adding_Status', 'Pending')
            ->get();

        // จำนวน Pending
        $countPending = $pendingJobs->count();

        // ชื่อ Requester
        $pendingRequesters = $pendingJobs->pluck('Requester')->unique();

        //dd($pendingJobs,$countPending,$pendingRequesters);

        $projectCodes = DB::table('collab_projectcode')->get();
        $officeCodes  = DB::table('collab_officecode')->get();

        return view('user.newjobassignment.sda', compact('newjob', 'officeCodes', 'projectCodes'));

    }

    // เปลี่ยน Status → ถ้า Approved จะ Gen Refcode
    public function updateStatus(Request $request, $id)
    {
        $job    = DB::table('collab_newjob')->where('id', $id)->first();
        $status = $request->input('Job_Adding_Status');

        // Gen Refcode เฉพาะ Approved และยังไม่มี Refcode
        $refcode = $job->Refcode;
        if ($status === 'Approved' && empty($refcode)) {
            $projectPrefix = substr($job->Project_Code, 0, 2);
            $yearPrefix    = '26'; // ปี ค.ศ. 2 หลัก
            $officePrefix  = substr($job->Office_Code, 0, 2);

            $prefix = $projectPrefix . '-' . $yearPrefix . '-' . $officePrefix;

            $latest = DB::table('collab_newjob')
                ->where('Refcode', 'like', $prefix . '%')
                ->orderBy('Refcode', 'desc')
                ->first();

            if ($latest) {
                $lastNumber = (int) substr($latest->Refcode, -4);
                $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = "0001";
            }

            $refcode = $prefix . $newNumber;
        }

        DB::table('collab_newjob')->where('id', $id)->update([
            'Job_Adding_Status' => $status,
            'Refcode'           => $refcode, // จะเป็น null ถ้าไม่ Approved
        ]);

        return back()->with('success', 'Status updated successfully!');
    }

    // บันทึกข้อมูลครั้งแรก → Status = Pending
    public function savenewjob(Request $request)
    {
        $user = Auth::user()->name;

        $validatedData = $request->validate([
            'site_code'               => 'required',
            'job_description'         => 'required',
            'project_code'            => 'required',
            'office_code'             => 'required',
            'estimated_revenue'       => 'required',
            'estimated_service_cost'  => 'required',
            'estimated_material_cost' => 'required',
        ]);

        $project = $request->input('project_code');
        $office  = $request->input('office_code');

        $newdata = [
            'site_code'                    => $request->input('site_code'),
            'site_name'                    => $request->input('site_name'),
            'Job_Description'              => $request->input('job_description'),

            'Project_Code'                 => $project,
            'Office_Code'                  => $office,
            'Customer_Region'              => $request->input('customer_region'),

            'Estimated_Revenue'            => $request->input('estimated_revenue'),
            'Estimated_Service_Cost'       => $request->input('estimated_service_cost'),
            'Estimated_Material_Cost'      => $request->input('estimated_material_cost'),
            'Estimated_Gross_Profit'       => $request->input('estimated_gross_profit'),
            'Estimated_Gross_ProfitMargin' => $request->input('estimated_gross_profit_margin'),

            'Requester'                    => $user,
            'Job_Adding_Status'            => 'Pending', // เริ่มต้น Pending
            'Refcode'                      => null,      // ยังไม่ Gen
        ];

        //dd($newdata);

        DB::table('collab_newjob')->insert($newdata);

        return redirect()->back()->with('success', 'New job added successfully!');
    }

    public function importnewjob(Request $request)
    {
        $newjob = DB::table('collab_newjob')->get();

        $projectCodes = DB::table('collab_projectcode')->get();
        $officeCodes  = DB::table('collab_officecode')->get();

        $dataToSave = [];
        $countData  = 0; // <-- ตัวแปรนับจำนวน
        ini_set('max_execution_time', 500);

        if ($request->isMethod('post')) {
            $request->validate([
                'xlsx_file_add' => 'required|file|mimes:xlsx|max:10240',
            ], [
                'xlsx_file_add.required' => 'กรุณาเลือกไฟล์ Excel',
                'xlsx_file_add.mimes'    => 'ไฟล์ต้องเป็นนามสกุล .xlsx เท่านั้น',
                'xlsx_file_add.max'      => 'ขนาดไฟล์ต้องไม่เกิน 10MB',
            ]);

            $file = $request->file('xlsx_file_add');

            $zip = new ZipArchive;
            if ($zip->open($file->getRealPath()) === true) {

                // อ่าน sharedStrings
                $sharedStringsXML = $zip->getFromName('xl/sharedStrings.xml');
                $sharedStrings    = [];
                if ($sharedStringsXML) {
                    $xml = simplexml_load_string($sharedStringsXML);
                    foreach ($xml->si as $si) {
                        if (isset($si->t)) {
                            $sharedStrings[] = (string) $si->t;
                        } else {
                            $text = '';
                            foreach ($si->r as $run) {
                                $text .= (string) $run->t;
                            }
                            $sharedStrings[] = $text;
                        }
                    }
                }

                // อ่าน sheet1
                $sheetXML = $zip->getFromName('xl/worksheets/sheet1.xml');
                $rows     = simplexml_load_string($sheetXML)->sheetData->row ?? [];

                $isFirstRow = true;
                $cols       = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];

                foreach ($rows as $row) {
                    if ($isFirstRow) {
                        $isFirstRow = false;
                        continue;
                    }

                    $rowData = [];
                    foreach ($row->c as $cell) {

                        $cellRef = (string) $cell['r'];
                        preg_match('/[A-Z]+/', $cellRef, $colLetters);
                        $colLetter = $colLetters[0];
                        $colIndex  = array_search($colLetter, $cols);

                        $val = (string) $cell->v;
                        if (isset($cell['t']) && $cell['t'] == 's') {
                            $val = $sharedStrings[(int) $val] ?? $val;
                        }

                        $rowData[$colIndex] = $val;
                    }

                    // เติมค่า null
                    $finalRow = [];
                    for ($i = 0; $i < count($cols); $i++) {
                        $finalRow[$i] = $rowData[$i] ?? null;
                    }

                    if (! empty(array_filter($finalRow))) {
                        $dataToSave[] = [
                            'Site_Code'                    => $finalRow[0],
                            'Site_Name'                    => $finalRow[1],
                            'Job_Description'              => $finalRow[2],
                            'Project_Code'                 => $finalRow[3],
                            'Office_Code'                  => $finalRow[4],
                            'Customer_Region'              => $finalRow[5],
                            'Estimated_Revenue'            => $finalRow[6],
                            'Estimated_Service_Cost'       => $finalRow[7],
                            'Estimated_Material_Cost'      => $finalRow[8],
                            'Estimated_Gross_Profit'       => $finalRow[9],
                            'Estimated_Gross_ProfitMargin' => $finalRow[10],
                            'Requester'                    => auth()->user()->name ?? '-',
                            'Job_Adding_Status'            => $finalRow[12],
                            'Refcode'                      => $finalRow[13],
                        ];
                    }
                }

                $zip->close();
            }

            // ⭐ นับจำนวนข้อมูลที่ import แล้ว
            $countData = count($dataToSave);
        }

        return view('user.newjobassignment.addjob', compact('dataToSave', 'newjob', 'countData', 'officeCodes', 'projectCodes'));
    }

    public function saveimportnewjob(Request $request)
    {
        if (! $request->has('dataToSave') || empty($request->dataToSave)) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลสำหรับบันทึก');
        }

        $dataList = $request->dataToSave;

        // ⭐ ดูข้อมูลก่อนบันทึกทั้งหมด (debug)
        //dd($dataList);

        try {
            foreach ($dataList as $data) {
                DB::table('collab_newjob')->insert([
                    'Site_Code'                    => $data['Site_Code'],
                    'Site_Name'                    => $data['Site_Name'],
                    'Job_Description'              => $data['Job_Description'],
                    'Project_Code'                 => $data['Project_Code'],
                    'Office_Code'                  => $data['Office_Code'],
                    'Customer_Region'              => $data['Customer_Region'],
                    'Estimated_Revenue'            => $data['Estimated_Revenue'],
                    'Estimated_Service_Cost'       => $data['Estimated_Service_Cost'],
                    'Estimated_Material_Cost'      => $data['Estimated_Material_Cost'],
                    'Estimated_Gross_Profit'       => $data['Estimated_Gross_Profit'],
                    'Estimated_Gross_ProfitMargin' => $data['Estimated_Gross_ProfitMargin'],

                    'Requester'                    => auth()->user()->name ?? '-',

                    'Refcode'                      => $data['Refcode'],

                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'บันทึกข้อมูลที่ Import สำเร็จแล้ว จำนวน: ' . count($dataList));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

}