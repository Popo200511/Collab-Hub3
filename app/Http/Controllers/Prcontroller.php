<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Prcontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $importPr    = DB::table('r_import_pr')->limit(50)->get();
        $recordCount = DB::table('r_import_pr')->count();

        return view('ERP.pr.home', compact('importPr', 'recordCount'));
    }

    public function importpr(Request $request)
    {
        $importPr    = DB::table('r_import_pr')->limit(20)->get();
        $recordCount = DB::table('r_import_pr')->count();
        // เพิ่ม memory limit
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 600);

        $previewData = [];
        $rowCount    = 0;

        if ($request->isMethod('post')) {
            $request->validate([
                'xlsx_file_add' => 'required|file|mimes:xlsx|max:20480', // 20 MB
            ]);

            $file = $request->file('xlsx_file_add')->getRealPath();
            $zip  = new \ZipArchive;

            if ($zip->open($file) !== true) {
                return back()->withErrors(['message' => 'เปิดไฟล์ Excel ไม่ได้']);
            }

            // อ่าน sharedStrings
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

            // อ่าน sheet1
            if (! ($xml = $zip->getStream('xl/worksheets/sheet1.xml'))) {
                return back()->withErrors(['message' => 'ไม่พบ sheet1']);
            }

            $reader = new \XMLReader();
            $reader->XML(stream_get_contents($xml));

            $currentRow = [];
            $allData    = [];
            $skipHeader = true;

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
                        $val = $reader->value ?? null;
                        if ($type === 's') {
                            $val = $sharedStrings[(int) $val] ?? null;
                        }
                        $currentRow[$colIndex] = $val;
                    }
                }

                if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name === 'row') {
                    if ($skipHeader) {
                        $skipHeader = false;
                        continue;
                    }

                    if (empty($currentRow)) {
                        continue;
                    }

                    for ($i = 0; $i <= 26; $i++) {
                        $currentRow[$i] = $currentRow[$i] ?? null;
                    }

                    $mappedRow = [
                        'PR_MR_No'           => $currentRow[0],
                        'Date_'              => $currentRow[1],
                        'Delivery_Date'      => $currentRow[2],
                        'Ref_Code'           => $currentRow[3],
                        'Project/Department' => $currentRow[4],
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
                        'Ref_PettyCash'      => $currentRow[20],
                        'Ref_AP'             => $currentRow[21],
                        'AddUser'            => $currentRow[22],
                        'AddDate'            => $currentRow[23],
                        'EditUser'           => $currentRow[24],
                        'EditDate'           => $currentRow[25],
                        'Other'              => $currentRow[26],
                    ];

                    $allData[] = $mappedRow;

                    if ($rowCount < 20) {
                        $previewData[] = $mappedRow; // preview 20 แถว
                    }
                    $rowCount++;
                }
            }

            $reader->close();
            $zip->close();

            // เก็บทั้งหมดไว้ใน session เพื่อใช้บันทึกจริง
            session(['import_pr_data' => $allData]);
        }

        return view('ERP.pr.home', [
            'previewData' => $previewData,
            'rowCount'    => $rowCount,
            'recordCount' => $recordCount,
            'importPr'    => $importPr,
        ]);
    }

    // แปลง A → 0, B → 1, Z → 25, AA → 26 เป็นต้น
    private function excelColumnToIndex($letters)
    {
        $result = 0;
        $len    = strlen($letters);

        for ($i = 0; $i < $len; $i++) {
            $result = $result * 26 + (ord($letters[$i]) - 64);
        }
        return $result - 1;
    }

    private function excelDateToDMY($excelDate)
    {
        if (is_numeric($excelDate)) {
            $unixDate = ($excelDate - 25569) * 86400;
            return gmdate("d-m-Y", $unixDate); // แปลงเป็น วัน-เดือน-ปี
        }
        return $excelDate;
    }

    // ฟังก์ชันสำหรับ save จริง
    public function savepr(Request $request)
    {
        $allData = session('import_pr_data', []);

        if (! empty($allData)) {
            // ล้าง table ก่อน
            DB::table('r_import_pr')->truncate();

            $batchSize = 500;
            $batch     = [];

            foreach ($allData as $row) {
                $batch[] = $row;
                if (count($batch) >= $batchSize) {
                    DB::table('r_import_pr')->insert($batch);
                    $batch = [];
                }
            }

            if (! empty($batch)) {
                DB::table('r_import_pr')->insert($batch);
            }

            // ล้าง session หลังบันทึก
            session()->forget('import_pr_data');
        }

        return redirect()->route('pr.home')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    // pr.purchase
    public function purchase()
    {
        $importpurchase = DB::table('r_import_purchase')->limit(50)->get();
        $recordCount    = DB::table('r_import_purchase')->count();

        return view('ERP.pr.purchase', compact('importpurchase', 'recordCount'));
    }

    public function importpurchase(Request $request)
    {
        {
            $importpurchase = DB::table('r_import_purchase')->limit(20)->get();
            $recordCount    = DB::table('r_import_purchase')->count();
            // เพิ่ม memory limit
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 600);

            $previewData = [];
            $rowCount    = 0;

            if ($request->isMethod('post')) {
                $request->validate([
                    'xlsx_file_add' => 'required|file|mimes:xlsx|max:20480', // 20 MB
                ]);

                $file = $request->file('xlsx_file_add')->getRealPath();
                $zip  = new \ZipArchive;

                if ($zip->open($file) !== true) {
                    return back()->withErrors(['message' => 'เปิดไฟล์ Excel ไม่ได้']);
                }

                // อ่าน sharedStrings
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

                // อ่าน sheet1
                if (! ($xml = $zip->getStream('xl/worksheets/sheet1.xml'))) {
                    return back()->withErrors(['message' => 'ไม่พบ sheet1']);
                }

                $reader = new \XMLReader();
                $reader->XML(stream_get_contents($xml));

                $currentRow = [];
                $allData    = [];
                $skipHeader = true;

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
                            $val = $reader->value ?? null;
                            if ($type === 's') {
                                $val = $sharedStrings[(int) $val] ?? null;
                            }
                            $currentRow[$colIndex] = $val;
                        }
                    }

                    if ($reader->nodeType == \XMLReader::END_ELEMENT && $reader->name === 'row') {
                        if ($skipHeader) {
                            $skipHeader = false;
                            continue;
                        }

                        if (empty($currentRow)) {
                            continue;
                        }

                        for ($i = 0; $i <= 24; $i++) {
                            $currentRow[$i] = $currentRow[$i] ?? null;
                        }

                        $mappedRow = [
                            'No'                  => $currentRow[0],
                            'Document_No'         => $currentRow[1],
                            'PR_Date'             => $currentRow[2],
                            'Approve_Date_Time'   => $currentRow[3],
                            'Attach_File'         => $currentRow[4],
                            'Ref_Code'            => $currentRow[5],
                            'ProjectNo_DptCode'   => $currentRow[6],
                            'ProjectName_DptName' => $currentRow[7],
                            'Priority'            => $currentRow[8],
                            'Type_'               => $currentRow[9],
                            'For_'                => $currentRow[10],
                            'Remark'              => $currentRow[11],
                            'Reference_By'        => $currentRow[12],
                            'Vendor'              => $currentRow[13],
                            'Project_Place'       => $currentRow[14],
                            'Approve'             => $currentRow[15],
                            'Ref_PettyCash'       => $currentRow[16],
                            'Ref_APO'             => $currentRow[17],
                            'Print_By'            => $currentRow[18],
                            'Print_Date'          => $currentRow[19],
                            'Comment_'            => $currentRow[20],
                            'Open_P_O'            => $currentRow[21],
                            'Subc_'               => $currentRow[22],
                            'Subc_Code'           => $currentRow[23],
                            'AddBy'               => $currentRow[24],

                        ];

                        $allData[] = $mappedRow;

                        if ($rowCount < 20) {
                            $previewData[] = $mappedRow; // preview 20 แถว
                        }
                        $rowCount++;
                        //dd($rowCount);
                    }

                }
                //dd($importpurchase);

                $reader->close();
                $zip->close();

                // เก็บทั้งหมดไว้ใน session เพื่อใช้บันทึกจริง
                session(['import_pr_data' => $allData]);
            }

            return view('ERP.pr.purchase', [
                'previewData'    => $previewData,
                'rowCount'       => $rowCount,
                'recordCount'    => $recordCount,
                'importpurchase' => $importpurchase,
            ]);
        }
    }

    // ฟังก์ชันสำหรับ save จริง
    public function savepurchase(Request $request)
    {
        $allData = session('import_pr_data', []);

        // DD ดูข้อมูล session
        //dd($allData); // <-- ใส่ตรงนี้ จะหยุดโค้ดและแสดงค่าทุกอย่างใน session

        if (empty($allData) || ! is_array($allData)) {
            return redirect()->route('pr.purchase')
                ->withErrors(['message' => 'ไม่มีข้อมูลให้บันทึก หรือ session หมดอายุ']);
        }

        // ล้าง table ก่อน
        DB::table('r_import_purchase')->truncate();

        $batchSize = 500;
        $batch     = [];

        foreach ($allData as $row) {
            $batch[] = $row;

            if (count($batch) >= $batchSize) {
                DB::table('r_import_purchase')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('r_import_purchase')->insert($batch);
        }

        session()->forget('import_pr_data');

        return redirect()->route('pr.purchase')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

}