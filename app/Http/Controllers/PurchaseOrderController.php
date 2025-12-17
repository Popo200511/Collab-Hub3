<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    public function index()
{
    // PO เปล่าสำหรับหัวตารางตอนโหลดครั้งแรก
    $po = (object)[
        'po_no' => '-',
        'customer_name' => '-',
        'po_date' => '-',
        'total_amount' => 0
    ];

    // รายการ PO ทั้งหมด
    $purchaseOrders = DB::table('purchase_orders as po')
        ->leftJoin('customers as c', 'po.customer_id', '=', 'c.id')
        ->select(
            'po.id as purchase_order_id',
            'po.po_no',
            'po.po_date',
            'po.total_amount',
            DB::raw('COALESCE(c.name, "-") as customer_name')
        )
        ->orderBy('po.id', 'desc')
        ->get();

    // รายการสินค้าสำหรับตอนสร้าง PO (เผื่ออนาคต)
    $items = DB::table('item_master')
        ->select('id','item_code','description','unit','unit_price')
        ->orderBy('item_code')
        ->get();

    // รายชื่อลูกค้า
    $customers = DB::table('customers')->orderBy('name')->get();

    // ส่งไปเฉพาะค่าที่ต้องใช้จริง
    return view('PO.purchase', compact('purchaseOrders', 'customers', 'items', 'po'));
}


public function fetchPoItems($id)
{
    $items = DB::table('po_items as i')
        ->leftJoin('item_master as im', 'i.item_id', '=', 'im.id')
        ->select(
            'im.item_code',
            'im.description as item_description',
            'i.unit_price',
            'i.qty',
            'i.amount',
            'i.used_qty',
            'i.used_amount',
            'i.balanced_qty',
            'i.balanced_amount'
        )
        ->where('i.purchase_order_id', $id)
        ->get();

    return response()->json([ 'items' => $items ]);
}



public function save(Request $request)
{

    $request->validate([
        'customer_name' => 'required|string|max:255',
        'po_no' => 'required|string|unique:purchase_orders,po_no',
        'po_date' => 'required|date',
        'total_amount' => 'required|numeric',
        'items' => 'required|array|min:1',
        'items.*.item_id' => 'required|integer',
        'items.*.qty' => 'required|numeric|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.amount' => 'required|numeric|min:0',
    ], [
        'customer_name.required' => 'กรุณาระบุชื่อลูกค้า',
        'po_no.required' => 'กรุณาระบุเลขที่ PO',
        'po_date.required' => 'กรุณาระบุวันที่ PO',
        'items.required' => 'กรุณาเพิ่มรายการสินค้าอย่างน้อย 1 รายการ',
    ]);

    DB::beginTransaction();

    try {
        // ============================
        // 1) ตรวจสอบลูกค้า + Create ถ้าไม่พบ
        // ============================

        $customerName = trim($request->customer_name);

        // ป้องกัน customerName เป็นค่าว่าง
        if ($customerName === "") {
            throw new \Exception("ชื่อลูกค้าไม่สามารถเป็นค่าว่างได้");
        }

        $customer = DB::table('customers')->where('name', $customerName)->first();

        if (!$customer) {
            $customerId = DB::table('customers')->insertGetId([
                'name' => $customerName,

            ]);
        } else {
            $customerId = $customer->id;
        }

        // ============================
        // 2) บันทึก Purchase Order
        // ============================

        $poId = DB::table('purchase_orders')->insertGetId([
            'po_no' => $request->po_no,
            'po_date' => $request->po_date,
            'customer_id' => $customerId,
            'total_amount' => $request->total_amount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ============================
        // 3) บันทึกสินค้าใน PO
        // ============================

        foreach ($request->items as $item) {
            DB::table('po_items')->insert([
                'purchase_order_id' => $poId,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'amount' => $item['amount'],
                'balanced_qty' => $item['qty'],
                'balanced_amount' => $item['amount'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'po_id' => $poId
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}



public function show($id = null)
{
    // ดึงรายการ PO ทั้งหมด สำหรับ list
    $purchaseOrders = DB::table('purchase_orders as po')
        ->leftJoin('customers as c', 'po.customer_id', '=', 'c.id')
        ->select(
            'po.id as purchase_order_id',
            'po.po_no',
            'po.po_date',
            'po.total_amount',
            DB::raw('COALESCE(c.name, "-") as customer_name')
        )
        ->get();

    if ($id) {
        $po = $purchaseOrders->where('purchase_order_id', $id)->first();
    }

    if (!isset($po)) {
        $po = (object)[
            'purchase_order_id' => null,
            'po_no' => '-',
            'po_date' => '-',
            'total_amount' => 0,
            'customer_name' => '-'
        ];
        $items = collect(); // empty collection
    } else {
        $items = DB::table('po_items as i')
            ->leftJoin('item_master as im', 'i.item_id', '=', 'im.id')
            ->select('im.item_code', 'im.description as item_description', 'i.unit_price', 'i.qty', 'i.amount', 'i.used_qty', 'i.used_amount', 'i.balanced_qty', 'i.balanced_amount')
            ->where('i.purchase_order_id', $id)
            ->get();
    }

    return view('PO.show', compact('purchaseOrders', 'po', 'items'));
}




public function checkItemsDuplicates(Request $request)
{
    $items = $request->input('items', []);
    if (!is_array($items)) {
        return response()->json(['duplicates' => []]);
    }

    // ดึง item_code จาก frontend (ตรงกับ JS)
    $itemCodes = collect($items)
        ->pluck('item_code')
        ->filter()
        ->unique()
        ->values()
        ->toArray();

    if (empty($itemCodes)) {
        return response()->json(['duplicates' => []]);
    }

    $existingCodes = DB::table('item_master')
        ->whereIn('item_code', $itemCodes)
        ->pluck('item_code')
        ->toArray();

    return response()->json([
        'duplicates' => $existingCodes
    ]);
}







    // Import items
   public function importItems(Request $request)
{
    $items = $request->input('items', []);

    Log::info('IMPORT ITEMS', $items);

    if (!is_array($items) || empty($items)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid items data'
        ], 422);
    }

    DB::beginTransaction();

    try {
        foreach ($items as $item) {

            if (empty($item['item_code'])) {
                continue;
            }

            $exists = DB::table('item_master')
                ->where('item_code', $item['item_code'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('item_master')->insert([
                'item_code'   => $item['item_code'],
                'description' => $item['description'] ?? null,
                'unit_price'  => isset($item['unit_price']) ? floatval($item['unit_price']) : 0,
                'unit'        => $item['unit'] ?? null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        DB::commit();

        return response()->json(['status' => 'success']);

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error('IMPORT FAILED', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}











}