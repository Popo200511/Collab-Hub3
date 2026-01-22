<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use function Laravel\Prompts\table;

class TowerDismantleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = DB::table("towerdismantle_header as m")
            ->leftJoin('towerdismantle_estimatedprice as e', 'm.id', '=', 'e.id_est')
            ->leftJoin('towerdismantle_workingprogress as w', 'm.id', '=', 'w.id_work')
            ->leftJoin('towerdismantle_towerbuybackdata as tb', 'm.id', '=', 'tb.id_tower')
            ->leftJoin('towerdismantle_towerselingdata as ts', 'm.id', '=', 'ts.id_towersell')
            ->leftJoin('towerdismantle_revenuedetail_1 as r1', 'm.id', '=', 'r1.id_re1')
            ->leftJoin('towerdismantle_revenuedetail_2 as r2', 'm.id', '=', 'r2.id_re2')
            ->leftJoin('towerdismantle_revenuedetail_3 as r3', 'm.id', '=', 'r3.id_re3')
            ->leftJoin('towerdismantle_paymentdetail_1 as p1', 'm.id', '=', 'p1.id_pay1')
            ->leftJoin('towerdismantle_paymentdetail_2 as p2', 'm.id', '=', 'p2.id_pay2')
            ->leftJoin('towerdismantle_paymentdetail_3 as p3', 'm.id', '=', 'p3.id_pay3')
            ->get()
            ->map(function ($item) {
                // รายชื่อ field ที่ต้องแปลง
                $dateFields = [
                    // Working Progress
                    'work_JobAssignedDateByCustomer', 'work_PlanSurveyedDate', 'work_ActualSurveyedDate',
                    'work_CustomerCommittedDate', 'work_PlanStartedDate', 'work_ActualStartedDate',
                    'work_PlanFinishedDate', 'work_ActualFinishedDate',
    
                    // Tower Buyback Data
                    'tower_Towerbuyback_PlanPaidDate', 'tower_Towerbuyback_ActualPaidDate',
    
                    // Tower Selling Data
                    'towersell_PlanGetPaidDate', 'towersell_ActualGetPaidDate',
    
                    // Revenue 1
                    're1_Customer_QuotationSubbmittedDatePlan', 're1_Customer_QuotationSubbmittedDateActual',
                    're1_Customer_POReceivedDate', 're1_PlanInvoicePlacedDate', 're1_ActualInvoicePlacedDate',
                    're1_ConfirmedDueDate',
    
                    // Revenue 2
                    're2_Customer_QuotationSubbmittedDatePlan', 're2_Customer_QuotationSubbmittedDateActual',
                    're2_Customer_POReceivedDate', 're2_PlanInvoicePlacedDate', 're2_ActualInvoicePlacedDate',
                    're2_ConfirmedDueDate',
    
                    // Revenue 3
                    're3_Customer_QuotationSubbmittedDatePlan', 're3_Customer_QuotationSubbmittedDateActual',
                    're3_Customer_POReceivedDate', 're3_PlanInvoicePlacedDate', 're3_ActualInvoicePlacedDate',
                    're3_ConfirmedDueDate',
    
                    // Payment Detail 1-3
                    'pay1_PRrequestedDateEmail', 'pay1_PRApprovedDateEmail', 'pay1_PRIssuedDateERP', 'pay1_PRApprovedDateERP',
                    'pay1_WOIssueDateERP', 'pay1_WOApprovedDateERP', 'pay1_DatesentWOtoSubCEmail',
                    'pay1_BillingRequestedDateEmail', 'pay1_BillingApprovedDateEmail', 'pay1_BillingIssuedDateERP',
                    'pay1_BillingApprovedDateERP', 'pay1_DatesentBillingtoSubC', 'pay1_InvoicePlaceddatebySubC',
                    'pay1_PaymentconfirmedDateERP',
    
                    'pay2_PRrequestedDateEmail', 'pay2_PRApprovedDateEmail', 'pay2_PRIssuedDateERP', 'pay2_PRApprovedDateERP',
                    'pay2_WOIssueDateERP', 'pay2_WOApprovedDateERP', 'pay2_DatesentWOtoSubCEmail',
                    'pay2_BillingRequestedDateEmail', 'pay2_BillingApprovedDateEmail', 'pay2_BillingIssuedDateERP',
                    'pay2_BillingApprovedDateERP', 'pay2_DatesentBillingtoSubC', 'pay2_InvoicePlaceddatebySubC',
                    'pay2_PaymentconfirmedDateERP',
    
                    'pay3_PRrequestedDateEmail', 'pay3_PRApprovedDateEmail', 'pay3_PRIssuedDateERP', 'pay3_PRApprovedDateERP',
                    'pay3_WOIssueDateERP', 'pay3_WOApprovedDateERP', 'pay3_DatesentWOtoSubCEmail',
                    'pay3_BillingRequestedDateEmail', 'pay3_BillingApprovedDateEmail', 'pay3_BillingIssuedDateERP',
                    'pay3_BillingApprovedDateERP', 'pay3_DatesentBillingtoSubC', 'pay3_InvoicePlaceddatebySubC',
                    'pay3_PaymentconfirmedDateERP',
                ];
    
                foreach ($dateFields as $field) {
                    if (!empty($item->$field)) {
                        try {
                            $item->$field = Carbon::parse($item->$field)->format('d-m-Y');
                        } catch (\Exception $e) {
                            // ไม่แปลงถ้า format ไม่ถูกต้อง
                            $item->$field = $item->$field;
                        }
                    }
                }
    
                return $item;
            });

            //dd($data);

        return view('towerDismantle.home', compact('data')); // ส่งผลลัพธ์ไปที่ view
    }


    public function addrefcode(Request $request)
    {
        $request->validate([
            'refCode' => 'required',
            'siteCode' => 'required',
            'siteName' => 'required',
            'gtnOffice' => 'required',
            'trueRegion' => 'required',
            'towerType' => 'required',
            'towerModel' => 'required',
            'estimatedTowerWeight' => 'required',
            'actualTowerWeight' => 'required',
            'remark' => 'required',
        ]);

        $data = [
            'refCode' => $request->refCode,
            'siteCode' => $request->siteCode,
            'siteName' => $request->siteName,
            'gtnOffice' => $request->gtnOffice,
            'trueRegion' => $request->trueRegion,
            'towerType' => $request->towerType,
            'towerModel' => $request->towerModel,
            'estimatedTowerWeight' => $request->estimatedTowerWeight,
            'actualTowerWeight' => $request->actualTowerWeight,
            'remark' => $request->remark,
        ];

      


        DB::beginTransaction(); // เริ่ม Transaction

        try {

            $mainId = DB::table('towerdismantle_header')->insertGetId($data);

            $est = ['id_est' => $mainId];
            $work = ['id_work' => $mainId];
            $tower = ['id_tower' => $mainId];
            $towersell = ['id_towersell' => $mainId];
            $re1 = ['id_re1' => $mainId];
            $re2 = ['id_re2' => $mainId];
            $re3 = ['id_re3' => $mainId];
            $pay1 = ['id_pay1' => $mainId];
            $pay2 = ['id_pay2' => $mainId];
            $pay3 = ['id_pay3' => $mainId];

            //dd($mainId, $est, $work, $tower, $towersell, $re1, $re2, $re3, $pay1, $pay2, $pay3);

            // ✅ เตรียมข้อมูลที่ใช้ id นี้ ไปแทรกในตารางอื่น
            DB::table('towerdismantle_estimatedprice')->insert($est);
            DB::table('towerdismantle_workingprogress')->insert($work);
            DB::table('towerdismantle_towerbuybackdata')->insert($tower);
            DB::table('towerdismantle_towerselingdata')->insert($towersell);

            DB::table('towerdismantle_revenuedetail_1')->insert($re1);
            DB::table('towerdismantle_revenuedetail_2')->insert($re2);
            DB::table('towerdismantle_revenuedetail_3')->insert($re3);

            DB::table('towerdismantle_paymentdetail_1')->insert($pay1);
            DB::table('towerdismantle_paymentdetail_2')->insert($pay2);
            DB::table('towerdismantle_paymentdetail_3')->insert($pay3);

            DB::commit(); // ทุกอย่างผ่าน ก็ commit
        } catch (\Exception $e) {
            DB::rollBack(); // ถ้ามีอะไรพัง ก็ rollback กลับ
            throw $e;
        }

        return redirect()->back()->with('success', 'RefCode added successfully!');
    }


    public function edit($id)
    {

        $blog = DB::table("towerdismantle_header as m")
            ->leftJoin('towerdismantle_estimatedprice as e', 'm.id', '=', 'e.id_est')
            ->leftJoin('towerdismantle_workingprogress as w', 'm.id', '=', 'w.id_work')
            ->leftJoin('towerdismantle_towerbuybackdata as tb', 'm.id', '=', 'tb.id_tower')
            ->leftJoin('towerdismantle_towerselingdata as ts', 'm.id', '=', 'ts.id_towersell')
            ->leftJoin('towerdismantle_revenuedetail_1 as r1', 'm.id', '=', 'r1.id_re1')
            ->leftJoin('towerdismantle_revenuedetail_2 as r2', 'm.id', '=', 'r2.id_re2')
            ->leftJoin('towerdismantle_revenuedetail_3 as r3', 'm.id', '=', 'r3.id_re3')
            ->leftJoin('towerdismantle_paymentdetail_1 as p1', 'm.id', '=', 'p1.id_pay1')
            ->leftJoin('towerdismantle_paymentdetail_2 as p2', 'm.id', '=', 'p2.id_pay2')
            ->leftJoin('towerdismantle_paymentdetail_3 as p3', 'm.id', '=', 'p3.id_pay3')


            ->where('m.id', $id)
            ->first();


        //dd($blog);    


        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if (!$blog) {
            return redirect()->back()->withErrors('Data not found for the given ID.');
        }

        //dd($blog);

        return view("towerDismantle.update", compact("blog"));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'refCode' => 'required',
            'siteCode' => 'required',
            'siteName' => 'required',
            'gtnOffice' => 'required',
            'trueRegion' => 'required',
            'towerType' => 'required',
            'towerModel' => 'required',
            'estimatedTowerWeight' => 'required',
            'actualTowerWeight' => 'required',
            'remark' => 'required',
        ]);

        $data = [
            'refCode' => $request->refCode,
            'siteCode' => $request->siteCode,
            'siteName' => $request->siteName,
            'gtnOffice' => $request->gtnOffice,
            'trueRegion' => $request->trueRegion,
            'towerType' => $request->towerType,
            'towerModel' => $request->towerModel,
            'estimatedTowerWeight' => $request->estimatedTowerWeight,
            'actualTowerWeight' => $request->actualTowerWeight,
            'remark' => $request->remark,
        ];

        $estimated = [
            'est_revenue' => $request->est_revenue,
            'est_serviceCost' => $request->est_serviceCost,
            'est_buybackCost' => $request->est_buybackCost,
            'est_transportationCost' => $request->est_transportationCost,
            'est_otherCost' => $request->est_otherCost,
            'est_grossProfit' => $request->est_grossProfit,
            'est_grossProfitMargin' => $request->est_grossProfitMargin,
        ];

        $Working = [
            'work_JobAssignedDateByCustomer' => $request->work_JobAssignedDateByCustomer, //date
            'work_PlanSurveyedDate' => $request->work_PlanSurveyedDate, //date
            'work_ActualSurveyedDate' => $request->work_ActualSurveyedDate, //date
            'work_CustomerCommittedDate' => $request->work_CustomerCommittedDate, //date
            'work_TowerDismantlingSubC' => $request->work_TowerDismantlingSubC,
            'work_PlanStartedDate' => $request->work_PlanStartedDate, //date
            'work_ActualStartedDate' => $request->work_ActualStartedDate, //date
            'work_PlanFinishedDate' => $request->work_PlanFinishedDate, //date
            'work_ActualFinishedDate' => $request->work_ActualFinishedDate, //date
            'work_WorkingIssue' => $request->work_WorkingIssue,
        ];

        $towerdismantle_towerbuybackdata = [
            'tower_TowerPrice' => $request->tower_TowerPrice,
            'tower_Towerbuyback_PlanPaidDate' => $request->tower_Towerbuyback_PlanPaidDate,
            'tower_Towerbuyback_ActualPaidDate' => $request->towersell_PlanGetPaidDate,
            'tower_ReceiptNo' => $request->tower_ReceiptNo,
        ];

        $towerdismantle_towerselingdata = [
            'towersell_TowerBuyerName' => $request->towersell_TowerBuyerName,
            'towersell_TowerSellingPrice' => $request->towersell_TowerSellingPrice,
            'towersell_PlanGetPaidDate' => $request->towersell_PlanGetPaidDate,
            'towersell_ActualGetPaidDate' => $request->towersell_ActualGetPaidDate,
        ];

        $revenuedetail_1 = [
            're1_TowerDismantlingServicePrice' => $request->re1_TowerDismantlingServicePrice,
            're1_Customer_QuotationSubbmittedDatePlan' => $request->re1_Customer_QuotationSubbmittedDatePlan,
            're1_Customer_QuotationSubbmittedDateActual' => $request->re1_Customer_QuotationSubbmittedDateActual,
            're1_Customer_QuotationAmount' => $request->re1_Customer_QuotationAmount,
            're1_Customer_POAmount' => $request->re1_Customer_POAmount,
            're1_Customer_POReceivedDate' => $request->re1_Customer_POReceivedDate,
            're1_PlanInvoicePlacedDate' => $request->re1_PlanInvoicePlacedDate,
            're1_PlanInvoiceAmount' => $request->re1_PlanInvoiceAmount,
            're1_InvoiceNo' => $request->re1_InvoiceNo,
            're1_ActualInvoiceAmount' => $request->re1_ActualInvoiceAmount,
            're1_ActualInvoicePlacedDate' => $request->re1_ActualInvoicePlacedDate,
            're1_ConfirmedDueDate' => $request->re1_ConfirmedDueDate,
        ];

        $revenuedetail_2 = [
            're2_TowerDismantlingServicePrice' => $request->re2_TowerDismantlingServicePrice,
            're2_Customer_QuotationSubbmittedDatePlan' => $request->re2_Customer_QuotationSubbmittedDatePlan,
            're2_Customer_QuotationSubbmittedDateActual' => $request->re2_Customer_QuotationSubbmittedDateActual,
            're2_Customer_QuotationAmount' => $request->re2_Customer_QuotationAmount,
            're2_Customer_POAmount' => $request->re2_Customer_POAmount,
            're2_Customer_POReceivedDate' => $request->re2_Customer_POReceivedDate,
            're2_PlanInvoicePlacedDate' => $request->re2_PlanInvoicePlacedDate,
            're2_PlanInvoiceAmount' => $request->re2_PlanInvoiceAmount,
            're2_InvoiceNo' => $request->re2_InvoiceNo,
            're2_ActualInvoiceAmount' => $request->re2_ActualInvoiceAmount,
            're2_ActualInvoicePlacedDate' => $request->re2_ActualInvoicePlacedDate,
            're2_ConfirmedDueDate' => $request->re2_ConfirmedDueDate,
        ];

        $revenuedetail_3 = [
            're3_TowerDismantlingServicePrice' => $request->re3_TowerDismantlingServicePrice,
            're3_Customer_QuotationSubbmittedDatePlan' => $request->re3_Customer_QuotationSubbmittedDatePlan,
            're3_Customer_QuotationSubbmittedDateActual' => $request->re3_Customer_QuotationSubbmittedDateActual,
            're3_Customer_QuotationAmount' => $request->re3_Customer_QuotationAmount,
            're3_Customer_POAmount' => $request->re3_Customer_POAmount,
            're3_Customer_POReceivedDate' => $request->re3_Customer_POReceivedDate,
            're3_PlanInvoicePlacedDate' => $request->re3_PlanInvoicePlacedDate,
            're3_PlanInvoiceAmount' => $request->re3_PlanInvoiceAmount,
            're3_InvoiceNo' => $request->re3_InvoiceNo,
            're3_ActualInvoiceAmount' => $request->re3_ActualInvoiceAmount,
            're3_ActualInvoicePlacedDate' => $request->re3_ActualInvoicePlacedDate,
            're3_ConfirmedDueDate' => $request->re3_ConfirmedDueDate,
        ];

        $pay_1 = [
            'pay1_SubCName' => $request->pay1_SubCName,
            'pay1_ActivityOfPayment' => $request->pay1_ActivityOfPayment,
            'pay1_PRAmount' => $request->pay1_PRAmount,
            'pay1_PRrequestedDateEmail' => $request->pay1_PRrequestedDateEmail,
            'pay1_PRApprovedDateEmail' => $request->pay1_PRApprovedDateEmail,
            'pay1_PRNoERP' => $request->pay1_PRNoERP,
            'pay1_PRIssuedDateERP' => $request->pay1_PRIssuedDateERP,
            'pay1_PRApprovedDateERP' => $request->pay1_PRApprovedDateERP,
            'pay1_WOAmountERP' => $request->pay1_WOAmountERP,
            'pay1_WONo' => $request->pay1_WONo,
            'pay1_WOIssueDateERP' => $request->pay1_WOIssueDateERP,
            'pay1_WOApprovedDateERP' => $request->pay1_WOApprovedDateERP,
            'pay1_DatesentWOtoSubCEmail' => $request->pay1_DatesentWOtoSubCEmail,
            'pay1_BillingAmount' => $request->pay1_BillingAmount,
            'pay1_BillingRequestedDateEmail' => $request->pay1_BillingRequestedDateEmail,
            'pay1_BillingApprovedDateEmail' => $request->pay1_BillingApprovedDateEmail,
            'pay1_BillingNoERP' => $request->pay1_BillingNoERP,
            'pay1_BillingIssuedDateERP' => $request->pay1_BillingIssuedDateERP,
            'pay1_BillingApprovedDateERP' => $request->pay1_BillingApprovedDateERP,
            'pay1_DatesentBillingtoSubC' => $request->pay1_DatesentBillingtoSubC,
            'pay1_InvoicePlaceddatebySubC' => $request->pay1_InvoicePlaceddatebySubC,
            'pay1_SubC_InvoiceAmount' => $request->pay1_SubC_InvoiceAmount,
            'pay1_PaymentconfirmedDateERP' => $request->pay1_PaymentconfirmedDateERP,
        ];

        $pay_2 = [
            'pay2_SubCName' => $request->pay2_SubCName,
            'pay2_ActivityOfPayment' => $request->pay2_ActivityOfPayment,
            'pay2_PRAmount' => $request->pay2_PRAmount,
            'pay2_PRrequestedDateEmail' => $request->pay2_PRrequestedDateEmail,
            'pay2_PRApprovedDateEmail' => $request->pay2_PRApprovedDateEmail,
            'pay2_PRNoERP' => $request->pay2_PRNoERP,
            'pay2_PRIssuedDateERP' => $request->pay2_PRIssuedDateERP,
            'pay2_PRApprovedDateERP' => $request->pay2_PRApprovedDateERP,
            'pay2_WOAmountERP' => $request->pay2_WOAmountERP,
            'pay2_WONo' => $request->pay2_WONo,
            'pay2_WOIssueDateERP' => $request->pay2_WOIssueDateERP,
            'pay2_WOApprovedDateERP' => $request->pay2_WOApprovedDateERP,
            'pay2_DatesentWOtoSubCEmail' => $request->pay2_DatesentWOtoSubCEmail,
            'pay2_BillingAmount' => $request->pay2_BillingAmount,
            'pay2_BillingRequestedDateEmail' => $request->pay2_BillingRequestedDateEmail,
            'pay2_BillingApprovedDateEmail' => $request->pay2_BillingApprovedDateEmail,
            'pay2_BillingNoERP' => $request->pay2_BillingNoERP,
            'pay2_BillingIssuedDateERP' => $request->pay2_BillingIssuedDateERP,
            'pay2_BillingApprovedDateERP' => $request->pay2_BillingApprovedDateERP,
            'pay2_DatesentBillingtoSubC' => $request->pay2_DatesentBillingtoSubC,
            'pay2_InvoicePlaceddatebySubC' => $request->pay2_InvoicePlaceddatebySubC,
            'pay2_SubC_InvoiceAmount' => $request->pay2_SubC_InvoiceAmount,
            'pay2_PaymentconfirmedDateERP' => $request->pay2_PaymentconfirmedDateERP,
        ];

        $pay_3 = [
            'pay3_SubCName' => $request->pay3_SubCName,
            'pay3_ActivityOfPayment' => $request->pay3_ActivityOfPayment,
            'pay3_PRAmount' => $request->pay3_PRAmount,
            'pay3_PRrequestedDateEmail' => $request->pay3_PRrequestedDateEmail,
            'pay3_PRApprovedDateEmail' => $request->pay3_PRApprovedDateEmail,
            'pay3_PRNoERP' => $request->pay3_PRNoERP,
            'pay3_PRIssuedDateERP' => $request->pay3_PRIssuedDateERP,
            'pay3_PRApprovedDateERP' => $request->pay3_PRApprovedDateERP,
            'pay3_WOAmountERP' => $request->pay3_WOAmountERP,
            'pay3_WONo' => $request->pay3_WONo,
            'pay3_WOIssueDateERP' => $request->pay3_WOIssueDateERP,
            'pay3_WOApprovedDateERP' => $request->pay3_WOApprovedDateERP,
            'pay3_DatesentWOtoSubCEmail' => $request->pay3_DatesentWOtoSubCEmail,
            'pay3_BillingAmount' => $request->pay3_BillingAmount,
            'pay3_BillingRequestedDateEmail' => $request->pay3_BillingRequestedDateEmail,
            'pay3_BillingApprovedDateEmail' => $request->pay3_BillingApprovedDateEmail,
            'pay3_BillingNoERP' => $request->pay3_BillingNoERP,
            'pay3_BillingIssuedDateERP' => $request->pay3_BillingIssuedDateERP,
            'pay3_BillingApprovedDateERP' => $request->pay3_BillingApprovedDateERP,
            'pay3_DatesentBillingtoSubC' => $request->pay3_DatesentBillingtoSubC,
            'pay3_InvoicePlaceddatebySubC' => $request->pay3_InvoicePlaceddatebySubC,
            'pay3_SubC_InvoiceAmount' => $request->pay3_SubC_InvoiceAmount,
            'pay3_PaymentconfirmedDateERP' => $request->pay3_PaymentconfirmedDateERP,
        ];

        //dd($data,$estimated,$Working,$towerdismantle_towerbuybackdata,$revenuedetail_1,$revenuedetail_2,$revenuedetail_3);

        //dd($pay_2);

        //dd($pay_3);

        try {
            // เริ่ม transaction
            DB::beginTransaction();

            // อัปเดตข้อมูลในตาราง main_csv โดยใช้ $id ที่มีอยู่แล้ว
            DB::table('towerdismantle_header')->where('id', $id)->update($data);
            DB::table("towerdismantle_estimatedprice")->where("id_est", $id)->update($estimated);
            DB::table("towerdismantle_workingprogress")->where("id_work", $id)->update($Working);
            DB::table("towerdismantle_towerbuybackdata")->where("id_tower", $id)->update($towerdismantle_towerbuybackdata);
            DB::table("towerdismantle_towerselingdata")->where("id_towersell", $id)->update($towerdismantle_towerselingdata);

            DB::table("towerdismantle_revenuedetail_1")->where("id_re1", $id)->update($revenuedetail_1);
            DB::table("towerdismantle_revenuedetail_2")->where("id_re2", $id)->update($revenuedetail_2);
            DB::table("towerdismantle_revenuedetail_3")->where("id_re3", $id)->update($revenuedetail_3);

            DB::table("towerdismantle_paymentdetail_1")->where("id_pay1", $id)->update($pay_1);
            DB::table("towerdismantle_paymentdetail_2")->where("id_pay2", $id)->update($pay_2);
            DB::table("towerdismantle_paymentdetail_3")->where("id_pay3", $id)->update($pay_3);

            DB::commit();

            return back()->with('success', 'Updated successfully');
        } catch (\Exception $e) {
            // Rollback transaction หากเกิดข้อผิดพลาด
            DB::rollback();

            // จัดการกับข้อผิดพลาดที่เกิดขึ้นที่นี่ เช่นการแจ้งเตือน
            throw $e;
        }
    }
}