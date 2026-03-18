<?php
namespace App\Http\Controllers;

use App\Mail\UpdateMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Admincontroller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    //Data total
    public function index()
    {
        $areas = DB::table('area')->get();

        $data = DB::table('main_csv as m')
            ->leftJoin('saq_csv as s', 'm.id', '=', 's.id_saq')
            ->leftJoin('cr_csv as c', 'm.id', '=', 'c.id_cr')
            ->leftJoin('tssr_csv as t', 'm.id', '=', 't.id_tssr')
            ->leftJoin('civilwork_csv as cw', 'm.id', '=', 'cw.id_cw')
            ->leftJoin('additional_csv as add', 'm.id', '=', 'add.id_add')
            ->get();

        return view('blog', compact('areas', 'data'));
        // OK

    }

    public function edit($id)
    {
        //dd($id);

        $areas = DB::table('area')->get();
        $blog  = DB::table("main_csv as m")
            ->leftJoin('saq_csv as s', 'm.id', '=', 's.id_saq')
            ->leftJoin('cr_csv as c', 'm.id', '=', 'c.id_cr')
            ->leftJoin('tssr_csv as t', 'm.id', '=', 't.id_tssr')
            ->leftJoin('civilwork_csv as cw', 'm.id', '=', 'cw.id_cw')
            ->leftJoin('additional_csv as add', 'm.id', '=', 'add.id_add')
            ->leftJoin('area as b', 'm.Region_id', '=', 'b.Region_id') // เปลี่ยนจาก join เป็น leftJoin
            ->where('m.id', $id)
            ->first();

        // ดึง PR_MR_No ไม่ซ้ำ 10 รายการล่าสุด
        $prList = DB::table('r_import_pr')
            ->select('PR_MR_No')
            ->distinct()
            ->orderBy('id', 'desc') // หรือ 'created_at' ถ้ามี
            ->get();
        //dd($blog);

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (! $blog) {
            return redirect()->back()->withErrors('Data not found for the given ID.');
        }

        return view("edit", compact("blog", "areas", "prList"));
    }

    // LINE NOTIFY
    private function sendLineNotify($message)
    {
        $line_token = 'Rsov88H4frcqLTscPnyPKSBuESSuccoqKFfPO1QcZey'; // ใส่ Line Notify Token ของคุณ
        $line_api   = 'https://notify-api.line.me/api/notify';
        $queryData  = http_build_query(['message' => $message], '', '&');

        $headerOptions = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n" .
                "Authorization: Bearer " . $line_token . "\r\n" .
                "Content-Length: " . strlen($queryData) . "\r\n",
                'content' => $queryData,
            ],
        ];

        $context = stream_context_create($headerOptions);
        $result  = file_get_contents($line_api, false, $context);

        return json_decode($result);
    }

    public function update(Request $request, $id)
    {

        $blog = DB::table("main_csv as m")
            ->leftJoin('saq_csv as s', 'm.id', '=', 's.id_saq')
            ->leftJoin('cr_csv as c', 'm.id', '=', 'c.id_cr')
            ->leftJoin('tssr_csv as t', 'm.id', '=', 't.id_tssr')
            ->leftJoin('civilwork_csv as cw', 'm.id', '=', 'cw.id_cw')
            ->leftJoin('additional_csv as add', 'm.id', '=', 'add.id_add')
            ->leftJoin('area as b', 'm.Region_id', '=', 'b.Region_id')
            ->where('m.id', $id)
            ->first();

        $request->validate(
            [
                'SiteCode' => 'required',

            ],
            [
                'SiteCode' => 'กรุณากรอก SiteCode',

            ]
        );

        // แปลงค่าเป็นตัวเลขก่อนการคำนวณ BALACE

        $amount1_IN = floatval($request->Amount1_IN);
        $amount2_IN = floatval($request->Amount2_IN);
        $amount1_CC = floatval($request->Amount1_CC);
        $amount2_CC = floatval($request->Amount2_CC);
        $po_Amount  = floatval($request->PO_Amount_IN);

        // คำนวณผลรวม
        $total      = $amount1_IN + $amount2_IN + $amount1_CC + $amount2_CC;
        $banlace    = $request->PO_Amount_IN;
        $difference = ($banlace - $total);

        if (empty($banlace)) {
            $Banlace_IN = null;
        } elseif ($total > $banlace) {
            $Banlace_IN = $difference;
        } else {
            $Banlace_IN = $difference;
        }

        $data_MAIN = [ //input form
                           //  'GTNJobNo' => $request->GTNJobNo,
            'RefCode'             => $request->RefCode,
            'OwnerOldSte'         => $request->OwnerOldSte,
            'SiteCode'            => $request->SiteCode,
            'SiteNAME_T'          => $request->SiteNAME_T,

            //  'PlanType' => $request->PlanType,
            'Region_id'           => $request->Region_id,
            'Province'            => $request->Province,

            //  'SiteType' => $request->SiteType,
            'Towerheight'         => $request->Towerheight,

            // 'TowerNewSite' => $request->TowerNewSite,
            // 'Tower' => $request->Tower,
            // 'Zone' => $request->Zone,

            // INVOICE
            'Quotation_IN'        => $request->Quotation_IN,
            'PO_No_IN'            => $request->PO_No_IN,
            'PO_Amount_IN'        => $request->PO_Amount_IN,

            // Civil Design
            'Design_Amount'       => $request->Design_Amount,
            'Invoice1_IN'         => $request->Invoice1_IN,
            'Amount1_IN'          => $request->Amount1_IN,
            'Invoice2_IN'         => $request->Invoice2_IN,
            'Amount2_IN'          => $request->Amount2_IN,

            // Civil Construction
            'Construction_Amount' => $request->Construction_Amount,
            'Invoice1_CC'         => $request->Invoice1_CC,
            'Amount1_CC'          => $request->Amount1_CC,
            'Invoice2_CC'         => $request->Invoice2_CC,
            'Amount2_CC'          => $request->Amount2_CC,

            'Banlace_IN'          => number_format($Banlace_IN, 2, '.', ''),

        ];

        // แปลงค่าเป็นตัวเลขก่อนการคำนวณ BALACE

        $accept_1st_saq = floatval($request->Accept_1st_SAQ);
        $accept_2nd_saq = floatval($request->Accept_2nd_SAQ);
        $accept_3rd_saq = floatval($request->Accept_3rd_SAQ);
        $accept_4th_saq = floatval($request->Accept_4th_SAQ);
        $wo_price_saq   = floatval($request->WO_Price_SAQ);

        // คำนวณผลรวม
        $total      = $accept_1st_saq + $accept_2nd_saq + $accept_3rd_saq + $accept_4th_saq;
        $banlace    = $request->WO_Price_SAQ;
        $difference = ($banlace - $total);

        if (empty($banlace)) {
            $banlace_saq = null;
        } elseif ($total > $banlace) {
            $banlace_saq = $difference;
        } else {
            $banlace_saq = $difference;
        }

        $data_SAQ = [
            'AssignedSubCSurveySAQ' => $request->AssignedSubCSurveySAQ,
            'PlanSurveySAQ'         => $request->PlanSurveySAQ,
            'ActualSurveySAQ'       => $request->ActualSurveySAQ,
            'SubName_SAQ'           => $request->SubName_SAQ,
            'Quo_No_SAQ'            => $request->Quo_No_SAQ,
            'PR_Price_SAQ'          => $request->PR_Price_SAQ,
            'Accept_PR_Date_SAQ'    => $request->Accept_PR_Date_SAQ,
            'WO_No_SAQ'             => $request->WO_No_SAQ,
            'WO_Price_SAQ'          => $request->WO_Price_SAQ,

            'Accept_1st_SAQ'        => $request->Accept_1st_SAQ,
            'Mail_1st_SAQ'          => $request->Mail_1st_SAQ, // วันที่ในรูปแบบ d-m-Y
            'ERP_1st_SAQ'           => $request->ERP_1st_SAQ,  // วันที่ในรูปแบบ d-m-Y

            'Accept_2nd_SAQ'        => $request->Accept_2nd_SAQ,
            'Mail_2nd_SAQ'          => $request->Mail_2nd_SAQ, // วันที่ในรูปแบบ d-m-Y
            'ERP_2nd_SAQ'           => $request->ERP_2nd_SAQ,  // วันที่ในรูปแบบ d-m-Y

            'Accept_3rd_SAQ'        => $request->Accept_3rd_SAQ,
            'Mail_3rd_SAQ'          => $request->Mail_3rd_SAQ, // วันที่ในรูปแบบ d-m-Y
            'ERP_3rd_SAQ'           => $request->ERP_3rd_SAQ,  // วันที่ในรูปแบบ d-m-Y

            'Accept_4th_SAQ'        => $request->Accept_4th_SAQ,
            'Mail_4th_SAQ'          => $request->Mail_4th_SAQ, // วันที่ในรูปแบบ d-m-Y
            'ERP_4th_SAQ'           => $request->ERP_4th_SAQ,  // วันที่ในรูปแบบ d-m-Y

            'Banlace_SAQ'           => number_format($banlace_saq, 2, '.', ''),

        ];

        // แปลงค่าเป็นตัวเลขก่อนการคำนวณ BALACE

        $accept_1st_cr = floatval($request->Accept_1st_CR);
        $accept_2nd_cr = floatval($request->Accept_2nd_CR);
        $accept_3rd_cr = floatval($request->Accept_3rd_CR);
        $accept_4th_cr = floatval($request->Accept_4th_CR);
        $wo_price_cr   = floatval($request->WO_Price_CR);

        // คำนวณผลรวม
        $total      = $accept_1st_cr + $accept_2nd_cr + $accept_3rd_cr + $accept_4th_cr;
        $banlace    = $request->WO_Price_CR;
        $difference = ($banlace - $total);

        if (empty($banlace)) {
            $banlace_cr = null;
        } elseif ($total > $banlace) {
            $banlace_cr = $difference;
        } else {
            $banlace_cr = $difference;
        }

        // -- CR -->
        $data_CR = [
            'AssignedSubCCR'    => $request->AssignedSubCCR, //Date
            'PlanCR'            => $request->PlanCR,         //Date
            'ActualCR'          => $request->ActualCR,       //Date
            'SubName_CR'        => $request->SubName_CR,
            'Quo_No_CR'         => $request->Quo_No_CR,
            'PR_Price_CR'       => $request->PR_Price_CR,
            'Accept_PR_Date_CR' => $request->Accept_PR_Date_CR, //Date
            'WO_No_CR'          => $request->WO_No_CR,
            'WO_Price_CR'       => $request->WO_Price_CR,

            'Accept_1st_CR'     => $request->Accept_1st_CR,
            'Mail_1st_CR'       => $request->Mail_1st_CR, // วันที่ในรูปแบบ d-m-Y
            'ERP_1st_CR'        => $request->ERP_1st_CR,

            'Accept_2nd_CR'     => $request->Accept_2nd_CR,
            'Mail_2nd_CR'       => $request->Mail_2nd_CR, // วันที่ในรูปแบบ d-m-Y
            'ERP_2nd_CR'        => $request->ERP_2nd_CR,

            'Accept_3rd_CR'     => $request->Accept_3rd_CR,
            'Mail_3rd_CR'       => $request->Mail_3rd_CR, // วันที่ในรูปแบบ d-m-Y
            'ERP_3rd_CR'        => $request->ERP_3rd_CR,

            'Accept_4th_CR'     => $request->Accept_4th_CR,
            'Mail_4th_CR'       => $request->Mail_4th_CR,
            'ERP_4th_CR'        => $request->ERP_4th_CR, //Date

            'Banlace_CR'        => number_format($banlace_cr, 2, '.', ''),

        ];

        // แปลงค่าเป็นตัวเลขก่อนการคำนวณ BALACE

        // -- TSSR -->

        $accept_1st_TSSR = floatval($request->Accept_1st_TSSR);
        $accept_2nd_TSSR = floatval($request->Accept_2nd_TSSR);
        $accept_3rd_TSSR = floatval($request->Accept_3rd_TSSR);
        $accept_4th_TSSR = floatval($request->Accept_4th_TSSR);
        $wo_price_TSSR   = floatval($request->WO_Price_TSSR);

        // คำนวณผลรวม
        $total      = $accept_1st_TSSR + $accept_2nd_TSSR + $accept_3rd_TSSR + $accept_4th_TSSR;
        $banlace    = $request->WO_Price_TSSR;
        $difference = ($banlace - $total);

        if (empty($banlace)) {
            $banlace_TSSR = null;
        } elseif ($total > $banlace) {
            $banlace_TSSR = $difference;
        } else {
            $banlace_TSSR = $difference;
        }

        $data_TSSR = [
            'AssignedSubCTSSR'    => $request->AssignedSubCTSSR, //Date
            'PlanTSSR'            => $request->PlanTSSR,         //Date
            'ActualTSSR'          => $request->ActualTSSR,       //Date
            'SubName_TSSR'        => $request->SubName_TSSR,
            'Quo_No_TSSR'         => $request->Quo_No_TSSR,
            'PR_Price_TSSR'       => $request->PR_Price_TSSR,
            'Accept_PR_Date_TSSR' => $request->Accept_PR_Date_TSSR, //Date
            'WO_No_TSSR'          => $request->WO_No_TSSR,
            'WO_Price_TSSR'       => $request->WO_Price_TSSR,

            'Accept_1st_TSSR'     => $request->Accept_1st_TSSR,
            'Mail_1st_TSSR'       => $request->Mail_1st_TSSR, // วันที่ในรูปแบบ d-m-Y
            'ERP_1st_TSSR'        => $request->ERP_1st_TSSR,

            'Accept_2nd_TSSR'     => $request->Accept_2nd_TSSR,
            'Mail_2nd_TSSR'       => $request->Mail_2nd_TSSR, // วันที่ในรูปแบบ d-m-Y
            'ERP_2nd_TSSR'        => $request->ERP_2nd_TSSR,

            'Accept_3rd_TSSR'     => $request->Accept_3rd_TSSR,
            'Mail_3rd_TSSR'       => $request->Mail_3rd_TSSR, // วันที่ในรูปแบบ d-m-Y
            'ERP_3rd_TSSR'        => $request->ERP_3rd_TSSR,

            'Accept_4th_TSSR'     => $request->Accept_4th_TSSR,
            'Mail_4th_TSSR'       => $request->Mail_4th_TSSR,
            'ERP_4th_TSSR'        => $request->ERP_4th_TSSR,

            'Banlace_TSSR'        => number_format($banlace_TSSR, 2, '.', ''),
        ];

        // แปลงค่าเป็นตัวเลขก่อนการคำนวณ BALACE

        // CivilWork

        $accept_1st_CivilWork = floatval($request->Accept_1st_CivilWork);
        $accept_2nd_CivilWork = floatval($request->Accept_2nd_CivilWork);
        $accept_3rd_CivilWork = floatval($request->Accept_3rd_CivilWork);
        $accept_4th_CivilWork = floatval($request->Accept_4th_CivilWork);
        $wo_price_CivilWork   = floatval($request->WO_Price_CivilWork);

        // คำนวณผลรวม
        $total      = $accept_1st_CivilWork + $accept_2nd_CivilWork + $accept_3rd_CivilWork + $accept_4th_CivilWork;
        $banlace    = $request->WO_Price_CivilWork;
        $difference = ($banlace - $total);

        if (empty($banlace)) {
            $banlace_CivilWork = null;
        } elseif ($total > $banlace) {
            $banlace_CivilWork = $difference;
        } else {
            $banlace_CivilWork = $difference;
        }

        $data_CW = [
            'AssignSubCivilfoundation'    => $request->AssignSubCivilfoundation,
            'PlanCivilWorkFoundation'     => $request->PlanCivilWorkFoundation,
            'ActualCivilWorkTower'        => $request->ActualCivilWorkTower,
            'AssignCivilWorkTower'        => $request->AssignCivilWorkTower,
            'PlanInstallationRectifier'   => $request->PlanInstallationRectifier,
            'ActualInstallationRectifier' => $request->ActualInstallationRectifier,
            'PlanACPower'                 => $request->PlanACPower,
            'ActualACPower'               => $request->ActualACPower,
            'PlanACMeter'                 => $request->PlanACMeter,
            'ActualACMeter'               => $request->ActualACMeter,
            'PAT'                         => $request->PAT,
            'DefPAT'                      => $request->DefPAT,
            'FAT'                         => $request->FAT,

            'Assigned_CivilWork'          => $request->Assigned_CivilWork, // วันที่ในรูปแบบ d-m-Y
            'Plan_CivilWork'              => $request->Plan_CivilWork,     // วันที่ในรูปแบบ d-m-Y
            'Actual_CivilWork'            => $request->Actual_CivilWork,   // วันที่ในรูปแบบ d-m-Y
            'SubName_CivilWork'           => $request->SubName_CivilWork,
            'Quo_No_CivilWork'            => $request->Quo_No_CivilWork,
            'PR_Price_CivilWork'          => $request->PR_Price_CivilWork,       //กรอกเลข 0.00
            'Accept_PR_Date_CivilWork'    => $request->Accept_PR_Date_CivilWork, // วันที่ในรูปแบบ d-m-Y
            'WO_No_CivilWork'             => $request->WO_No_CivilWork,          //กรอกเลข 0.00
            'WO_Price_CivilWork'          => $request->WO_Price_CivilWork,       //กรอกเลข 0.00

            'Accept_1st_CivilWork'        => $request->Accept_1st_CivilWork, //กรอกเลข 0.00
            'Mail_1st_CivilWork'          => $request->Mail_1st_CivilWork,   // วันที่ในรูปแบบ d-m-Y
            'ERP_1st_CivilWork'           => $request->ERP_1st_CivilWork,    // วันที่ในรูปแบบ d-m-Y

            'Accept_2nd_CivilWork'        => $request->Accept_2nd_CivilWork, //กรอกเลข 0.00
            'Mail_2nd_CivilWork'          => $request->Mail_2nd_CivilWork,   // วันที่ในรูปแบบ d-m-Y
            'ERP_2nd_CivilWork'           => $request->ERP_2nd_CivilWork,    // วันที่ในรูปแบบ d-m-Y

            'Accept_3rd_CivilWork'        => $request->Accept_3rd_CivilWork, //กรอกเลข 0.00
            'Mail_3rd_CivilWork'          => $request->Mail_3rd_CivilWork,   // วันที่ในรูปแบบ d-m-Y
            'ERP_3rd_CivilWork'           => $request->ERP_3rd_CivilWork,    // วันที่ในรูปแบบ d-m-Y

            'Accept_4th_CivilWork'        => $request->Accept_4th_CivilWork, //กรอกเลข 0.00
            'Mail_4th_CivilWork'          => $request->Mail_4th_CivilWork,   // วันที่ในรูปแบบ d-m-Y
            'ERP_4th_CivilWork'           => $request->ERP_4th_CivilWork,    // วันที่ในรูปแบบ d-m-Y

            'Banlace_CivilWork'           => number_format($banlace_CivilWork, 2, '.', ''),

        ];

        $additional = [
            'pile_supplier'             => $request->pile_supplier,
            'price'                     => $request->price,                     // number
            'pile_supplier_accept_date' => $request->pile_supplier_accept_date, // date
            'wo_no'                     => $request->wo_no,
            'accept_1'                  => $request->accept_1, // date
            'accept_2'                  => $request->accept_2, // date
            'accept_3'                  => $request->accept_3, // date
            'sub_extra_work'            => $request->sub_extra_work,
            'sub_extra_work_price'      => $request->sub_extra_work_price,   // number
            'extra_work_accept_date'    => $request->extra_work_accept_date, // date
            'build_permit'              => $request->build_permit,           // number
            'payment_to'                => $request->payment_to,
            'payment_date'              => $request->payment_date, // date
        ];

        //dd($data_MAIN);

        try {

            // เริ่ม transaction
            DB::beginTransaction();

            // รับข้อมูลเก่าจากฐานข้อมูลเพื่อตรวจสอบการเปลี่ยนแปลง

            $main = DB::table("main_csv")->where("id", $id)->first();
            $saq  = DB::table("saq_csv")->where("id_saq", $id)->first();
            $cr   = DB::table("cr_csv")->where("id_cr", $id)->first();
            $tssr = DB::table("tssr_csv")->where("id_tssr", $id)->first();
            $cw   = DB::table("civilwork_csv")->where("id_cw", $id)->first();
            $add  = DB::table("additional_csv")->where("id_add", $id)->first();
            // dd($data_MAIN, $data_SAQ, $data_CR, $data_TSSR, $data_CW);

            // อัปเดตข้อมูลในตาราง main_csv โดยใช้ $id ที่มีอยู่แล้ว
            DB::table('main_csv')->where('id', $id)->update($data_MAIN);
            // อัปเดตข้อมูลในตาราง saq_csv โดยใช้ id_saq
            DB::table("saq_csv")->where("id_saq", $id)->update($data_SAQ);
            // อัปเดตข้อมูลในตาราง cr_csv โดยใช้ id_cr
            DB::table("cr_csv")->where("id_cr", $id)->update($data_CR);
            // อัปเดตข้อมูลในตาราง tssr_csv โดยใช้ id_tssr
            DB::table("tssr_csv")->where("id_tssr", $id)->update($data_TSSR);
            // อัปเดตข้อมูลในตาราง civilwork_csv โดยใช้ id_cw
            DB::table("civilwork_csv")->where("id_cw", $id)->update($data_CW);
            // อัปเดตข้อมูลในตาราง civilwork_csv โดยใช้ id_Add
            DB::table("additional_csv")->where("id_add", $id)->update($additional);
            // Commit transaction หลังจากอัปเดตสำเร็จ
            DB::commit();

                                       // ใช้ Font Awesome หรือ Emoji สำหรับไอคอน
            $userIcon   = "&#128100;"; // 👤 Unicode Entity หรือใช้ '<i class="fas fa-user"></i>'
            $linkIcon   = "🔗";      // 🔗 Unicode Entity หรือใช้ '<i class="fas fa-link"></i>'
            $bulletSite = "🟢";      // ใช้สัญลักษณ์จุดสีเขียว
            $bulletRef  = "🟠";      // ใช้สัญลักษณ์จุดสีส้ม
            $redBullet  = "🔴";

            $name = Auth::user()->name; // ดึงชื่อของผู้ใช้ที่เข้าสู่ระบบ

            $link     = '<a href="' . url('https://homeofficegtn.com/blog') . '">https://homeofficegtn.com/Tracking</a>';
            $refcode  = '<a href="' . url('https://homeofficegtn.com/refcode/home') . '">https://homeofficegtn.com/Refcode</a>';
            $itclinic = '<a href="' . url('https://sites.google.com/team-gtn.com/it-clinic/home') . '">https://homeofficegtn.com/Clinic</a>';

                                                                            // สีที่ใช้
            $yellowBullet      = "<span style='color:#eaff01;'>●</span>"; // INVOICE
            $lightBlueBullet   = "<span style='color:#D1E9F6;'>●</span>"; //  SAQ
            $blueBullet        = "<span style='color:#29b6f6;'>●</span>"; // CR
            $lightYellowBullet = "<span style='color:#fff176;'>●</span>"; // TSSR
            $greenBullet       = "<span style='color:#00DFA2;'>●</span>"; // CIVIL WORK

            // ✅ เริ่มสร้างข้อความ
            $message = $userIcon . " User : " . $name . " Update " . "<br>" .
            "$bulletRef RefCode : " . $request->input('RefCode') . "<br>" .
            "$bulletSite SiteCode : " . $request->input('SiteCode') . "<br>" .
            "$redBullet SiteName : " . $request->input('SiteNAME_T') . "<br><br>";

            // ตรวจสอบว่ามีค่าหรือไม่ก่อนจะเพิ่มเข้าไปในข้อความ INVOICE Civil Design

            if ($request->filled('PO_No_IN') && $request->input('PO_No_IN') != $main->PO_No_IN) {
                $message .= "$yellowBullet PO No : " . $request->input('PO_No_IN') . "<br>";
            }

            if ($request->filled('Invoice1_IN') && $request->input('Invoice1_IN') != $main->Invoice1_IN) {
                $message .= "$yellowBullet Invoice Civil Design 1 : " . $request->input('Invoice1_IN') . "<br>";
            }

            if ($request->filled('Amount1_IN') && $request->input('Amount1_IN') != $main->Amount1_IN) {
                $message .= "$yellowBullet Amount Civil Design 1 : " . $request->input('Amount1_IN') . "<br>";
            }

            if ($request->filled('Invoice2_IN') && $request->input('Invoice2_IN') != $main->Invoice2_IN) {
                $message .= "$yellowBullet Invoice Civil Design 2 : " . $request->input('Invoice2_IN') . "<br>";
            }

            if ($request->filled('Amount2_IN') && $request->input('Amount2_IN') != $main->Amount2_IN) {
                $message .= "$yellowBullet Amount Civil Design 2 : " . $request->input('Amount2_IN') . "<br>";
            }

            // ตรวจสอบว่ามีค่าหรือไม่ก่อนจะเพิ่มเข้าไปในข้อความ INVOICE Civil Construction

            if ($request->filled('Invoice1_CC') && $request->input('Invoice1_CC') != $main->Invoice1_CC) {
                $message .= "$yellowBullet Invoice Civil Construction 1 : " . $request->input('Invoice1_CC') . "<br>";
            }

            if ($request->filled('Amount1_CC') && $request->input('Amount1_CC') != $main->Amount1_CC) {
                $message .= "$yellowBullet Amount Civil Construction 1 : " . $request->input('Amount1_CC') . "<br>";
            }

            if ($request->filled('Invoice2_CC') && $request->input('Invoice2_CC') != $main->Invoice2_CC) {
                $message .= "$yellowBullet Invoice Civil Construction 2 : " . $request->input('Invoice2_CC') . "<br>";
            }

            if ($request->filled('Amount2_CC') && $request->input('Amount2_CC') != $main->Amount2_CC) {
                $message .= "$yellowBullet Amount Civil Construction 2 : " . $request->input('Amount2_CC') . "<br>";
            }

            // ตรวจสอบว่ามีค่าหรือไม่ก่อนจะเพิ่มเข้าไปในข้อความ  SAQ

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 1
            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 1
            if ($request->filled('Accept_1st_SAQ') && $request->input('Accept_1st_SAQ') != $saq->Accept_1st_SAQ) {
                $message .= "$lightBlueBullet Accept 1st SAQ: " . $request->input('Accept_1st_SAQ') . "<br>";
            }

            if ($request->filled('Mail_1st_SAQ') && $request->input('Mail_1st_SAQ') != $saq->Mail_1st_SAQ) {
                $message .= "$lightBlueBullet Mail 1st SAQ: " . $request->input('Mail_1st_SAQ') . "<br>";
            }

            if ($request->filled('ERP_1st_SAQ') && $request->input('ERP_1st_SAQ') != $saq->ERP_1st_SAQ) {
                $message .= "$lightBlueBullet ERP 1st SAQ: " . $request->input('ERP_1st_SAQ') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 2
            if ($request->filled('Accept_2nd_SAQ') && $request->input('Accept_2nd_SAQ') != $saq->Accept_2nd_SAQ) {
                $message .= "$lightBlueBullet Accept 2nd SAQ: " . $request->input('Accept_2nd_SAQ') . "<br>";
            }

            if ($request->filled('Mail_2nd_SAQ') && $request->input('Mail_2nd_SAQ') != $saq->Mail_2nd_SAQ) {
                $message .= "$lightBlueBullet Mail 2nd SAQ: " . $request->input('Mail_2nd_SAQ') . "<br>";
            }

            if ($request->filled('ERP_2nd_SAQ') && $request->input('ERP_2nd_SAQ') != $saq->ERP_2nd_SAQ) {
                $message .= "$lightBlueBullet ERP 2nd SAQ: " . $request->input('ERP_2nd_SAQ') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 3
            if ($request->filled('Accept_3rd_SAQ') && $request->input('Accept_3rd_SAQ') != $saq->Accept_3rd_SAQ) {
                $message .= "$lightBlueBullet Accept 3rd SAQ: " . $request->input('Accept_3rd_SAQ') . "<br>";
            }

            if ($request->filled('Mail_3rd_SAQ') && $request->input('Mail_3rd_SAQ') != $saq->Mail_3rd_SAQ) {
                $message .= "$lightBlueBullet Mail 3rd SAQ: " . $request->input('Mail_3rd_SAQ') . "<br>";
            }

            if ($request->filled('ERP_3rd_SAQ') && $request->input('ERP_3rd_SAQ') != $saq->ERP_3rd_SAQ) {
                $message .= "$lightBlueBullet ERP 3rd SAQ: " . $request->input('ERP_3rd_SAQ') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 4
            if ($request->filled('Accept_4th_SAQ') && $request->input('Accept_4th_SAQ') != $saq->Accept_4th_SAQ) {
                $message .= "$lightBlueBullet Accept 4th SAQ: " . $request->input('Accept_4th_SAQ') . "<br>";
            }

            if ($request->filled('Mail_4th_SAQ') && $request->input('Mail_4th_SAQ') != $saq->Mail_4th_SAQ) {
                $message .= "$lightBlueBullet Mail 4th SAQ: " . $request->input('Mail_4th_SAQ') . "<br>";
            }

            if ($request->filled('ERP_4th_SAQ') && $request->input('ERP_4th_SAQ') != $saq->ERP_4th_SAQ) {
                $message .= "$lightBlueBullet ERP 4th SAQ: " . $request->input('ERP_4th_SAQ') . "<br>";
            }

            // ตรวจสอบว่ามีค่าหรือไม่ก่อนจะเพิ่มเข้าไปในข้อความ  CR
            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 1
            if ($request->filled('Accept_1st_CR') && $request->input('Accept_1st_CR') != $cr->Accept_1st_CR) {
                $message .= "$blueBullet Accept 1st CR: " . $request->input('Accept_1st_CR') . "<br>";
            }

            if ($request->filled('Mail_1st_CR') && $request->input('Mail_1st_CR') != $cr->Mail_1st_CR) {
                $message .= "$blueBullet Mail 1st CR: " . $request->input('Mail_1st_CR') . "<br>";
            }

            if ($request->filled('ERP_1st_CR') && $request->input('ERP_1st_CR') != $cr->ERP_1st_CR) {
                $message .= "$blueBullet ERP 1st CR: " . $request->input('ERP_1st_CR') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 2
            if ($request->filled('Accept_2nd_CR') && $request->input('Accept_2nd_CR') != $cr->Accept_2nd_CR) {
                $message .= "$blueBullet Accept 2nd CR: " . $request->input('Accept_2nd_CR') . "<br>";
            }

            if ($request->filled('Mail_2nd_CR') && $request->input('Mail_2nd_CR') != $cr->Mail_2nd_CR) {
                $message .= "$blueBullet Mail 2nd CR: " . $request->input('Mail_2nd_CR') . "<br>";
            }

            if ($request->filled('ERP_2nd_CR') && $request->input('ERP_2nd_CR') != $cr->ERP_2nd_CR) {
                $message .= "$blueBullet ERP 2nd CR: " . $request->input('ERP_2nd_CR') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 3
            if ($request->filled('Accept_3rd_CR') && $request->input('Accept_3rd_CR') != $cr->Accept_3rd_CR) {
                $message .= "$blueBullet Accept 3rd CR: " . $request->input('Accept_3rd_CR') . "<br>";
            }

            if ($request->filled('Mail_3rd_CR') && $request->input('Mail_3rd_CR') != $cr->Mail_3rd_CR) {
                $message .= "$blueBullet Mail 3rd CR: " . $request->input('Mail_3rd_CR') . "<br>";
            }

            if ($request->filled('ERP_3rd_CR') && $request->input('ERP_3rd_CR') != $cr->ERP_3rd_CR) {
                $message .= "$blueBullet ERP 3rd CR: " . $request->input('ERP_3rd_CR') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 4
            if ($request->filled('Accept_4th_CR') && $request->input('Accept_4th_CR') != $cr->Accept_4th_CR) {
                $message .= "$blueBullet Accept 4th CR: " . $request->input('Accept_4th_CR') . "<br>";
            }

            if ($request->filled('Mail_4th_CR') && $request->input('Mail_4th_CR') != $cr->Mail_4th_CR) {
                $message .= "$blueBullet Mail 4th CR: " . $request->input('Mail_4th_CR') . "<br>";
            }

            if ($request->filled('ERP_4th_CR') && $request->input('ERP_4th_CR') != $cr->ERP_4th_CR) {
                $message .= "$blueBullet ERP 4th CR: " . $request->input('ERP_4th_CR') . "<br>";
            }

            // ตรวจสอบว่ามีค่าหรือไม่ก่อนจะเพิ่มเข้าไปในข้อความ  TSRR
            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 1
            if ($request->filled('Accept_1st_TSSR') && $request->input('Accept_1st_TSSR') != $tssr->Accept_1st_TSSR) {
                $message .= "$lightYellowBullet Accept 1st TSSR: " . $request->input('Accept_1st_TSSR') . "<br>";
            }

            if ($request->filled('Mail_1st_TSSR') && $request->input('Mail_1st_TSSR') != $tssr->Mail_1st_TSSR) {
                $message .= "$lightYellowBullet Mail 1st TSSR: " . $request->input('Mail_1st_TSSR') . "<br>";
            }

            if ($request->filled('ERP_1st_TSSR') && $request->input('ERP_1st_TSSR') != $tssr->ERP_1st_TSSR) {
                $message .= "$lightYellowBullet ERP 1st TSSR: " . $request->input('ERP_1st_TSSR') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 2
            if ($request->filled('Accept_2nd_TSSR') && $request->input('Accept_2nd_TSSR') != $tssr->Accept_2nd_TSSR) {
                $message .= "$lightYellowBullet Accept 2nd TSSR: " . $request->input('Accept_2nd_TSSR') . "<br>";
            }

            if ($request->filled('Mail_2nd_TSSR') && $request->input('Mail_2nd_TSSR') != $tssr->Mail_2nd_TSSR) {
                $message .= "$lightYellowBullet Mail 2nd TSSR: " . $request->input('Mail_2nd_TSSR') . "<br>";
            }

            if ($request->filled('ERP_2nd_TSSR') && $request->input('ERP_2nd_TSSR') != $tssr->ERP_2nd_TSSR) {
                $message .= "$lightYellowBullet ERP 2nd TSSR: " . $request->input('ERP_2nd_TSSR') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 3
            if ($request->filled('Accept_3rd_TSSR') && $request->input('Accept_3rd_TSSR') != $tssr->Accept_3rd_TSSR) {
                $message .= "$lightYellowBullet Accept 3rd TSSR: " . $request->input('Accept_3rd_TSSR') . "<br>";
            }

            if ($request->filled('Mail_3rd_TSSR') && $request->input('Mail_3rd_TSSR') != $tssr->Mail_3rd_TSSR) {
                $message .= "$lightYellowBullet Mail 3rd TSSR: " . $request->input('Mail_3rd_TSSR') . "<br>";
            }

            if ($request->filled('ERP_3rd_TSSR') && $request->input('ERP_3rd_TSSR') != $tssr->ERP_3rd_TSSR) {
                $message .= "$lightYellowBullet ERP 3rd TSSR: " . $request->input('ERP_3rd_TSSR') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 4
            if ($request->filled('Accept_4th_TSSR') && $request->input('Accept_4th_TSSR') != $tssr->Accept_4th_TSSR) {
                $message .= "$lightYellowBullet Accept 4th TSSR: " . $request->input('Accept_4th_TSSR') . "<br>";
            }

            if ($request->filled('Mail_4th_TSSR') && $request->input('Mail_4th_TSSR') != $tssr->Mail_4th_TSSR) {
                $message .= "$lightYellowBullet Mail 4th TSSR: " . $request->input('Mail_4th_TSSR') . "<br>";
            }

            if ($request->filled('ERP_4th_TSSR') && $request->input('ERP_4th_TSSR') != $tssr->ERP_4th_TSSR) {
                $message .= "$lightYellowBullet ERP 4th TSSR: " . $request->input('ERP_4th_TSSR') . "<br>";
            }

            // ตรวจสอบว่ามีค่าหรือไม่ก่อนจะเพิ่มเข้าไปในข้อความ  CIVILWORK
            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 1
            if ($request->filled('Accept_1st_CivilWork') && $request->input('Accept_1st_CivilWork') != $cw->Accept_1st_CivilWork) {
                $message .= "$greenBullet Accept 1st Civil Work: " . $request->input('Accept_1st_CivilWork') . "<br>";
            }

            if ($request->filled('Mail_1st_CivilWork') && $request->input('Mail_1st_CivilWork') != $cw->Mail_1st_CivilWork) {
                $message .= "$greenBullet Mail 1st Civil Work: " . $request->input('Mail_1st_CivilWork') . "<br>";
            }

            if ($request->filled('ERP_1st_CivilWork') && $request->input('ERP_1st_CivilWork') != $cw->ERP_1st_CivilWork) {
                $message .= "$greenBullet ERP 1st Civil Work: " . $request->input('ERP_1st_CivilWork') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 2
            if ($request->filled('Accept_2nd_CivilWork') && $request->input('Accept_2nd_CivilWork') != $cw->Accept_2nd_CivilWork) {
                $message .= "$greenBullet Accept 2nd CivilWork: " . $request->input('Accept_2nd_CivilWork') . "<br>";
            }

            if ($request->filled('Mail_2nd_CivilWork') && $request->input('Mail_2nd_CivilWork') != $cw->Mail_2nd_CivilWork) {
                $message .= "$greenBullet Mail 2nd Civil Work: " . $request->input('Mail_2nd_CivilWork') . "<br>";
            }

            if ($request->filled('ERP_2nd_CivilWork') && $request->input('ERP_2nd_CivilWork') != $cw->ERP_2nd_CivilWork) {
                $message .= "$greenBullet ERP 2nd Civil Work: " . $request->input('ERP_2nd_CivilWork') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 3
            if ($request->filled('Accept_3rd_CivilWork') && $request->input('Accept_3rd_CivilWork') != $cw->Accept_3rd_CivilWork) {
                $message .= "$greenBullet Accept 3rd CivilWork: " . $request->input('Accept_3rd_CivilWork') . "<br>";
            }

            if ($request->filled('Mail_3rd_CivilWork') && $request->input('Mail_3rd_CivilWork') != $cw->Mail_3rd_CivilWork) {
                $message .= "$greenBullet Mail 3rd Civil Work: " . $request->input('Mail_3rd_CivilWork') . "<br>";
            }

            if ($request->filled('ERP_3rd_CivilWork') && $request->input('ERP_3rd_CivilWork') != $cw->ERP_3rd_CivilWork) {
                $message .= "$greenBullet ERP 3rd Civil Work: " . $request->input('ERP_3rd_CivilWork') . "<br>";
            }

            // ตรวจสอบข้อมูลใหม่กับข้อมูลเก่าก่อนแจ้งเตือน  ACCEPT 4
            if ($request->filled('Accept_4th_CivilWork') && $request->input('Accept_4th_CivilWork') != $cw->Accept_4th_CivilWork) {
                $message .= "$greenBullet Accept 4th CivilWork: " . $request->input('Accept_4th_CivilWork') . "<br>";
            }

            if ($request->filled('Mail_4th_CivilWork') && $request->input('Mail_4th_CivilWork') != $cw->Mail_4th_CivilWork) {
                $message .= "$greenBullet Mail 4th Civil Work: " . $request->input('Mail_4th_CivilWork') . "<br>";
            }

            if ($request->filled('ERP_4th_CivilWork') && $request->input('ERP_4th_CivilWork') != $cw->ERP_4th_CivilWork) {
                $message .= "$greenBullet ERP 4th Civil Work: " . $request->input('ERP_4th_CivilWork') . "<br>";
            }

            // ✅ เพิ่มลิงก์ต่างๆ
            $message .= "<br>" . $linkIcon . " Link Tracking : " . $link . "<br><br>" .
                $linkIcon . " Link Search RefCode : " . $refcode . "<br><br>" .
                $linkIcon . " Link IT Clinic : " . $itclinic . "<br><br>";

            // ดึงอีเมลจากฐานข้อมูลที่มี status = 4

            /*
            $emails = DB::table('users')
                ->where('status', 4) // เงื่อนไข status = 4
                ->pluck('email')     // ดึงแค่คอลัมน์ email
                ->toArray();         // เปลี่ยนผลลัพธ์เป็น array

            foreach ($emails as $email) {
                Mail::to($email)->send(new UpdateMail($message));
            }
			*/

            // ส่งข้อความ success กลับไปยังหน้าเดิม
            return back()->with('success', 'Updated successfully');
        } catch (\Exception $e) {
            // Rollback transaction หากเกิดข้อผิดพลาด
            DB::rollback();

            // จัดการกับข้อผิดพลาดที่เกิดขึ้นที่นี่ เช่นการแจ้งเตือน
            throw $e;
        }
    }

    // ฟังก์ชันสำหรับแปลงวันที่
    private function convertToDateFormat($date)
    {
        if ($date) {
            $dateObject = \DateTime::createFromFormat(format: 'd-m-Y', datetime: $date);
            if ($dateObject) {
                return $dateObject->format('d-m-Y');
            }
        }
        return null; // หรือค่าเริ่มต้นอื่น ๆ
    }

    public function add()
    {
        $areas = DB::table('area')->get();
        return view('add', compact("areas"));
    }

    // เพิ่ม Sitecode
    public function insert(Request $request)
    {

        $request->validate(
            [
                'SiteCode' => 'required',

            ],
            [
                'SiteCode' => 'กรุณากรอก SiteCode',
            ]
        );

        // Form MAIN

        $data_MAIN = [
            //  'GTNJobNo' => $request->GTNJobNo,
            'RefCode'     => $request->RefCode,
            'OwnerOldSte' => $request->OwnerOldSte,
            'SiteCode'    => $request->SiteCode,
            'SiteNAME_T'  => $request->SiteNAME_T,
            //'PlanType' => $request->PlanType,
            'Region_id'   => $request->Region_id,
            'Province'    => $request->Province,
            //'SiteType' => $request->SiteType,
            // 'CancelSite' => $request->CancelSite,
            // 'TowerNewSite' => $request->TowerNewSite,
            'Towerheight' => $request->Towerheight,
            //    'Tower' => $request->Tower,
            //    'Zone' => $request->Zone,
            //    'DeadLine' => $request->DeadLine,
            //    'DeadLine_Y' => $request->DeadLine_Y,
            //    'Status' => $request->Status,

        ];

        try {
            // แทรกข้อมูลลงใน main_csv และดึง ID ที่สร้างขึ้น
            $mainId = DB::table('main_csv')->insertGetId($data_MAIN);

            // สร้างข้อมูลสำหรับตาราง saq_csv โดยใช้ ID ที่ได้จาก main_csv
            $data_SAQ = [
                'id_saq' => $mainId, // ใช้ ID ที่ดึงมาจาก main_csv
            ];

            $data_CR = [

                'id_cr' => $mainId,
            ];

            $data_TSSR = [
                'id_tssr' => $mainId,
            ];

            $data_CivilWork = [
                'id_cw' => $mainId,
            ];

            $data_additional = [
                'id_add' => $mainId,
            ];

            // แทรกข้อมูลลงใน saq_csv
            DB::table('saq_csv')->insert($data_SAQ);
            DB::table('cr_csv')->insert($data_CR);
            DB::table('tssr_csv')->insert($data_TSSR);
            DB::table('civilwork_csv')->insert($data_CivilWork);
            DB::table('additional_csv')->insert($data_additional);

            //dd($mainId,$data_SAQ,$data_CR,$data_TSSR,$data_CivilWork,$data_additional);

            // Commit transaction
            DB::commit();

            $bulletSite = "🟢"; // ใช้สัญลักษณ์จุดสีเขียว
            $bulletRef  = "🟠"; // ใช้สัญลักษณ์จุดสีส้ม

            $name     = Auth::user()->name; // ดึงชื่อของผู้ใช้ที่เข้าสู่ระบบ
            $userIcon = "&#128100;";        // 👤 Unicode Entity หรือใช้ '<i class="fas fa-user"></i>'
            $linkIcon = "🔗";             // 🔗 Unicode Entity หรือใช้ '<i class="fas fa-link"></i>'

            $link     = '<a href="' . url('https://homeofficegtn.com/home') . '">https://homeofficegtn.com/home</a>';
            $refcode  = '<a href="' . url('https://homeofficegtn.com/refcode/home') . '">https://homeofficegtn.com/Refcode</a>';
            $itclinic = '<a href="' . url('https://sites.google.com/team-gtn.com/it-clinic/home') . '">https://homeofficegtn.com/Clinic</a>';

            $refcodeIN  = $request->RefCode;
            $siteCodeIN = $request->SiteCode;

            // สร้างข้อความที่ต้องการส่งไปยัง Mail
            $message = $userIcon . " User : " . $name . " เพิ่ม Refcode และ SiteCode" . "<br>"
                . "$bulletRef RefCode : " . $refcodeIN . "<br>"
                . "$bulletSite SiteCode : " . $siteCodeIN . "<br>"
                . "<br>" . $linkIcon . " Link Tracking : " . $link . "<br><br>"
                . $linkIcon . " Link Search Refcode : " . $refcode . "<br><br>"
                . $linkIcon . " Link IT Clinic : " . $itclinic . "<br><br>";

            // ส่งข้อความไปยัง MAIL
            // ดึงอีเมลจากฐานข้อมูลที่มี status = 4

            $emails = DB::table('users')
                ->where('status', 4) // เงื่อนไข status = 4
                ->pluck('email')     // ดึงแค่คอลัมน์ email
                ->toArray();         // เปลี่ยนผลลัพธ์เป็น array

            foreach ($emails as $email) {
                Mail::to($email)->send(new UpdateMail($message));
            }

            //dd($emails);
        } catch (\Exception $e) {
            // Rollback transaction if there is an error
            DB::rollback();
            // จัดการกับข้อผิดพลาดที่เกิดขึ้นที่นี่
            // เช่น การบันทึกข้อผิดพลาดหรือการแจ้งเตือน
            throw $e;
        }

        // เปลี่ยนเส้นทางหลังจากการบันทึกข้อมูลสำเร็จ
        return redirect('/blog')->with('success', 'Data add successfully!');
    }

    // ImportRefcode

    public function importrefcode(Request $request)
    {
        // ดึง RefCode ทั้งหมดจากฐานข้อมูล
        $existingRefcodes = DB::table('main_csv')->pluck('RefCode')->toArray();

        $areas = DB::table('area')->get();

        $data = DB::table('main_csv as m')
            ->leftJoin('saq_csv as s', 'm.id', '=', 's.id_saq')
            ->leftJoin('cr_csv as c', 'm.id', '=', 'c.id_cr')
            ->leftJoin('tssr_csv as t', 'm.id', '=', 't.id_tssr')
            ->leftJoin('civilwork_csv as cw', 'm.id', '=', 'cw.id_cw')
            ->get();

        // dd($data);

        $dataToSave = [];

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
                    if (! $isFirstRow) {
                        $refcode = isset($data[0]) ? trim($data[0]) : '';

                        $dataToSave[] = [
                            'RefCode'      => $refcode,
                            'OwnerOldSte'  => isset($data[1]) ? trim($data[1]) : '',
                            'SiteCode'     => isset($data[2]) ? trim($data[2]) : '',
                            'SiteNAME_T'   => isset($data[3]) ? trim($data[3]) : '',
                            //   'PlanType' => isset($data[4]) ? trim($data[4]) : '',
                            'Region_id'    => isset($data[4]) ? trim($data[4]) : '',
                            'Province'     => isset($data[5]) ? trim($data[5]) : '',
                            //   'SiteType' => isset($data[7]) ? trim($data[7]) : '',
                            'Towerheight'  => isset($data[6]) ? trim($data[6]) : '',
                                                                                     //  'TowerNewSite' => isset($data[8]) ? trim($data[8]) : '',
                                                                                     //  'Tower' => isset($data[10]) ? trim($data[10]) : '',
                                                                                     //  'Zone' => isset($data[11]) ? trim($data[11]) : '',
                            'exists_in_db' => in_array($refcode, $existingRefcodes), // เช็คว่ามีอยู่ในฐานข้อมูลไหม
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

        //dd( $dataToSave);

        return view('import', compact('data', 'areas', 'dataToSave'));
    }

    //SAVE IMPORT Refcode
    public function saveAdd(Request $request)
    {
        $dataToSave = json_decode($request->data_add, true);

        // ดึง RefCode ทั้งหมดจากฐานข้อมูล
        $existingRefcodes = DB::table('main_csv')->pluck('RefCode')->toArray();

        // เริ่มต้น transaction
        DB::beginTransaction();

        // ตรวจสอบว่า $dataToSave เป็น array และมีข้อมูล
        if (! is_array($dataToSave)) {
            return redirect('/blog')->withErrors(['error' => 'ข้อมูลไม่ถูกต้องหรือไม่มีข้อมูลที่จะบันทึก']);
        }

                       // Loop ผ่านแต่ละแถวใน $dataToSave
        $newData = []; // สำหรับเก็บข้อมูลที่ไม่ซ้ำกัน
        foreach ($dataToSave as $row) {
            // เช็คว่า refcode ซ้ำหรือไม่
            if (! in_array($row['RefCode'], $existingRefcodes)) {
                // ถ้า refcode ไม่ซ้ำกัน ก็เพิ่มข้อมูลใหม่
                $newData[] = [
                    'RefCode'      => $row['RefCode'] ?? null,
                    'OwnerOldSte'  => $row['OwnerOldSte'] ?? null,
                    'SiteCode'     => $row['SiteCode'] ?? null,
                    'SiteNAME_T'   => $row['SiteNAME_T'] ?? null,
                    'PlanType'     => $row['PlanType'] ?? null,
                    'Region_id'    => $row['Region_id'] ? (int) $row['Region_id'] : null, // ตรวจสอบ Region_id ก่อน insert
                    'Province'     => $row['Province'] ?? null,
                    'SiteType'     => $row['SiteType'] ?? null,
                    'Towerheight'  => $row['Towerheight'] ?? null,

                    'Quotation_IN' => $row['Quotation_IN'] ?? null,
                    'PO_No_IN'     => $row['PO_No_IN'] ?? null,
                    'PO_Amount_IN' => $row['PO_Amount_IN'] ?? null,

                    'Invoice1_IN'  => $row['Invoice1_IN'] ?? null,
                    'Amount1_IN'   => $row['Amount1_IN'] ?? null,
                    'Invoice2_IN'  => $row['Invoice2_IN'] ?? null,
                    'Amount2_IN'   => $row['Amount2_IN'] ?? null,

                    'Invoice1_CC'  => $row['Invoice1_CC'] ?? null,
                    'Amount1_CC'   => $row['Amount1_CC'] ?? null,
                    'Invoice2_CC'  => $row['Invoice2_CC'] ?? null,
                    'Amount2_CC'   => $row['Amount2_CC'] ?? null,

                    'Banlace_IN'   => $row['Banlace_IN'] ?? null,
                ];
            }
        }

        //dd($newData);

        // ถ้ามีข้อมูลที่ไม่ซ้ำกันให้ทำการบันทึก
        if (count($newData) > 0) {
            $insertedIds = [];

            foreach ($newData as $row) {
                // Insert ข้อมูล และดึง ID ที่ถูกสร้าง
                $mainId        = DB::table('main_csv')->insertGetId($row);
                $insertedIds[] = [
                    'RefCode'  => $row['RefCode'],
                    'SiteCode' => $row['SiteCode'],
                    'id'       => $mainId,
                ];

                // Insert ข้อมูลไปยังตารางอื่น ๆ โดยใช้ $mainId และดึงค่า ID ที่ถูกเพิ่ม
                $saqId  = DB::table('saq_csv')->insertGetId(['id_saq' => $mainId]);
                $crId   = DB::table('cr_csv')->insertGetId(['id_cr' => $mainId]);
                $tssrId = DB::table('tssr_csv')->insertGetId(['id_tssr' => $mainId]);
                $cwId   = DB::table('civilwork_csv')->insertGetId(['id_cw' => $mainId]);
                $addId  = DB::table('additional_csv')->insertGetId(['id_add' => $mainId]);

                $insertedIds[count($insertedIds) - 1] += [

                    'id_saq'  => $saqId,
                    'id_cr'   => $crId,
                    'id_tssr' => $tssrId,
                    'id_cw'   => $cwId,
                    'id_add'  => $addId,
                ];
            }

            // แสดงผล ID ที่ถูกสร้างขึ้นทั้งหมด
            //dd($insertedIds);

            DB::commit();

            $userIcon   = "&#128100;"; // 👤 Unicode Entity
            $linkIcon   = "🔗";      // 🔗 Unicode Entity
            $bulletSite = "🟢";      // ใช้สัญลักษณ์จุดสีเขียว
            $bulletRef  = "🟠";      // ใช้สัญลักษณ์จุดสีส้ม

            $name = Auth::user()->name; // ดึงชื่อของผู้ใช้ที่เข้าสู่ระบบ

            $link     = '<a href="' . url('https://homeofficegtn.com/home') . '">https://homeofficegtn.com/home</a>';
            $refcode  = '<a href="' . url('https://homeofficegtn.com/refcode/home') . '">https://homeofficegtn.com/Refcode</a>';
            $itclinic = '<a href="' . url('https://sites.google.com/team-gtn.com/it-clinic/home') . '">https://homeofficegtn.com/Clinic</a>';

            // สร้างข้อความเริ่มต้น
            $message = $userIcon . " User : " . $name . " Import<br>" .
                $bulletRef . " Refcode " . "  " . $bulletSite . " SiteCode " . " ดังนี้ :<br><br>";

            // วนลูปเพิ่ม SiteCode ที่ถูก import
            foreach ($insertedIds as $row) {
                $message .=
                    '<span style="display:inline-block; width:100px;">' . $bulletRef . " " . $row['RefCode'] . '</span>' .
                    '<span style="display:inline-block; width:100px;">' . $bulletSite . " " . $row['SiteCode'] . '</span>' . "<br>";
            }

            // เพิ่ม Link  ต่อท้ายข้อความ
            $message .= "<br>" .
                $linkIcon . " Link Tracking : " . $link . "<br><br>" .
                $linkIcon . " Link Search Refcode : " . $refcode . "<br><br>" .
                $linkIcon . " Link IT Clinic  : " . $itclinic . "<br><br>";

            $emails = DB::table('users')
                ->where('status', 4) // เงื่อนไข status = 4
                ->pluck('email')     // ดึงแค่คอลัมน์ email
                ->toArray();         // เปลี่ยนผลลัพธ์เป็น array

            foreach ($emails as $email) {
                Mail::to($email)->send(new UpdateMail($message));
            }

            return redirect('blog')->with('success', 'Import RefCode สำเร็จ');
        } else {
                            // ถ้าไม่มีข้อมูลใหม่ให้บันทึก
            DB::rollBack(); // ยกเลิกการทำธุรกรรม
            return redirect('blog')->with('error', 'ข้อมูล RefCode ซ้ำกัน');
        }
    }
}
