<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class Truecontroller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    //Data total
    public function index()
    {

        $newjob = DB::table('collab_newjob')
            ->whereRaw("LEFT(Project_Code, 2) = '16'")
            ->whereNotNull('Refcode')    // Refcode ต้องไม่เป็น null
            ->where('Refcode', '!=', '') // Refcode ต้องไม่ว่าง
            ->orderBy('id', 'ASC')       // เก่า → ใหม่
            ->get();

        return view('ProjectDatabase.98True.home', compact('newjob'));
        // OK

    }


}
