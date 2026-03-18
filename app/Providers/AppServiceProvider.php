<?php
namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

// ⭐ อันนี้แหละที่ขาด!

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        View::composer('*', function ($view) {

            if (! Auth::check()) {
                return;
            }

            $user = Auth::user();

            // ================= ADMIN =================
            if ($user->status === 'Admin') {

                // 🔵 งานที่ต้องพิจารณา
                $unreadNotifications = DB::table('collab_newjob')
                    ->where('admin_read', 0)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $readNotifications = DB::table('collab_newjob')
                    ->where('admin_read', 1)
                    ->orderBy('updated_at', 'desc')
                    ->limit(20)
                    ->get();
            }

            // ================= USER =================
            else {

                // 🔔 ผลการอนุมัติใหม่
                $unreadNotifications = DB::table('collab_newjob')
                    ->where('Requester', $user->name)
                    ->where('user_read', 0)
                    ->whereIn('Job_Adding_Status', ['Approved', 'Rejected'])
                    ->orderBy('updated_at', 'desc')
                    ->get();

                $readNotifications = DB::table('collab_newjob')
                    ->where('Requester', $user->name)
                    ->where('user_read', 1)
                    ->orderBy('updated_at', 'desc')
                    ->limit(20)
                    ->get();
            }

            $view->with(compact('unreadNotifications', 'readNotifications'));
        });

        View::composer('layouts.user', function ($view) {

            // ตรวจสอบ Project 90 ว่ามี member_status = 'yes' หรือไม่
            $userId            = Auth::id(); 
            $showProjectView16 = DB::table('collab_user_permissions90')
                ->where('project_code', 'like', '90%')
                ->where('user_id', $userId)
                ->where('member_status', 'yes')
                ->exists();
           


            // ตรวจสอบ Project 83 ว่ามี member_status = 'yes' หรือไม่
            $userId            = Auth::id(); 
            $showProjectView83 = DB::table('collab_user_permissions83')
                ->where('project_code', 'like', '83%')
                ->where('user_id', $userId)
                ->where('member_status', 'yes')
                ->exists();
           


            // ตรวจสอบ Project 85 ว่ามี member_status = 'yes' หรือไม่
            $userId            = Auth::id(); 
            $showProjectView85 = DB::table('collab_user_permissions85')
                ->where('project_code', 'like', '85%')
                ->where('user_id', $userId)
                ->where('member_status', 'yes')
                ->exists();
            

            // ตรวจสอบ Project 88 ว่ามี member_status = 'yes' หรือไม่
            $userId            = Auth::id(); 
            $showProjectView88 = DB::table('collab_user_permissions88')
                ->where('project_code', 'like', '88%')
                ->where('user_id', $userId)
                ->where('member_status', 'yes')
                ->exists();
			
			// ตรวจสอบ Project 84 ว่ามี member_status = 'yes' หรือไม่
            $userId            = Auth::id(); 
            $showProjectView84  = DB::table('collab_user_permissions84')
                ->where('project_code', 'like', '84%')
                ->where('user_id', $userId)
                ->where('member_status', 'yes')
                ->exists();  

            $view->with([
                'showProjectView16' => $showProjectView16,
                'showProjectView83' => $showProjectView83,
                'showProjectView85' => $showProjectView85,
                'showProjectView88' => $showProjectView88,
				'showProjectView84' => $showProjectView84
            ]);
        });

    }

}
