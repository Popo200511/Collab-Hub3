<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Billingcontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $billing = DB::table('r_import_biling')->get();

        return view('ERP.billing.home', compact('billing'));
    }

    public function importbilling(Request $request)
    {
        $billing = DB::table('r_import_biling')->get();
        $count   = DB::table('r_import_biling')->pluck('refCode')->toArray();

        //dd( $count);

        $dataToSave = [];
        ini_set('max_execution_time', 500);

        if ($request->isMethod('post')) {
            $request->validate([
                'xlsx_file_add' => 'required|file|mimes:xlsx|max:10240', // อนุญาตสูงสุด 10MB
            ], [
                'xlsx_file_add.required' => 'กรุณาเลือกไฟล์ Excel',
                'xlsx_file_add.mimes'    => 'ไฟล์ต้องเป็นนามสกุล .xlsx เท่านั้น',
                'xlsx_file_add.max'      => 'ขนาดไฟล์ต้องไม่เกิน 10MB',
            ]);

            $file = $request->file('xlsx_file_add');

            $zip = new \ZipArchive;
            if ($zip->open($file->getRealPath()) === true) {
                // ✅ sharedStrings
                $sharedStringsXML = $zip->getFromName('xl/sharedStrings.xml');
                $sharedStrings    = [];
                if ($sharedStringsXML) {
                    $xml = simplexml_load_string($sharedStringsXML);
                    foreach ($xml->si as $si) {
                        // กรณีมี rich text
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

                // ✅ sheet1
                $sheetXML = $zip->getFromName('xl/worksheets/sheet1.xml');
                $rows     = simplexml_load_string($sheetXML)->sheetData->row ?? [];

                $isFirstRow = true;
                foreach ($rows as $row) {
                    if ($isFirstRow) {
                        $isFirstRow = false;
                        continue; // skip header
                    }

                    $rowData = [];
                    foreach ($row->c as $cell) {
                                                        // หาตำแหน่งคอลัมน์
                        $cellRef = (string) $cell['r']; // เช่น "C2"
                        preg_match('/[A-Z]+/', $cellRef, $colLetters);
                        $colIndex = $this->excelColumnToIndex($colLetters[0]);

                        // ค่าในเซลล์
                        $val = (string) $cell->v;
                        if (isset($cell['t']) && $cell['t'] == 's') {
                            $val = $sharedStrings[(int) $val] ?? $val;
                        }

                        $rowData[$colIndex] = $val;
                    }

                    // ✅ เติมค่า null ให้ครบทุกคอลัมน์ (0–30 → A ถึง BR)
                    $finalRow = [];
                    for ($i = 0; $i <= 30; $i++) {
                        $finalRow[$i] = $rowData[$i] ?? null;
                    }

                    if (! empty(array_filter($finalRow))) {
                        $dataToSave[] = [
                            'no'                => $finalRow[0],
                            'dataType'          => $finalRow[1],
                            'documentNo'        => $finalRow[2],
                            'subcontractor'     => $finalRow[3],
                            'type'              => $finalRow[4],
                            'billNo'            => $finalRow[5],
                            'billDate'          => $this->excelDateToDMY($finalRow[6]), // Date
                            'periodNo'          => $finalRow[7],
                            'dueDate'           => $this->excelDateToDMY($finalRow[8]), // Date
                            'voucherNo'         => $finalRow[9],
                            'vendor'            => $finalRow[10],
                            'branchNo'          => $finalRow[11],
                            'invoice'           => $finalRow[12],
                            'currency'          => $finalRow[13],
                            'amount'            => $finalRow[14],
                            'lessAmt'           => $finalRow[15],
                            'netAmount'         => $finalRow[16],
                            'refCode'           => $finalRow[17],
                            'projectDepartment' => $finalRow[18],
                            'job'               => $finalRow[19],
                            'submit'            => $finalRow[20],
                            'submitBy'          => $finalRow[21],
                            'submitDate'        => $this->excelDateToDMY($finalRow[22]), // Date
                            'sign'              => $finalRow[23],
                            'remark'            => $finalRow[24],
                            'addUser'           => $finalRow[25],
                            'addDate'           => $this->excelDateToDMY($finalRow[26]), // Date
                            'editUser'          => $finalRow[27],
                            'editDate'          => $this->excelDateToDMY($finalRow[28]), // Date
                            'gl'                => $finalRow[29],
                            'writeOffStatus'    => $finalRow[30],
                        ];
                    }
                }
                $zip->close();
            } else {
                return back()->withErrors(['message' => 'ไม่สามารถเปิดไฟล์ Excel']);
            }
        }

        //dd( $dataToSave);
        $countDataToSave = count($dataToSave);

        return view('ERP.billing.home', compact('billing', 'dataToSave', 'countDataToSave', 'count'));
    }

/**
 * แปลง column letter → index
 * เช่น A=0, B=1, Z=25, AA=26, AB=27 ...
 */
    private function excelColumnToIndex($letters)
    {
        $letters = str_split($letters);
        $index   = 0;
        foreach ($letters as $char) {
            $index = $index * 26 + (ord($char) - ord('A') + 1);
        }
        return $index - 1;
    }

    private function excelDateToDMY($excelDate)
    {
        if (is_numeric($excelDate)) {
            $unixDate = ($excelDate - 25569) * 86400;
            return gmdate("d-m-Y", $unixDate); // แปลงเป็น วัน-เดือน-ปี
        }
        return $excelDate;
    }

    public function savebilling(Request $request)
    {
        $dataTosave = json_decode($request->input('dataToSave'), true);

        // ดึง refcode ทั้งหมดจาก DB
        $refcode = DB::table('r_import_biling')->pluck('refCode')->toArray();

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (! is_array($dataTosave) || empty($dataTosave)) {
            return back()->withErrors(['message' => 'ไม่มีข้อมูลที่จะบันทึก']);
        }

        // ฟังก์ชันช่วยแปลงวันที่ หากไม่ตรง format → return null
        $parseDate = function ($dateStr) {
            if (empty($dateStr)) {
                return null;
            }

            $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d']; // รองรับหลายรูปแบบ
            foreach ($formats as $format) {
                $dt = \DateTime::createFromFormat($format, $dateStr);
                if ($dt !== false) {
                    return $dt->format('Y-m-d');
                }

            }

            return null; // ไม่ตรง format → ว่าง
        };

        DB::beginTransaction();

        $newDate = []; //สำหรับเก็บข้อมูลที่ไม่ซ้ำ

        foreach ($dataTosave as $row) {
            // แปลงวันที่ทุก field
            $billDate   = $parseDate($row['billDate'] ?? null);
            $dueDate    = $parseDate($row['dueDate'] ?? null);
            $submitDate = $parseDate($row['submitDate'] ?? null);
            $addDate    = $parseDate($row['addDate'] ?? null);
            $editDate   = $parseDate($row['editDate'] ?? null);

            // ตรวจสอบ Refcode ซ้ำ
            if (! in_array($row['refCode'], $refcode)) {
                $newDate[] = [
                    'no'                => $row['no'],
                    'dataType'          => $row['dataType'],
                    'documentNo'        => $row['documentNo'],
                    'subcontractor'     => $row['subcontractor'],
                    'type'              => $row['type'],
                    'billNo'            => $row['billNo'],
                    'billDate'          => $billDate,
                    'periodNo'          => $row['periodNo'],
                    'dueDate'           => $dueDate,
                    'voucherNo'         => $row['voucherNo'],
                    'vendor'            => $row['vendor'],
                    'branchNo'          => $row['branchNo'],
                    'invoice'           => $row['invoice'],
                    'currency'          => $row['currency'],
                    'amount'            => $row['amount'],
                    'lessAmt'           => $row['lessAmt'],
                    'netAmount'         => $row['netAmount'],
                    'refCode'           => $row['refCode'],
                    'projectDepartment' => $row['projectDepartment'],
                    'job'               => $row['job'],
                    'submit'            => $row['submit'],
                    'submitBy'          => $row['submitBy'],
                    'submitDate'        => $submitDate,
                    'sign'              => $row['sign'],
                    'remark'            => $row['remark'],
                    'addUser'           => $row['addUser'],
                    'addDate'           => $addDate,
                    'editUser'          => $row['editUser'],
                    'editDate'          => $editDate,
                    'gl'                => $row['gl'],
                    'writeOffStatus'    => $row['writeOffStatus'],
                ];
            }
        }

        //dd($newDate);

        if (count($newDate) > 0) {
            // insert ข้อมูลใหม่
            DB::table('r_import_biling')->insert($newDate);
            DB::commit();
            return redirect()->route('billing.home')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } else {
            DB::rollBack();
            return back()->withErrors(['message' => 'มีข้อมูล Refcode ซ้ำกัน']);
        }
    }

    public function search(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date   = $request->input('to_date');

        $query = DB::table('r_import_biling');

        if ($from_date && $to_date) {
            $query->whereBetween('billDate', [$from_date, $to_date]);
        } elseif ($from_date) {
            $query->where('billDate', '>=', $from_date);
        } elseif ($to_date) {
            $query->where('billDate', '<=', $to_date);
        }

        $billing = $query->orderBy('billDate', 'desc')->get();

        return view('ERP.billing.home', compact('billing'));
    }

}