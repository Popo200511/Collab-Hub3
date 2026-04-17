<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class RevenuePurchaseController extends Controller
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

        return view('Revenue.PO.purchase', compact('data'));
    }

    /* บันทึกข้อมูล PO Received จาก Modal  ไปบันทึกที่ ตาราง collab_revenue_po_receviedfromcustomer */
    public function PO_Received(Request $request)
    {
        //dd('po_no');
        // 1️⃣ Validate ข้อมูล
        $validated = $request->validate([
            'customer_code'    => 'nullable|string|max:50',  // ✅ เปลี่ยนเป็น nullable ถ้ายังไม่มีการกรอกข้อมูล Customer:
            'customer_name'    => 'nullable|string|max:255', // ✅ เปลี่ยนเป็น nullable ถ้ายังไม่มีการกรอกข้อมูล Customer:
            'po_no'            => 'required|string|max:100',
            'po_amount'        => 'required|numeric|min:0',
            'po_received_date' => 'required|date|before_or_equal:today',
        ]);

        $data = [
            'customer_code'    => $request->customer_code,
            'customer_name'    => $request->customer_name,
            'po_no'            => $request->po_no,
            'po_amount'        => $request->po_amount,
            'po_received_date' => $request->po_received_date,
        ];

        //dd($data);

        // 2️⃣ บันทึกข้อมูลที่ตาราง collab_revenue_po_receviedfromcustomer
        $id = DB::table('collab_revenue_po_receviedfromcustomer')->insertGetId([
            'customer_code'    => $validated['customer_code'],
            'customer_name'    => $validated['customer_name'],
            'po_no'            => $validated['po_no'],
            'po_amount'        => $validated['po_amount'],
            'po_received_date' => $validated['po_received_date'],
        ]);

        //dd($id);

        // 3️⃣ Redirect กลับหน้าเดิมพร้อม Flash Message
        return redirect()->back()->with('success', 'บันทึก PO Received เรียบร้อยแล้ว!');
    }

    /* บันทึกข้อมูล PO Decrement จาก Modal */
    public function PO_Decrement(Request $request)
    {
        // 1️⃣ Validate ข้อมูล
        $validated = $request->validate([
            'po_decrement' => 'required|string|max:50',
        ]);

        // 2️⃣ บันทึกข้อมูลที่ตาราง collab_revenue_po_decremen
        $id = DB::table('collab_revenue_po_decremen')->insertGetId([
            'po_decrement' => $validated['po_decrement'],
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // 3️⃣ Redirect กลับหน้าเดิมพร้อม Flash Message
        return redirect()->back()->with('success', 'บันทึก PO Decrement เรียบร้อยแล้ว!');
    }




    /**
     * แสดงฟอร์มสร้างใหม่ (Create)
     */
    public function create()
    {
        return view('revenue-purchase.create');
    }

    /**
     * บันทึกข้อมูลใหม่ (Store)
     */
    public function store(Request $request)
    {
        // 1️⃣ Validate ข้อมูล
        $validated = $request->validate([
            'customer_code'    => 'required|string|max:50',
            'customer_name'    => 'required|string|max:255',
            'po_no'            => 'required|string|max:100|unique:collab_revenue_po_receviedfromcustomer,po_no',
            'po_amount'        => 'required|numeric|min:0',
            'po_received_date' => 'required|date|before_or_equal:today',
            'po_decrement'     => 'nullable|numeric|min:0',
            'po_booking'       => 'nullable|numeric|min:0',
            'invoice_amount'   => 'nullable|numeric|min:0',
        ]);

        // 2️⃣ บันทึกข้อมูลด้วย Query Builder
        $id = DB::table('collab_revenue_po_receviedfromcustomer')->insertGetId([
            'customer_code'       => $validated['customer_code'],
            'customer_name'       => $validated['customer_name'],
            'po_no'               => $validated['po_no'],
            'po_amount'           => $validated['po_amount'],
            'po_received_date'    => $validated['po_received_date'],
            'po_decrement'        => $validated['po_decrement'] ?? 0,
            'po_booking'          => $validated['po_booking'] ?? 0,
            'invoice_amount'      => $validated['invoice_amount'] ?? 0,
            // คำนวณยอดคงเหลือ (ถ้าไม่ใช้ Generated Column ใน SQL)
            'balanced_po_booking' => $validated['po_amount'] - ($validated['po_decrement'] ?? 0) - ($validated['po_booking'] ?? 0),
            'balanced_po_invoice' => $validated['po_amount'] - ($validated['po_decrement'] ?? 0) - ($validated['invoice_amount'] ?? 0),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อย!');
    }

    /**
     * แสดงรายละเอียด (Show)
     */
    public function show($id)
    {
        $data = DB::table('collab_revenue_po_receviedfromcustomer')
                  ->where('id', $id)
                  ->first();

        if (!$data) {
            abort(404);
        }

        return view('revenue-purchase.show', compact('data'));
    }

    /**
     * แสดงฟอร์มแก้ไข (Edit)
     */
    public function edit($id)
    {
        $data = DB::table('collab_revenue_po_receviedfromcustomer')
                  ->where('id', $id)
                  ->first();

        if (!$data) {
            abort(404);
        }

        return view('revenue-purchase.edit', compact('data'));
    }

    /**
     * อัปเดตข้อมูล (Update)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'po_decrement'     => 'nullable|numeric|min:0',
            'po_booking'       => 'nullable|numeric|min:0',
            'invoice_amount'   => 'nullable|numeric|min:0',
        ]);

        // อัปเดตข้อมูล
        DB::table('collab_revenue_po_receviedfromcustomer')
            ->where('id', $id)
            ->update([
                'po_decrement'        => $validated['po_decrement'] ?? 0,
                'po_booking'          => $validated['po_booking'] ?? 0,
                'invoice_amount'      => $validated['invoice_amount'] ?? 0,
                // คำนวณยอดคงเหลือใหม่
                'balanced_po_booking' => DB::raw('po_amount - COALESCE(po_decrement, 0) - COALESCE(po_booking, 0)'),
                'balanced_po_invoice' => DB::raw('po_amount - COALESCE(po_decrement, 0) - COALESCE(invoice_amount, 0)'),
                'updated_at'          => now(),
            ]);

        return redirect()->back()->with('success', 'อัปเดตข้อมูลเรียบร้อย!');
    }

    /**
     * ลบข้อมูล (Destroy)
     */
    public function destroy($id)
    {
        DB::table('collab_revenue_po_receviedfromcustomer')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('success', 'ลบข้อมูลเรียบร้อย!');
    }
}