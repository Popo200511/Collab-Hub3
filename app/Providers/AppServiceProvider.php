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
        View::composer('layouts.Tailwind', function ($view) {

            // Pending
            $pendingJobs = DB::table('collab_newjob')
                ->where('Job_Adding_Status', 'Pending')
                ->get();

            // Notifications
            if (Auth::check()) {
                $requester = Auth::user()->name;

                $countNotifications = DB::table('collab_newjob')
                    ->where('Requester', $requester)
                    ->where('is_read', 0)
                    ->whereIn('Job_Adding_Status', ['Approved', 'Rejected'])
                    ->count();

                $notifications = DB::table('collab_newjob')
                    ->where('Requester', $requester)
                    ->whereIn('Job_Adding_Status', ['Approved', 'Rejected'])
                    ->orderBy('is_read', 'asc')
                    ->orderBy('id', 'desc')
                    ->get();
            } else {
                $countNotifications = 0;
                $notifications      = collect([]);
            }

            $view->with([
                'pendingJobs'        => $pendingJobs,
                'countPending'       => $pendingJobs->count(),
                'notifications'      => $notifications,
                'countNotifications' => $countNotifications,
            ]);
        });

        View::composer('layouts.user', function ($view) {

                                             // ตรวจสอบ Project 16 ว่ามี member_status = 'yes' หรือไม่
            $userId            = Auth::id(); // หรือ user ที่ต้องการเช็ค
            $showProjectView16 = DB::table('collab_user_permissions')
                ->where('project_code', 'like', '16%')
                ->where('user_id', $userId)
                ->where('member_status', 'yes')
                ->exists();
            //dd($showProjectView16);

            $view->with([
                'showProjectView16' => $showProjectView16,
            ]);
        });

    }

}