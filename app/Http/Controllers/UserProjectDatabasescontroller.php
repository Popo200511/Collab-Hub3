<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserProjectDatabasescontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function project90(Request $request)
    {
        $projectCode   = 90;
        $refcodePrefix = '90-';

        /*
            |--------------------------------------------------------------------------
            | STEP 1️⃣ ลบข้อมูลที่ไม่ใช่ 90- ออกจากตาราง
            | (ทำครั้งเดียว หรือทำทุกครั้งก็ได้ ปลอดภัย)
            |--------------------------------------------------------------------------
        */

        DB::table('collab_table_project90')
            ->where('Refcode_PJ', 'not like', $refcodePrefix . '%')
            ->delete();

        /*
            |--------------------------------------------------------------------------
            | STEP 2️⃣ insert เฉพาะ newjob ที่เป็น 90-
            | และยังไม่เคยมีใน collab_table_project90
            |--------------------------------------------------------------------------
        */

        DB::table('collab_table_project90')->insertUsing(
            [
                'Refcode_PJ',
                'Site_Code_PJ',
                'Job_Description_PJ',
                'Office_Code_PJ',
                'Customer_Region_PJ',
                'Estimated_Revenue_PJ',
                'Estimated_Service_Cost_PJ',
                'Estimated_Material_Cost_PJ',
                'Estimated_Gross_Profit_PJ',
                'Estimated_Gross_ProfitMargin_PJ',
            ],
            DB::table('collab_newjob')
                ->select(
                    'Refcode',
                    'Site_Code',
                    'Job_Description',
                    'Office_Code',
                    'Customer_Region',
                    'Estimated_Revenue',
                    'Estimated_Service_Cost',
                    'Estimated_Material_Cost',
                    'Estimated_Gross_Profit',
                    'Estimated_Gross_ProfitMargin'
                )
                ->where('Refcode', 'like', $refcodePrefix . '%')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('collab_table_project90')
                        ->whereColumn(
                            'collab_table_project90.Refcode_PJ',
                            'collab_newjob.Refcode'
                        );
                })
        );

                                            // 2️⃣ ดึงข้อมูลจาก project16 มาแสดงตาม status ของ user
        $userStatus = Auth::user()->status; // เช่น "01_BKK" หรือ "admin"

        $projectRole = DB::table('collab_user_permissions90')
            ->where('user_id', Auth::id())
            ->value('project_role');

        $projectData = DB::table('collab_table_project90')
            ->when(! in_array($projectRole, ['Admin', 'Project Manager']), function ($query) use ($userStatus) {
                // ถ้าไม่ใช่ Admin และไม่ใช่ Project Manager
                $query->where('Office_Code_PJ', $userStatus);
            })
            ->orderBy('Refcode_PJ')
            ->get();

        //dd($projectData);

        // 3️⃣ user ที่ใช้จัด permission
        $users = DB::table('users')
            ->orderBy('name')
            ->get();

        // 4️⃣ permissions (ผูกกับ project_code = 90)
        $permissions = DB::table('collab_user_permissions90')
            ->where('project_code', $projectCode)
            ->get()
            ->keyBy('user_id');

        // 5️⃣ ชื่อโปรเจค (แสดงผล)
        $projectName = '90_True Maintenance';

        //dd($showProjectView16);

        return view(
            'user.projectdatabases.projectview_90',
            compact('projectData', 'users', 'permissions', 'projectName', 'projectCode')
        );
    }

    // update or insert permissions
    public function save(Request $request)
    {
        $userIds = array_keys($request->member_status);

        foreach ($userIds as $userId) {

            $data = [
                'member_status'                    => $request->member_status[$userId] ?? 'no',
                'project_role'                     => $request->project_role[$userId] ?? null,

                // ✅ อ่านจาก *_permission
                'Customer_Region_PJ'               => $request->Customer_Region_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Revenue_PJ'             => $request->Estimated_Revenue_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Service_Cost_PJ'        => $request->Estimated_Service_Cost_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Material_Cost_PJ'       => $request->Estimated_Material_Cost_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Transportation_Cost_PJ' => $request->Estimated_Transportation_Cost_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Other_Cost_PJ'          => $request->Estimated_Other_Cost_PJ_permission[$userId] ?? 'invisible',

                // 🔥 เพิ่ม 2 ตัวนี้
                'Estimated_Gross_Profit_PJ'        => $request->Estimated_Gross_Profit_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Gross_Profit_Margin_PJ' => $request->Estimated_Gross_Profit_Margin_PJ_permission[$userId] ?? 'invisible',
            ];

            //dd($data);

            // col 1 - col 50
            for ($i = 1; $i <= 50; $i++) {
                $data["col{$i}"] = $request->{"col{$i}_permission"}[$userId] ?? 'invisible';
            }

            //dd($data);

            DB::table('collab_user_permissions90')->updateOrInsert(
                [
                    'user_id'      => $userId,
                    'project_code' => $request->project_code,
                ],
                $data
            );
        }

        return back()->with('success', 'Permissions saved successfully!');
    }

    public function inlineUpdate_old(Request $request)
    {
        $request->validate([
            'id'    => 'required|string', // Refcode_PJ
            'field' => 'required|string',
            'value' => 'nullable|string',
        ]);

        // ===== col1 - col50 =====
        $allowedCols = [];
        for ($i = 1; $i <= 50; $i++) {
            $allowedCols[] = 'col' . $i;
        }

        // ===== project money fields =====
        $projectFields = [
            'Customer_Region_PJ',
            'Estimated_Revenue_PJ',
            'Estimated_Service_Cost_PJ',
            'Estimated_Material_Cost_PJ',
        ];

        if (! in_array($request->field, array_merge($allowedCols, $projectFields))) {
            return response()->json(['success' => false], 403);
        }

        // ===== permission =====
        $permission = DB::table('collab_user_permissions90')
            ->where('user_id', Auth::id())
            ->where('project_code', '16')
            ->first();

        if (! $permission || $permission->member_status !== 'yes') {
            return response()->json(['success' => false], 403);
        }

        if (
            ! isset($permission->{$request->field}) ||
            $permission->{$request->field} !== 'write'
        ) {
            return response()->json(['success' => false], 403);
        }

        DB::beginTransaction();

        try {

            /* ===============================
           1) เตรียม value (ล้าง comma ถ้าเป็นเงิน)
           =============================== */
            $value = $request->value;

            if (in_array($request->field, [
                'Estimated_Revenue_PJ',
                'Estimated_Service_Cost_PJ',
                'Estimated_Material_Cost_PJ',
            ])) {
                $value = str_replace(',', '', $value);
            }

            /* ===============================
           2) update collab_table_project90
           =============================== */
            DB::table('collab_table_project90')
                ->where('Refcode_PJ', $request->id)
                ->update([
                    $request->field => $value,
                ]);

            /* ===============================
           3) sync basic fields → collab_newjob
           =============================== */
            $syncMap = [
                'Customer_Region_PJ'         => 'Customer_Region',
                'Estimated_Revenue_PJ'       => 'Estimated_Revenue',
                'Estimated_Service_Cost_PJ'  => 'Estimated_Service_Cost',
                'Estimated_Material_Cost_PJ' => 'Estimated_Material_Cost',
            ];

            if (isset($syncMap[$request->field])) {
                DB::table('collab_newjob')
                    ->where('Refcode', $request->id)
                    ->update([
                        $syncMap[$request->field] => $value,
                    ]);
            }

            /* ===============================
           4) คำนวณ Gross (เฉพาะ field เงิน)
           =============================== */
            if (in_array($request->field, [
                'Estimated_Revenue_PJ',
                'Estimated_Service_Cost_PJ',
                'Estimated_Material_Cost_PJ',
            ])) {

                $row = DB::table('collab_table_project90')
                    ->where('Refcode_PJ', $request->id)
                    ->first();

                $revenue  = (float) str_replace(',', '', $row->Estimated_Revenue_PJ ?? 0);
                $service  = (float) str_replace(',', '', $row->Estimated_Service_Cost_PJ ?? 0);
                $material = (float) str_replace(',', '', $row->Estimated_Material_Cost_PJ ?? 0);

                $grossProfit = $revenue - $service - $material;
                $grossMargin = $revenue > 0
                    ? ($grossProfit / $revenue) * 100
                    : 0;

                // update project table
                DB::table('collab_table_project90')
                    ->where('Refcode_PJ', $request->id)
                    ->update([
                        'Estimated_Gross_Profit_PJ'       => number_format($grossProfit, 2, '.', ''),
                        'Estimated_Gross_ProfitMargin_PJ' => number_format($grossMargin, 2, '.', ''),
                    ]);

                // sync ไป collab_newjob
                DB::table('collab_newjob')
                    ->where('Refcode', $request->id)
                    ->update([
                        'Estimated_Gross_Profit'       => number_format($grossProfit, 2, '.', ''),
                        'Estimated_Gross_ProfitMargin' => number_format($grossMargin, 2, '.', ''),
                    ]);
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function inlineUpdate(Request $request)
    {
        $request->validate([
            'id'    => 'required|string', // Refcode_PJ
            'field' => 'required|string',
            'value' => 'nullable|string',
        ]);

        /* ===============================
       1) allow fields
       =============================== */
        $allowedCols = [];
        for ($i = 1; $i <= 50; $i++) {
            $allowedCols[] = 'col' . $i;
        }

        $moneyFields = [
            'Estimated_Revenue_PJ',
            'Estimated_Service_Cost_PJ',
            'Estimated_Material_Cost_PJ',
            'Estimated_Transportation_Cost_PJ',
            'Estimated_Other_Cost_PJ',
        ];

        $projectFields = array_merge([
            'Customer_Region_PJ',
        ], $moneyFields);

        if (! in_array($request->field, array_merge($allowedCols, $projectFields))) {
            return response()->json(['success' => false], 403);
        }

        /* ===============================
       2) permission
       =============================== */
        $permission = DB::table('collab_user_permissions90')
            ->where('user_id', Auth::id())
            ->where('project_code', '90')
            ->first();

        if (! $permission || $permission->member_status !== 'yes') {
            return response()->json(['success' => false], 403);
        }

        if (
            ! isset($permission->{$request->field}) ||
            $permission->{$request->field} !== 'write'
        ) {
            return response()->json(['success' => false], 403);
        }

        DB::beginTransaction();

        try {

            /* ===============================
           3) prepare value
           =============================== */
            $rawValue   = trim((string) $request->value);
            $storeValue = $rawValue;

            // ถ้าเป็น field เงิน → format ให้มี comma + 2 decimals
            if (in_array($request->field, $moneyFields)) {
                $numeric    = (float) str_replace(',', '', $rawValue);
                $storeValue = number_format($numeric, 2, '.', ',');
            }

            /* ===============================
           4) update collab_table_project90
           =============================== */
            DB::table('collab_table_project90')
                ->where('Refcode_PJ', $request->id)
                ->update([
                    $request->field => $storeValue,
                ]);

            /* ===============================
           5) sync basic fields → collab_newjob
           =============================== */
            $syncMap = [
                'Customer_Region_PJ'         => 'Customer_Region',
                'Estimated_Revenue_PJ'       => 'Estimated_Revenue',
                'Estimated_Service_Cost_PJ'  => 'Estimated_Service_Cost',
                'Estimated_Material_Cost_PJ' => 'Estimated_Material_Cost',
            ];

            if (isset($syncMap[$request->field])) {
                DB::table('collab_newjob')
                    ->where('Refcode', $request->id)
                    ->update([
                        $syncMap[$request->field] => $storeValue,
                    ]);
            }

            /* ===============================
           6) calculate gross (money only)
           =============================== */
            if (in_array($request->field, $moneyFields)) {

                $row = DB::table('collab_table_project90')
                    ->where('Refcode_PJ', $request->id)
                    ->first();

                $revenue  = (float) str_replace(',', '', $row->Estimated_Revenue_PJ ?? 0);
                $service  = (float) str_replace(',', '', $row->Estimated_Service_Cost_PJ ?? 0);
                $material = (float) str_replace(',', '', $row->Estimated_Material_Cost_PJ ?? 0);

                $grossProfit = $revenue - $service - $material;
                $grossMargin = $revenue > 0
                    ? ($grossProfit / $revenue) * 100
                    : 0;

                $grossProfitFormatted = number_format($grossProfit, 2, '.', ',');
                $grossMarginFormatted = number_format($grossMargin, 2, '.', ',');

                // update project table
                DB::table('collab_table_project90')
                    ->where('Refcode_PJ', $request->id)
                    ->update([
                        'Estimated_Gross_Profit_PJ'       => $grossProfitFormatted,
                        'Estimated_Gross_ProfitMargin_PJ' => $grossMarginFormatted,
                    ]);

                // sync → collab_newjob
                DB::table('collab_newjob')
                    ->where('Refcode', $request->id)
                    ->update([
                        'Estimated_Gross_Profit'       => $grossProfitFormatted,
                        'Estimated_Gross_ProfitMargin' => $grossMarginFormatted,
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'value'   => $storeValue,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }




   // PM พี่ฟ้า 2 Project 83 และ Project 85
    public function project83(Request $request)
    {

        //dd('ok');

        $projectCode   = 83;
        $refcodePrefix = '83-';

        /*
            |--------------------------------------------------------------------------
            | STEP 1️⃣ ลบข้อมูลที่ไม่ใช่ 83- ออกจากตาราง
            | (ทำครั้งเดียว หรือทำทุกครั้งก็ได้ ปลอดภัย)
            |--------------------------------------------------------------------------
        */

        DB::table('collab_table_project83')
            ->where('Refcode_PJ', 'not like', $refcodePrefix . '%')
            ->delete();

        /*
            |--------------------------------------------------------------------------
            | STEP 2️⃣ insert เฉพาะ newjob ที่เป็น 83-
            | และยังไม่เคยมีใน collab_table_project83
            |--------------------------------------------------------------------------
        */

        DB::table('collab_table_project83')->insertUsing(
            [
                'Refcode_PJ',
                'Site_Code_PJ',
                'Job_Description_PJ',
                'Office_Code_PJ',
                'Customer_Region_PJ',
                'Estimated_Revenue_PJ',
                'Estimated_Service_Cost_PJ',
                'Estimated_Material_Cost_PJ',
                'Estimated_Gross_Profit_PJ',
                'Estimated_Gross_ProfitMargin_PJ',
            ],
            DB::table('collab_newjob')
                ->select(
                    'Refcode',
                    'Site_Code',
                    'Job_Description',
                    'Office_Code',
                    'Customer_Region',
                    'Estimated_Revenue',
                    'Estimated_Service_Cost',
                    'Estimated_Material_Cost',
                    'Estimated_Gross_Profit',
                    'Estimated_Gross_ProfitMargin'
                )
                ->where('Refcode', 'like', $refcodePrefix . '%')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('collab_table_project83')
                        ->whereColumn(
                            'collab_table_project83.Refcode_PJ',
                            'collab_newjob.Refcode'
                        );
                })
        );

                                            // 2️⃣ ดึงข้อมูลจาก project16 มาแสดงตาม status ของ user
        $userStatus = Auth::user()->status; // เช่น "01_BKK" หรือ "admin"

        $projectRole = DB::table('collab_user_permissions83')
            ->where('user_id', Auth::id())
            ->value('project_role');

        $projectData = DB::table('collab_table_project83')
            ->when(! in_array($projectRole, ['Admin', 'Project Manager']), function ($query) use ($userStatus) {
                // ถ้าไม่ใช่ Admin และไม่ใช่ Project Manager
                $query->where('Office_Code_PJ', $userStatus);
            })
            ->orderBy('Refcode_PJ')
            ->get();

        //dd($projectData);

        // 3️⃣ user ที่ใช้จัด permission
        $users = DB::table('users')
            ->orderBy('name')
            ->get();

        // 4️⃣ permissions (ผูกกับ project_code = 83)
        $permissions = DB::table('collab_user_permissions83')
            ->where('project_code', $projectCode)
            ->get()
            ->keyBy('user_id');

        // 5️⃣ ชื่อโปรเจค (แสดงผล)
        $projectName = '83_True Tower Strengthening';

        //dd($permissions);

        return view(
            'user.projectdatabases.projectview_83',
            compact('projectData', 'users', 'permissions', 'projectName', 'projectCode')
        );

    }

    public function save83(Request $request)
    {
        $userIds = array_keys($request->member_status);

        foreach ($userIds as $userId) {

            $data = [
                'member_status'                    => $request->member_status[$userId] ?? 'no',
                'project_role'                     => $request->project_role[$userId] ?? null,

                // ✅ อ่านจาก *_permission
                'Customer_Region_PJ'               => $request->Customer_Region_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Revenue_PJ'             => $request->Estimated_Revenue_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Service_Cost_PJ'        => $request->Estimated_Service_Cost_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Material_Cost_PJ'       => $request->Estimated_Material_Cost_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Transportation_Cost_PJ' => $request->Estimated_Transportation_Cost_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Other_Cost_PJ'          => $request->Estimated_Other_Cost_PJ_permission[$userId] ?? 'invisible',

                // 🔥 เพิ่ม 2 ตัวนี้
                'Estimated_Gross_Profit_PJ'        => $request->Estimated_Gross_Profit_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Gross_Profit_Margin_PJ' => $request->Estimated_Gross_Profit_Margin_PJ_permission[$userId] ?? 'invisible',
            ];

            //dd($data);

            // col 1 - col 50
            for ($i = 1; $i <= 50; $i++) {
                $data["col{$i}"] = $request->{"col{$i}_permission"}[$userId] ?? 'invisible';
            }

            //dd($data);

            DB::table('collab_user_permissions83')->updateOrInsert(
                [
                    'user_id'      => $userId,
                    'project_code' => $request->project_code,
                ],
                $data
            );
        }

        return back()->with('success', 'Permissions saved successfully!');
    }

    public function inlineUpdate83(Request $request)
    {
        $request->validate([
            'id'    => 'required|string', // Refcode_PJ
            'field' => 'required|string',
            'value' => 'nullable|string',
        ]);

        /* ===============================
       1) allow fields
       =============================== */
        $allowedCols = [];
        for ($i = 1; $i <= 50; $i++) {
            $allowedCols[] = 'col' . $i;
        }

        $moneyFields = [
            'Estimated_Revenue_PJ',
            'Estimated_Service_Cost_PJ',
            'Estimated_Material_Cost_PJ',
            'Estimated_Transportation_Cost_PJ',
            'Estimated_Other_Cost_PJ',

            'col25',
            'col25',
            'col29',
        ];

        $projectFields = array_merge([
            'Customer_Region_PJ',
        ], $moneyFields);

        if (! in_array($request->field, array_merge($allowedCols, $projectFields))) {
            return response()->json(['success' => false], 403);
        }

        /* ===============================
       2) permission
       =============================== */
        $permission = DB::table('collab_user_permissions83')
            ->where('user_id', Auth::id())
            ->where('project_code', '83')
            ->first();

        if (! $permission || $permission->member_status !== 'yes') {
            return response()->json(['success' => false], 403);
        }

        if (
            ! isset($permission->{$request->field}) ||
            $permission->{$request->field} !== 'write'
        ) {
            return response()->json(['success' => false], 403);
        }

        DB::beginTransaction();

        try {

            /* ===============================
           3) prepare value
           =============================== */
            $rawValue   = trim((string) $request->value);
            $storeValue = $rawValue;

            // ถ้าเป็น field เงิน → format ให้มี comma + 2 decimals
            if (in_array($request->field, $moneyFields)) {
                $numeric    = (float) str_replace(',', '', $rawValue);
                $storeValue = number_format($numeric, 2, '.', ',');
            }

            /* ===============================
           4) update collab_table_project83
           =============================== */
            DB::table('collab_table_project83')
                ->where('Refcode_PJ', $request->id)
                ->update([
                    $request->field => $storeValue,
                ]);

            /* ===============================
           5) sync basic fields → collab_newjob
           =============================== */
            $syncMap = [
                'Customer_Region_PJ'         => 'Customer_Region',
                'Estimated_Revenue_PJ'       => 'Estimated_Revenue',
                'Estimated_Service_Cost_PJ'  => 'Estimated_Service_Cost',
                'Estimated_Material_Cost_PJ' => 'Estimated_Material_Cost',
            ];

            if (isset($syncMap[$request->field])) {
                DB::table('collab_newjob')
                    ->where('Refcode', $request->id)
                    ->update([
                        $syncMap[$request->field] => $storeValue,
                    ]);
            }

            /* ===============================
           6) calculate gross (money only)
           =============================== */
            if (in_array($request->field, $moneyFields)) {

                $row = DB::table('collab_table_project83')
                    ->where('Refcode_PJ', $request->id)
                    ->first();

                $revenue  = (float) str_replace(',', '', $row->Estimated_Revenue_PJ ?? 0);
                $service  = (float) str_replace(',', '', $row->Estimated_Service_Cost_PJ ?? 0);
                $material = (float) str_replace(',', '', $row->Estimated_Material_Cost_PJ ?? 0);

                $grossProfit = $revenue - $service - $material;
                $grossMargin = $revenue > 0
                    ? ($grossProfit / $revenue) * 100
                    : 0;

                $grossProfitFormatted = number_format($grossProfit, 2, '.', ',');
                $grossMarginFormatted = number_format($grossMargin, 2, '.', ',');


                $col25 = (float) str_replace(',', '', $row->col25 ?? 0);
                $col27 = (float) str_replace(',', '', $row->col27 ?? 0);
                $col29 = (float) str_replace(',', '', $row->col29 ?? 0);

                $col30 = $col25 - $col27 - $col29;
                $col30Formatted = number_format($col30, 2, '.', ',');

                // update project table
                DB::table('collab_table_project83')
                    ->where('Refcode_PJ', $request->id)
                    ->update([
                        'Estimated_Gross_Profit_PJ'       => $grossProfitFormatted,
                        'Estimated_Gross_ProfitMargin_PJ' => $grossMarginFormatted,
                        'col30'                           => $col30Formatted,
                    ]);

                // sync → collab_newjob
                DB::table('collab_newjob')
                    ->where('Refcode', $request->id)
                    ->update([
                        'Estimated_Gross_Profit'       => $grossProfitFormatted,
                        'Estimated_Gross_ProfitMargin' => $grossMarginFormatted,
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'value'   => $storeValue,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    // PM พี่ฟ้า 2 Project 83 และ Project 85
    public function project85(Request $request)
    {

        //dd('ok');

        $projectCode   = 85;
        $refcodePrefix = '85-';

        /*
            |--------------------------------------------------------------------------
            | STEP 1️⃣ ลบข้อมูลที่ไม่ใช่ 83- ออกจากตาราง
            | (ทำครั้งเดียว หรือทำทุกครั้งก็ได้ ปลอดภัย)
            |--------------------------------------------------------------------------
        */

        DB::table('collab_table_project85')
            ->where('Refcode_PJ', 'not like', $refcodePrefix . '%')
            ->delete();

        /*
            |--------------------------------------------------------------------------
            | STEP 2️⃣ insert เฉพาะ newjob ที่เป็น 83-
            | และยังไม่เคยมีใน collab_table_project83
            |--------------------------------------------------------------------------
        */

        DB::table('collab_table_project85')->insertUsing(
            [
                'Refcode_PJ',
                'Site_Code_PJ',
                'Job_Description_PJ',
                'Office_Code_PJ',
                'Customer_Region_PJ',
                'Estimated_Revenue_PJ',
                'Estimated_Service_Cost_PJ',
                'Estimated_Material_Cost_PJ',
                'Estimated_Gross_Profit_PJ',
                'Estimated_Gross_ProfitMargin_PJ',
            ],
            DB::table('collab_newjob')
                ->select(
                    'Refcode',
                    'Site_Code',
                    'Job_Description',
                    'Office_Code',
                    'Customer_Region',
                    'Estimated_Revenue',
                    'Estimated_Service_Cost',
                    'Estimated_Material_Cost',
                    'Estimated_Gross_Profit',
                    'Estimated_Gross_ProfitMargin'
                )
                ->where('Refcode', 'like', $refcodePrefix . '%')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('collab_table_project85')
                        ->whereColumn(
                            'collab_table_project85.Refcode_PJ',
                            'collab_newjob.Refcode'
                        );
                })
        );

                                            // 2️⃣ ดึงข้อมูลจาก project16 มาแสดงตาม status ของ user
        $userStatus = Auth::user()->status; // เช่น "01_BKK" หรือ "admin"

        $projectRole = DB::table('collab_user_permissions85')
            ->where('user_id', Auth::id())
            ->value('project_role');

        $projectData = DB::table('collab_table_project85')
            ->when(! in_array($projectRole, ['Admin', 'Project Manager']), function ($query) use ($userStatus) {
                // ถ้าไม่ใช่ Admin และไม่ใช่ Project Manager
                $query->where('Office_Code_PJ', $userStatus);
            })
            ->orderBy('Refcode_PJ')
            ->get();

        //dd($projectData);

        // 3️⃣ user ที่ใช้จัด permission
        $users = DB::table('users')
            ->orderBy('name')
            ->get();

        // 4️⃣ permissions (ผูกกับ project_code = 85)
        $permissions = DB::table('collab_user_permissions85')
            ->where('project_code', $projectCode)
            ->get()
            ->keyBy('user_id');

        // 5️⃣ ชื่อโปรเจค (แสดงผล)
        $projectName = '85_True Site Preparation';

        //dd($permissions);

        return view(
            'user.projectdatabases.projectview_85',
            compact('projectData', 'users', 'permissions', 'projectName', 'projectCode')
        );

    }

    public function save85(Request $request)
    {
        //dd('ok');
        $userIds = array_keys($request->member_status);

        foreach ($userIds as $userId) {

            $data = [
                'member_status'                    => $request->member_status[$userId] ?? 'no',
                'project_role'                     => $request->project_role[$userId] ?? null,

                // ✅ อ่านจาก *_permission
                'Customer_Region_PJ'               => $request->Customer_Region_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Revenue_PJ'             => $request->Estimated_Revenue_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Service_Cost_PJ'        => $request->Estimated_Service_Cost_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Material_Cost_PJ'       => $request->Estimated_Material_Cost_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Transportation_Cost_PJ' => $request->Estimated_Transportation_Cost_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Other_Cost_PJ'          => $request->Estimated_Other_Cost_PJ_permission[$userId] ?? 'invisible',

                // 🔥 เพิ่ม 2 ตัวนี้
                'Estimated_Gross_Profit_PJ'        => $request->Estimated_Gross_Profit_PJ_permission[$userId] ?? 'invisible',
                'Estimated_Gross_Profit_Margin_PJ' => $request->Estimated_Gross_Profit_Margin_PJ_permission[$userId] ?? 'invisible',
            ];

            //dd($data);

            // col 1 - col 50
            for ($i = 1; $i <= 50; $i++) {
                $data["col{$i}"] = $request->{"col{$i}_permission"}[$userId] ?? 'invisible';
            }

            //dd($data);

            DB::table('collab_user_permissions85')->updateOrInsert(
                [
                    'user_id'      => $userId,
                    'project_code' => $request->project_code,
                ],
                $data
            );
        }

        return back()->with('success', 'Permissions saved successfully!');
    }

    public function inlineUpdate85(Request $request)
    {
        $request->validate([
            'id'    => 'required|string', // Refcode_PJ
            'field' => 'required|string',
            'value' => 'nullable|string',
        ]);

        /* ===============================
       1) allow fields
       =============================== */
        $allowedCols = [];
        for ($i = 1; $i <= 50; $i++) {
            $allowedCols[] = 'col' . $i;
        }

        $moneyFields = [
            'Estimated_Revenue_PJ',
            'Estimated_Service_Cost_PJ',
            'Estimated_Material_Cost_PJ',
            'Estimated_Transportation_Cost_PJ',
            'Estimated_Other_Cost_PJ',

            'col22',
            'col24',
            'col26',
        ];

        $projectFields = array_merge([
            'Customer_Region_PJ',
        ], $moneyFields);

        if (! in_array($request->field, array_merge($allowedCols, $projectFields))) {
            return response()->json(['success' => false], 403);
        }

        /* ===============================
       2) permission
       =============================== */
        $permission = DB::table('collab_user_permissions85')
            ->where('user_id', Auth::id())
            ->where('project_code', '85')
            ->first();

        if (! $permission || $permission->member_status !== 'yes') {
            return response()->json(['success' => false], 403);
        }

        if (
            ! isset($permission->{$request->field}) ||
            $permission->{$request->field} !== 'write'
        ) {
            return response()->json(['success' => false], 403);
        }

        DB::beginTransaction();

        try {

            /* ===============================
           3) prepare value
           =============================== */
            $rawValue   = trim((string) $request->value);
            $storeValue = $rawValue;

            // ถ้าเป็น field เงิน → format ให้มี comma + 2 decimals
            if (in_array($request->field, $moneyFields)) {
                $numeric    = (float) str_replace(',', '', $rawValue);
                $storeValue = number_format($numeric, 2, '.', ',');
            }

            /* ===============================
           4) update collab_table_project83
           =============================== */
            DB::table('collab_table_project85')
                ->where('Refcode_PJ', $request->id)
                ->update([
                    $request->field => $storeValue,
                ]);

            /* ===============================
           5) sync basic fields → collab_newjob
           =============================== */
            $syncMap = [
                'Customer_Region_PJ'         => 'Customer_Region',
                'Estimated_Revenue_PJ'       => 'Estimated_Revenue',
                'Estimated_Service_Cost_PJ'  => 'Estimated_Service_Cost',
                'Estimated_Material_Cost_PJ' => 'Estimated_Material_Cost',
            ];

            if (isset($syncMap[$request->field])) {
                DB::table('collab_newjob')
                    ->where('Refcode', $request->id)
                    ->update([
                        $syncMap[$request->field] => $storeValue,
                    ]);
            }

            /* ===============================
           6) calculate gross (money only)
           =============================== */
            if (in_array($request->field, $moneyFields)) {

                $row = DB::table('collab_table_project85')
                    ->where('Refcode_PJ', $request->id)
                    ->first();

                $revenue  = (float) str_replace(',', '', $row->Estimated_Revenue_PJ ?? 0);
                $service  = (float) str_replace(',', '', $row->Estimated_Service_Cost_PJ ?? 0);
                $material = (float) str_replace(',', '', $row->Estimated_Material_Cost_PJ ?? 0);

                $grossProfit = $revenue - $service - $material;
                $grossMargin = $revenue > 0
                    ? ($grossProfit / $revenue) * 100
                    : 0;

                $grossProfitFormatted = number_format($grossProfit, 2, '.', ',');
                $grossMarginFormatted = number_format($grossMargin, 2, '.', ',');

                $col22 = (float) str_replace(',', '', $row->col22 ?? 0);
                $col24 = (float) str_replace(',', '', $row->col24 ?? 0);
                $col26 = (float) str_replace(',', '', $row->col26 ?? 0);

                $col27 = $col22 - $col24 - $col26;
                $col27Formatted = number_format($col27, 2, '.', ',');   

                // update project table
                DB::table('collab_table_project85')
                    ->where('Refcode_PJ', $request->id)
                    ->update([
                        'Estimated_Gross_Profit_PJ'       => $grossProfitFormatted,
                        'Estimated_Gross_ProfitMargin_PJ' => $grossMarginFormatted,
                        'col27'                           => $col27Formatted,
                    ]);

                // sync → collab_newjob
                DB::table('collab_newjob')
                    ->where('Refcode', $request->id)
                    ->update([
                        'Estimated_Gross_Profit'       => $grossProfitFormatted,
                        'Estimated_Gross_ProfitMargin' => $grossMarginFormatted,
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'value'   => $storeValue,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    




    
};
