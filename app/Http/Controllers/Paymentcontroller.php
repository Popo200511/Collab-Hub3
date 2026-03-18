<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Paymentcontroller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    // Controller
    public function exportJson()
    {
        $items = DB::table('r_import_paymenttimeline')->orderBy('PR_MR_No', 'asc')->get();
        return response()->json($items);
    }


    public function payment(Request $request)
{
    $search   = $request->input('search');
    $showAll  = $request->boolean('show_all'); // 👈 เพิ่มตรงนี้
    $query    = DB::table('r_import_paymenttimeline');

    // กันค่า -, 0, -0, - 0
    if (in_array(str_replace(' ', '', $search), ['-', '0', '-0' , '- 0'])) {
        $search = null;
    }

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('ref_code', 'LIKE', "%{$search}%")
              ->orWhere('project_name', 'LIKE', "%{$search}%")
              ->orWhere('Vendors', 'LIKE', "%{$search}%")
              ->orWhere('Remark', 'LIKE', "%{$search}%")
              ->orWhere('PR_MR_No', 'LIKE', "%{$search}%")
              ->orWhere('docno', 'LIKE', "%{$search}%");
        });
    }

    $items = $query->orderBy('PR_MR_No', 'desc')->get();

    $groupedTimeline = $items->groupBy('PR_MR_No');

    $count = $groupedTimeline->count();

    // ===============================
    // ถ้ากด Show All
    // ===============================
    if ($showAll) {

        $timeline = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedTimeline,
            $count,
            $count, // แสดงทั้งหมด
            1,
            ['path' => request()->url(), 'query' => request()->query()]
        );

    } else {

        $page    = request()->get('page', 1);
        $perPage = 10;

        $paged = $groupedTimeline->slice(($page - 1) * $perPage, $perPage);

        $timeline = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged,
            $count,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    return view('user.erp.payment_timeline.payment', compact('timeline', 'count', 'search', 'showAll'));
}

    /* NEW
    public function payment(Request $request)
    {
        $search = $request->input('search');
        $query  = DB::table('r_import_paymenttimeline');
        

        $search = $request->input('search');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                // ค้นหา ref_code แบบปกติ
                $q->where('ref_code', 'LIKE', "%{$search}%")

                // ค้นหาในคอลัมน์อื่น ๆ ด้วย
                    ->orWhere('project_name', 'LIKE', "%{$search}%")
                    ->orWhere('Vendors', 'LIKE', "%{$search}%")
                    ->orWhere('Remark', 'LIKE', "%{$search}%")
                    ->orWhere('PR_MR_No', 'LIKE', "%{$search}%")
                    ->orWhere('docno', 'LIKE', "%{$search}%");
            });
        }

        // ดึงข้อมูลทั้งหมดก่อน
        $items = $query->orderBy('PR_MR_No', 'desc')->get();

        // Group ตาม PR
        $groupedTimeline = $items->groupBy('PR_MR_No');

        // Paginate ที่ระดับ PR
        $page    = request()->get('page', 1);
        $perPage = 10; // จำนวน PR ต่อหน้า
        $paged   = $groupedTimeline->slice(($page - 1) * $perPage, $perPage);

        $timeline = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged,
            $groupedTimeline->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        //dd($timeline);

        $count = $groupedTimeline->count();

        return view('user.erp.payment_timeline.payment', compact('timeline', 'count'));
    }
    */

    /* OLD
    public function payment(Request $request)
    {
        // ดึงข้อมูลแสดงผลตรงๆ จากตารางสรุป (ไม่มีการเช็ค Sync ใดๆ ทั้งสิ้น)
        $search = $request->input('search');
        $query  = DB::table('r_import_paymenttimeline');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ref_code', 'LIKE', "%{$search}%");
                    //->orWhere('PR_MR_No', 'LIKE', "%{$search}%");
            });
        }

        // ใช้ paginate ปกติ หรือ simplePaginate ก็ได้ (จะเร็วขึ้นทันตาเห็น)
        $timeline = $query->orderBy('PR_MR_No', 'asc')->paginate(100);
        $count    = $timeline->total();


        return view('user.erp.payment_timeline.payment', compact('timeline', 'count'));
    }
    */

    public static function refreshTimelineData()
    {
        DB::transaction(function () {

            //  ล้างข้อมูลเก่า
            DB::table('r_import_paymenttimeline')->delete();

            //  Query Join (PR -> WO -> Billing)
            $sourceQuery = DB::table('r_import_purchase as p')
                ->leftJoin('r_import_refcode as r', 'r.ref_code', '=', 'p.Ref_Code')
                ->leftJoin('r_import_wo as w', 'w.refprno', '=', 'p.PR_MR_No')
                ->leftJoin('r_import_biling as b', 'b.documentNo_clean', '=', 'w.docno')
                ->select([
                    'r.ref_code', 'r.project_name',
                    'p.PR_MR_No', 'p.Remark', 'p.Vendors',
                    'p.Delivery_Date', 'p.Approve_Date', 'p.Amount as pr_amount',
                    'w.refprno', 'w.docno', 'w.docdate', 'w.docapp_dt', 'w.Amount as wo_amount',
                    'b.documentNo', 'b.billNo', 'b.addDate', 'b.Sign', 'b.amount as bill_amount',
                    'b.voucherNo', 'b.dueDate',
                ]);

            //  Insert ข้อมูลที่ Join แล้วลงตารางสรุป
            DB::table('r_import_paymenttimeline')->insertUsing([
                'ref_code', 'project_name',
                'PR_MR_No', 'Remark', 'Vendors',
                'Delivery_Date', 'Approve_Date', 'Amount',
                'wo_refprno', 'docno', 'docdate', 'docapp_dt', 'wo_amount',
                'documentNo', 'billNo', 'addDate', 'Sign', 'bill_amount',
                'voucherNo', 'dueDate',
            ], $sourceQuery);
        });
    }

    public function importpurchase(Request $request)
    {
        //dd('ฟังก์ชันนำเข้าข้อมูลทำงานแล้ว PR');
        // 1. Validation ไฟล์เบื้องต้น
        $request->validate([
            'xlsx_file_add' => 'required|file|mimes:xlsx|max:40960',
        ], [
            'xlsx_file_add.required' => 'กรุณาเลือกไฟล์ Excel',
            'xlsx_file_add.mimes'    => 'ไฟล์ต้องเป็น .xlsx เท่านั้น',
            'xlsx_file_add.max'      => 'ไฟล์ต้องไม่เกิน 40MB',
        ]);

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '512M');

        try {
            $file = $request->file('xlsx_file_add')->getRealPath();
            $zip  = new \ZipArchive;

            if ($zip->open($file) !== true) {
                throw new \Exception('ไม่สามารถเปิดไฟล์ Excel ได้');
            }

            // เริ่ม Transaction หลังจากเปิดไฟล์สำเร็จ
            DB::beginTransaction();

            // 2. ใช้ delete() แทน truncate() เพื่อป้องกัน Implicit Commit (ตัวการที่ทำให้เกิด Error)
            DB::table('r_import_purchase')->delete();

            // --- อ่าน Shared Strings ---
            $sharedStrings = [];
            if (($xml = $zip->getStream('xl/sharedStrings.xml'))) {
                $reader = new \XMLReader();
                $reader->XML(stream_get_contents($xml));
                while ($reader->read()) {
                    if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 't') {
                        $reader->read();
                        $sharedStrings[] = $reader->value ?? '';
                    }
                }
                $reader->close();
            }

            // --- อ่าน Sheet1 ---
            if (! ($xml = $zip->getStream('xl/worksheets/sheet1.xml'))) {
                throw new \Exception('ไม่พบข้อมูล Sheet1 ในไฟล์');
            }

            $reader = new \XMLReader();
            $reader->XML(stream_get_contents($xml));

            $skipHeader   = true;
            $dataToInsert = [];
            $batchSize    = 200;
            $rowCount     = 0;

            while ($reader->read()) {
                if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 'row') {
                    $currentRow = [];
                }

                if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 'c') {
                    $cellRef = $reader->getAttribute('r');
                    preg_match('/[A-Z]+/', $cellRef, $colLetters);
                    $colIndex = $this->excelColumnToIndex($colLetters[0]);
                    $type     = $reader->getAttribute('t');

                    $reader->read();
                    if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 'v') {
                        $reader->read();
                        $value = $reader->value ?? null;
                        if ($type === 's') {
                            $value = $sharedStrings[(int) $value] ?? null;
                        }
                        $currentRow[$colIndex] = $value;
                    }
                }

                if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name === 'row') {

                    // --- ตรวจสอบหัวคอลัมน์ (Header Validation) ---
                    if ($skipHeader) {
                        $expectedHeaders = [
                            'PR/MR No.', 'Date', 'Delivery Date', 'Ref. Code', 'Project/Department',
                            'Job', 'PO Type', 'For', 'Remark',
                            'Amount', 'Requestor by',
                            'Vendors.', 'Approve', 'Approve Date', 'Status', 'Pending',
                            'Ref. BOQ', 'Submit', 'Submit by', 'Submit Date', 'Ref. Petty Cash',
                            'Ref. AP', 'Add User', 'Add Date', 'Edit User', 'Edit Date', '',
                        ];

                        // 1. เช็คว่าจำนวนคอลัมน์ในแถวนี้ มี "มากกว่า" ที่กำหนดไว้หรือไม่
                        if (count($currentRow) > count($expectedHeaders)) {
                            throw new \Exception("ไฟล์มีจำนวนคอลัมน์เกินกำหนด (พบ " . count($currentRow) . " คอลัมน์, คาดหวัง " . count($expectedHeaders) . " คอลัมน์)");
                        }

                        foreach ($expectedHeaders as $index => $expectedTitle) {
                            $actualTitle = trim($currentRow[$index] ?? '');
                            if ($actualTitle !== $expectedTitle) {
                                throw new \Exception("หัวคอลัมน์ที่ " . ($index + 1) . " ไม่ถูกต้อง: คาดหวัง '{$expectedTitle}' แต่พบ '{$actualTitle}'");
                            }
                        }
                        $skipHeader = false;
                        continue;
                    }

                    if (empty($currentRow)) {
                        continue;
                    }

                    // เตรียมข้อมูล (0-26)
                    for ($i = 0; $i <= 26; $i++) {
                        $currentRow[$i] = $currentRow[$i] ?? null;
                    }

                    // --- Mapping ข้อมูล ---
                    $dataToInsert[] = [
                        'PR_MR_No'           => $currentRow[0],
                        'Date'               => $currentRow[1],
                        'Delivery_Date'      => $currentRow[2],
                        'Ref_Code'           => $currentRow[3],
                        'Project_Department' => $currentRow[4],
                        'Job'                => $currentRow[5],
                        'PO_Type'            => $currentRow[6],
                        'For'                => $currentRow[7],
                        'Remark'             => $currentRow[8],
                        'Amount'             => $currentRow[9],
                        'Requestor_by'       => $currentRow[10],
                        'Vendors'            => $currentRow[11],
                        'Approve'            => $currentRow[12],
                        'Approve_Date'       => $currentRow[13],
                        'Status'             => $currentRow[14],
                        'Pending'            => $currentRow[15],
                        'Ref_BOQ'            => $currentRow[16],
                        'Submit'             => $currentRow[17],
                        'Submit_by'          => $currentRow[18],
                        'Submit_Date'        => $currentRow[19],
                        'Ref_Petty_Cash'     => $currentRow[20],
                        'Ref_AP'             => $currentRow[21],
                        'Add_User'           => $currentRow[22],
                        'Add_Date'           => $currentRow[23],
                        'Edit_User'          => $currentRow[24],
                        'Edit_Date'          => $currentRow[25],
                        'Other'              => $currentRow[26],
                    ];

                    $rowCount++;

                    if (count($dataToInsert) >= $batchSize) {
                        DB::table('r_import_purchase')->insert($dataToInsert);
                        $dataToInsert = [];
                    }
                }
            }

            if (! empty($dataToInsert)) {
                DB::table('r_import_purchase')->insert($dataToInsert);
            }

            // --- Commit เมื่อทุกอย่างทำงานสำเร็จ ---
            DB::commit();

            self::refreshTimelineData();

            if (isset($reader)) {
                $reader->close();
            }

            if (isset($zip)) {
                $zip->close();
            }

            return back()->with('success', "นำเข้าข้อมูลเรียบร้อยแล้ว จำนวน {$rowCount} รายการ");

        } catch (\Exception $e) {
            // ตรวจสอบว่ามี Transaction เปิดอยู่จริงไหมก่อนสั่ง Rollback
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return back()->withErrors(['error' => 'ไม่สามารถนำเข้าข้อมูลได้: ' . $e->getMessage()]);
        }
    }

    public function importwo(Request $request)
    {
        //dd('ฟังก์ชันนำเข้าข้อมูลทำงานแล้ว WO');
        // 1. Validation ไฟล์เบื้องต้น
        $request->validate([
            'xlsx_file_add' => 'required|file|mimes:xlsx|max:40960',
        ], [
            'xlsx_file_add.required' => 'กรุณาเลือกไฟล์ Excel',
            'xlsx_file_add.mimes'    => 'ไฟล์ต้องเป็น .xlsx เท่านั้น',
            'xlsx_file_add.max'      => 'ไฟล์ต้องไม่เกิน 40MB',
        ]);

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '512M');

        try {
            $file = $request->file('xlsx_file_add')->getRealPath();
            $zip  = new \ZipArchive;

            if ($zip->open($file) !== true) {
                throw new \Exception('ไม่สามารถเปิดไฟล์ Excel ได้');
            }

            // เริ่ม Transaction หลังจากเปิดไฟล์สำเร็จ
            DB::beginTransaction();

            // 2. ใช้ delete() แทน truncate() เพื่อป้องกัน Implicit Commit (ตัวการที่ทำให้เกิด Error)
            DB::table('r_import_wo')->delete();

            // --- อ่าน Shared Strings ---
            $sharedStrings = [];
            if (($xml = $zip->getStream('xl/sharedStrings.xml'))) {
                $reader = new \XMLReader();
                $reader->XML(stream_get_contents($xml));
                while ($reader->read()) {
                    if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 't') {
                        $reader->read();
                        $sharedStrings[] = $reader->value ?? '';
                    }
                }
                $reader->close();
            }

            // --- อ่าน Sheet1 ---
            if (! ($xml = $zip->getStream('xl/worksheets/sheet1.xml'))) {
                throw new \Exception('ไม่พบข้อมูล Sheet1 ในไฟล์');
            }

            $reader = new \XMLReader();
            $reader->XML(stream_get_contents($xml));

            $skipHeader   = true;
            $dataToInsert = [];
            $batchSize    = 200;
            $rowCount     = 0;

            while ($reader->read()) {
                if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 'row') {
                    // จองที่ไว้ 43 คอลัมน์ (0-42) ให้เป็นค่า null ทั้งหมดก่อน
                    $currentRow = array_fill(0, 43, null);
                }

                if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 'c') {
                    $cellRef = $reader->getAttribute('r');
                    preg_match('/[A-Z]+/', $cellRef, $colLetters);
                    $colIndex = $this->excelColumnToIndex($colLetters[0]);
                    $type     = $reader->getAttribute('t');

                    $reader->read();
                    if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 'v') {
                        $reader->read();
                        $value = $reader->value ?? null;
                        if ($type === 's') {
                            $value = $sharedStrings[(int) $value] ?? null;
                        }
                        $currentRow[$colIndex] = $value;
                    }
                }

                if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name === 'row') {

                    // --- ตรวจสอบหัวคอลัมน์ (Header Validation) ---
                    if ($skipHeader) {
                        $expectedHeaders = [
                            'maincode', 'docno', 'docdate', 'docapp_dt', 'quono', 'quodate', 'period_beg', 'period_end',
                            'acct_no', 'cust_name', 'pre_event', 'pre_des', 'jobcode',
                            'jobname', 'dpt_code', 'dpt_name', 'vattype', 'vatper', 'refcode', 'pre_event2',
                            'data_ty', 'bus_code', 'docapp', 'adduser', 'Contract_Amount',
                            'Amount', 'Advance', 'Vat_amt', 'WT_Amount', 'retention01_amt', 'Net_amount',
                            'description', 'refprno', 'revno', 'buyer_name', 'send_date_mail', 'email_format',
                            'reply_status', 'reply_remark', 'reply_dt', 'user_name', 'contract_type', 'contract_type_name',
                        ];

                        // 1. เช็คว่าจำนวนคอลัมน์ในแถวนี้ มี "มากกว่า" ที่กำหนดไว้หรือไม่
                        if (count($currentRow) > count($expectedHeaders)) {
                            throw new \Exception("ไฟล์มีจำนวนคอลัมน์เกินกำหนด (พบ " . count($currentRow) . " คอลัมน์, คาดหวัง " . count($expectedHeaders) . " คอลัมน์)");
                        }

                        foreach ($expectedHeaders as $index => $expectedTitle) {
                            $actualTitle = trim($currentRow[$index] ?? '');
                            if ($actualTitle !== $expectedTitle) {
                                throw new \Exception("หัวคอลัมน์ที่ " . ($index + 1) . " ไม่ถูกต้อง: คาดหวัง '{$expectedTitle}' แต่พบ '{$actualTitle}'");
                            }
                        }
                        $skipHeader = false;
                        continue;
                    }

                    if (empty($currentRow)) {
                        continue;
                    }

                    // เตรียมข้อมูล (0-42)
                    for ($i = 0; $i <= 42; $i++) {
                        $currentRow[$i] = $currentRow[$i] ?? null;
                    }

                    // --- Mapping ข้อมูล ---
                    $dataToInsert[] = [
                        'maincode'           => $currentRow[0],
                        'docno'              => $currentRow[1],
                        'docdate'            => $this->excelDateToPhp($currentRow[2]), // ครอบฟังก์ชันแปลงวันที่
                        'docapp_dt'          => $this->excelDateToPhp($currentRow[3]), // ครอบฟังก์ชันแปลงวันที่
                        'quono'              => $currentRow[4],
                        'quodate'            => $this->excelDateToPhp($currentRow[5]), // ครอบฟังก์ชันแปลงวันที่
                        'period_beg'         => $this->excelDateToPhp($currentRow[6]), // ครอบฟังก์ชันแปลงวันที่
                        'period_end'         => $this->excelDateToPhp($currentRow[7]), // ครอบฟังก์ชันแปลงวันที่
                        'acct_no'            => $currentRow[8],
                        'cust_name'          => $currentRow[9],
                        'pre_event'          => $currentRow[10],
                        'pre_des'            => $currentRow[11],
                        'jobcode'            => $currentRow[12],
                        'jobname'            => $currentRow[13],
                        'dpt_code'           => $currentRow[14],
                        'dpt_name'           => $currentRow[15],
                        'vattype'            => $currentRow[16],
                        'vatper'             => $currentRow[17],
                        'refcode'            => $currentRow[18],
                        'pre_event2'         => $currentRow[19],
                        'data_ty'            => $currentRow[20],
                        'bus_code'           => $currentRow[21],
                        'docapp'             => $currentRow[22],
                        'adduser'            => $currentRow[23],
                        'Contract_Amount'    => $currentRow[24],
                        'Amount'             => $currentRow[25],
                        'Advance'            => $currentRow[26],
                        'Vat_amt'            => $currentRow[27],
                        'WT_Amount'          => $currentRow[28],
                        'retention01_amt'    => $currentRow[29],
                        'Net_amount'         => $currentRow[30],
                        'description'        => $currentRow[31],
                        'refprno'            => $currentRow[32],
                        'revno'              => $currentRow[33],
                        'buyer_name'         => $currentRow[34],
                        'send_date_mail'     => $currentRow[35],
                        'email_format'       => $currentRow[36],
                        'reply_status'       => $currentRow[37],
                        'reply_remark'       => $currentRow[38],
                        'reply_dt'           => $currentRow[39],
                        'user_name'          => $currentRow[40],
                        'contract_type'      => $currentRow[41],
                        'contract_type_name' => $currentRow[42],
                    ];

                    $rowCount++;

                    if (count($dataToInsert) >= $batchSize) {
                        DB::table('r_import_wo')->insert($dataToInsert);
                        $dataToInsert = [];
                    }
                }
            }

            if (! empty($dataToInsert)) {
                DB::table('r_import_wo')->insert($dataToInsert);
            }

            // --- Commit เมื่อทุกอย่างทำงานสำเร็จ ---
            DB::commit();

            // เพิ่มบรรทัดนี้
            self::refreshTimelineData();

            if (isset($reader)) {
                $reader->close();
            }

            if (isset($zip)) {
                $zip->close();
            }

            return back()->with('success', "นำเข้าข้อมูลเรียบร้อยแล้ว จำนวน {$rowCount} รายการ");

        } catch (\Exception $e) {
            // ตรวจสอบว่ามี Transaction เปิดอยู่จริงไหมก่อนสั่ง Rollback
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return back()->withErrors(['error' => 'ไม่สามารถนำเข้าข้อมูลได้: ' . $e->getMessage()]);
        }
    }

    public function importbilling(Request $request)
    {
        //dd('ฟังก์ชันนำเข้าข้อมูลทำงานแล้ว Billing');
        // 1. Validation ไฟล์เบื้องต้น
        $request->validate([
            'xlsx_file_add' => 'required|file|mimes:xlsx|max:40960',
        ], [
            'xlsx_file_add.required' => 'กรุณาเลือกไฟล์ Excel',
            'xlsx_file_add.mimes'    => 'ไฟล์ต้องเป็น .xlsx เท่านั้น',
            'xlsx_file_add.max'      => 'ไฟล์ต้องไม่เกิน 40MB',
        ]);

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '512M');

        try {
            $file = $request->file('xlsx_file_add')->getRealPath();
            $zip  = new \ZipArchive;

            if ($zip->open($file) !== true) {
                throw new \Exception('ไม่สามารถเปิดไฟล์ Excel ได้');
            }

            // เริ่ม Transaction หลังจากเปิดไฟล์สำเร็จ
            DB::beginTransaction();

            // 2. ใช้ delete() แทน truncate() เพื่อป้องกัน Implicit Commit (ตัวการที่ทำให้เกิด Error)
            DB::table('r_import_biling')->delete();

            // --- อ่าน Shared Strings ---
            $sharedStrings = [];
            if (($xml = $zip->getStream('xl/sharedStrings.xml'))) {
                $reader = new \XMLReader();
                $reader->XML(stream_get_contents($xml));
                while ($reader->read()) {
                    if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 't') {
                        $reader->read();
                        $sharedStrings[] = $reader->value ?? '';
                    }
                }
                $reader->close();
            }

            // --- อ่าน Sheet1 ---
            if (! ($xml = $zip->getStream('xl/worksheets/sheet1.xml'))) {
                throw new \Exception('ไม่พบข้อมูล Sheet1 ในไฟล์');
            }

            $reader = new \XMLReader();
            $reader->XML(stream_get_contents($xml));

            $skipHeader   = true;
            $dataToInsert = [];
            $batchSize    = 200;
            $rowCount     = 0;

            while ($reader->read()) {
                if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 'row') {
                    $currentRow = [];
                }

                if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 'c') {
                    $cellRef = $reader->getAttribute('r');
                    preg_match('/[A-Z]+/', $cellRef, $colLetters);
                    $colIndex = $this->excelColumnToIndex($colLetters[0]);
                    $type     = $reader->getAttribute('t');

                    $reader->read();
                    if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name === 'v') {
                        $reader->read();
                        $value = $reader->value ?? null;
                        if ($type === 's') {
                            $value = $sharedStrings[(int) $value] ?? null;
                        }
                        $currentRow[$colIndex] = $value;
                    }
                }

                if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name === 'row') {

                    // --- ตรวจสอบหัวคอลัมน์ (Header Validation) ---
                    if ($skipHeader) {
                        $expectedHeaders = [
                            'No.', 'Data Type', 'Document No. (WO)', 'Subcontractor', 'Type',
                            'Bill No.', 'Bill Date', 'Period No.', 'Due Date', 'Voucher No.',
                            'Vendor', 'Branch No.', 'Invoice', 'Currency', 'Amount',
                            'Less Amt.',
                            'Net Amount', 'Ref Code', 'Project / Department', 'Job', 'Submit',
                            'Submit By', 'Submit Date', 'Sign', 'Remark', 'Add User', 'Add Date', 'Edit User', 'Edit Date', 'GL',
                            'Write off Status',
                        ];

                        // 1. เช็คว่าจำนวนคอลัมน์ในแถวนี้ มี "มากกว่า" ที่กำหนดไว้หรือไม่
                        if (count($currentRow) > count($expectedHeaders)) {
                            throw new \Exception("ไฟล์มีจำนวนคอลัมน์เกินกำหนด (พบ " . count($currentRow) . " คอลัมน์, คาดหวัง " . count($expectedHeaders) . " คอลัมน์)");
                        }

                        foreach ($expectedHeaders as $index => $expectedTitle) {
                            $actualTitle = trim($currentRow[$index] ?? '');
                            if ($actualTitle !== $expectedTitle) {
                                throw new \Exception("หัวคอลัมน์ที่ " . ($index + 1) . " ไม่ถูกต้อง: คาดหวัง '{$expectedTitle}' แต่พบ '{$actualTitle}'");
                            }
                        }
                        $skipHeader = false;
                        continue;
                    }

                    if (empty($currentRow)) {
                        continue;
                    }

                    // เตรียมข้อมูล (0-30)
                    for ($i = 0; $i <= 30; $i++) {
                        $currentRow[$i] = $currentRow[$i] ?? null;
                    }

                    // --- ภายใน Loop ประมวลผลแถว ---

                    // 1. ดึงชื่อจากคอลัมน์ Vendor (Index 10 หรือ คอลัมน์ K)
                    // หมายเหตุ: หากดึงแล้วว่าง ให้ลองเช็ค dd($currentRow) ว่าชื่อบริษัทไปตกที่ Index ไหน
                    $vendorName = trim($currentRow[10] ?? '');
                    //d($vendorName); // เช็คว่าชื่อบริษัทถูกดึงมาได้ถูกต้องไหม

                    // 2. ดึงยอดเงินจากช่อง Amount (Index 14 หรือ คอลัมน์ O)
                    $rawAmount = str_replace(',', '', $currentRow[14] ?? '0');
                    $amount    = (float) $rawAmount;

                    // เงื่อนไขเช็ค "คำนำหน้า" (Prefix)
                    $keywords       = ['บริษัท', 'ห้างหุ้นส่วน', 'นายพนม'];
                    $isSpecialMatch = false;

                    foreach ($keywords as $word) {
                        // mb_substr ช่วยให้เช็คคำไทยที่ "ขึ้นต้น" ประโยคได้เป๊ะที่สุด
                        if (mb_substr($vendorName, 0, mb_strlen($word)) === $word) {
                            $isSpecialMatch = true;
                            break;
                        }
                    }

                    // 4. สมการบัญชี:
                    // เจอ 3 คำนี้ (บริษัท/ห้าง/นายพนม) -> หาร 1.04 (เช่น 128,960 / 1.04 = 124,000)
                    // ถ้าไม่เจอ (บุคคลทั่วไป)        -> หาร 0.97
                    $calculatedNet = ($isSpecialMatch) ? ($amount / 1.04) : ($amount / 0.97);

                    $originalDocNo = trim($currentRow[2] ?? ''); // ค่าต้นฉบับ เช่น BWO2025...

                    // ดึงตั้งแต่ตัวอักษรที่ 2 เป็นต้นไป (PHP เริ่มนับตำแหน่งแรกที่ 0 ดังนั้นตำแหน่งที่ 2 คือเลข 1)
                    $cleanDocNo = mb_substr($originalDocNo, 1);

                    // ---  Mapping ข้อมูล ---
                    $dataToInsert[] = [
                        'no'                => $currentRow[0] ?? null,
                        'dataType'          => $currentRow[1] ?? null,
                        'documentNo'        => $originalDocNo ?? null,
                        'documentNo_clean'  => $cleanDocNo, // คอลัมน์ใหม่สำหรับเก็บเลขที่เอกสารที่ "ทำความสะอาดแล้ว"
                        'subcontractor'     => $currentRow[3] ?? null,
                        'type'              => $currentRow[4] ?? null,
                        'billNo'            => $currentRow[5] ?? null,
                        'billDate'          => $currentRow[6] ?? null,
                        'periodNo'          => $currentRow[7] ?? null,
                        'dueDate'           => $currentRow[8] ?? null,
                        'voucherNo'         => $currentRow[9] ?? null,
                        'vendor'            => $vendorName,
                        'branchNo'          => $currentRow[11] ?? null,
                        'invoice'           => $currentRow[12] ?? null,
                        'currency'          => $currentRow[13] ?? null,
                        'amount'            => round($calculatedNet, 2),
                        'lessAmt'           => $currentRow[15] ?? null,
                        'netAmount'         => (float) str_replace(',', '', $currentRow[14] ?? 0),
                        'refCode'           => $currentRow[17] ?? null,
                        'projectDepartment' => $currentRow[18] ?? null,
                        'job'               => $currentRow[19] ?? null,
                        'submit'            => $currentRow[20] ?? null,
                        'submitBy'          => $currentRow[21] ?? null,
                        'submitDate'        => $currentRow[22] ?? null,
                        'sign'              => $currentRow[23] ?? null,
                        'remark'            => $currentRow[24] ?? null,
                        'addUser'           => $currentRow[25] ?? null,
                        'addDate'           => $currentRow[26] ?? null,
                        'editUser'          => $currentRow[27] ?? null,
                        'editDate'          => $currentRow[28] ?? null,
                        'gl'                => $currentRow[29] ?? null,
                        'writeOffStatus'    => $currentRow[30] ?? null,
                        'documentNo_clean'  => $cleanDocNo, // คอลัมน์ใหม่สำหรับเก็บเลขที่เอกสารที่ "ทำความสะอาดแล้ว"
                    ];

                    //dd($dataToInsert);

                    $rowCount++;

                    if (count($dataToInsert) >= $batchSize) {
                        DB::table('r_import_biling')->insert($dataToInsert);
                        $dataToInsert = [];
                    }
                }
            }

            if (! empty($dataToInsert)) {
                DB::table('r_import_biling')->insert($dataToInsert);
            }

            // --- Commit เมื่อทุกอย่างทำงานสำเร็จ ---
            DB::commit();

            // เพิ่มบรรทัดนี้
            self::refreshTimelineData();

            if (isset($reader)) {
                $reader->close();
            }

            if (isset($zip)) {
                $zip->close();
            }

            return back()->with('success', "นำเข้าข้อมูลเรียบร้อยแล้ว จำนวน {$rowCount} รายการ");

        } catch (\Exception $e) {
            // ตรวจสอบว่ามี Transaction เปิดอยู่จริงไหมก่อนสั่ง Rollback
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return back()->withErrors(['error' => 'ไม่สามารถนำเข้าข้อมูลได้: ' . $e->getMessage()]);
        }
    }

    private function excelDateToPhp($serial)
    {
        if (! $serial || ! is_numeric($serial)) {
            return $serial;
        }

        // สูตรคำนวณ: (Serial Excel - 25569) * วินาทีต่อวัน
        $unix_date = ($serial - 25569) * 86400;
        return date("Y-m-d", $unix_date);
    }

    private function excelColumnToIndex($letters)
    {
        $result = 0;
        $len    = strlen($letters);

        for ($i = 0; $i < $len; $i++) {
            $result = $result * 26 + (ord($letters[$i]) - 64);
        }
        return $result - 1;
    }
}
