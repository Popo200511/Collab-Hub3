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

    public function project16(Request $request)
    {
        $projectCode   = 16;
        $refcodePrefix = '16-';

        // 1️⃣ คัดลอกข้อมูลจาก newjob → project16 (กันซ้ำ)
        DB::table('collab_table_project16')->insertUsing(
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
                        ->from('collab_table_project16')
                        ->whereColumn(
                            'collab_table_project16.Refcode_PJ',
                            'collab_newjob.Refcode'
                        );
                })
        );

                                            // 2️⃣ ดึงข้อมูลจาก project16 มาแสดงตาม status ของ user
        $userStatus = Auth::user()->status; // เช่น "01_BKK" หรือ "admin"

        $projectData = DB::table('collab_table_project16')
            ->when($userStatus !== 'Admin', function ($query) use ($userStatus) {
                // ถ้าไม่ใช่ admin ให้กรองตาม Office_Code_PJ
                return $query->where('Office_Code_PJ', $userStatus);
            })
            ->orderBy('Refcode_PJ')
            ->get();

        // 3️⃣ user ที่ใช้จัด permission
        $users = DB::table('users')
            ->orderBy('name')
            ->get();

        // 4️⃣ permissions (ผูกกับ project_code = 16)
        $permissions = DB::table('collab_user_permissions')
            ->where('project_code', $projectCode)
            ->get()
            ->keyBy('user_id');

        // 5️⃣ ชื่อโปรเจค (แสดงผล)
        $projectName = '16_Other Customer Projects';

        //dd($showProjectView16);

        return view(
            'user.projectdatabases.projectview',
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

            DB::table('collab_user_permissions')->updateOrInsert(
                [
                    'user_id'      => $userId,
                    'project_code' => $request->project_code,
                ],
                $data
            );
        }

        return back()->with('success', 'Permissions saved successfully!');
    }

    public function inlineUpdate(Request $request)
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
        $permission = DB::table('collab_user_permissions')
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
           2) update collab_table_project16
           =============================== */
            DB::table('collab_table_project16')
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

                $row = DB::table('collab_table_project16')
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
                DB::table('collab_table_project16')
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

};