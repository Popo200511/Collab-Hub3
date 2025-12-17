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

        // 2️⃣ ดึงข้อมูลจาก project16 มาแสดง
        $projectData = DB::table('collab_table_project16')
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
        $userIds = array_keys($request->member_status); // user IDs

        foreach ($userIds as $userId) {
            $data = [
                'member_status' => $request->member_status[$userId] ?? 'no',
                'project_role'  => $request->project_role[$userId] ?? null,
            ];

            // loop col1 - col50
            for ($i = 1; $i <= 50; $i++) {
                $colName        = "col{$i}";
                $data[$colName] = $request->{"col{$i}_permission"}[$userId] ?? 'invisible';
            }

            // update หรือ insert
            DB::table('collab_user_permissions')->updateOrInsert(
                ['user_id' => $userId, 'project_code' => $request->project_code],
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

        // อนุญาตเฉพาะ col1 - col50
        $allowedFields = [];
        for ($i = 1; $i <= 50; $i++) {
            $allowedFields[] = 'col' . $i;
        }

        if (! in_array($request->field, $allowedFields)) {
            return response()->json(['success' => false], 403);
        }

        if (! in_array($request->field, $allowedFields)) {
            return response()->json(['success' => false], 403);
        }

        // permission ของ user
        $permission = DB::table('collab_user_permissions')
            ->where('user_id', Auth::id())
            ->where('project_code', '16')
            ->first();

        if (! $permission || $permission->member_status !== 'yes') {
            return response()->json(['success' => false], 403);
        }

        // ต้องเป็น write เท่านั้น
        if ($permission->{$request->field} !== 'write') {
            return response()->json(['success' => false], 403);
        }

        // update จริง 
        $updated = DB::table('collab_table_project16')
            ->where('Refcode_PJ', $request->id)
            ->update([
                $request->field => $request->value,
            ]);

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }

};