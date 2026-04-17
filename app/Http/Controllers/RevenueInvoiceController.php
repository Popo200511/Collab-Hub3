<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class RevenueInvoiceController extends Controller
{
     /**
     * แสดงหน้ารายการ (Index)
     */
    public function index()
    {
        // ตัวอย่าง: ดึงข้อมูลจากตาราง
        $data = DB::table('collab_revenue_po_receviedfromcustomer')
                  ->orderBy('po_received_date', 'desc')
                  ->get();

        return view('Revenue.Invoice.Invoice-Table', compact('data'));
    }

}