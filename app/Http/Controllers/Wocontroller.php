<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class Wocontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $wo = DB::table('r_import_wo')
             ->orderBy('id', 'desc') // แนะนำให้เรียงลำดับด้วยครับ จะได้ดูง่าย
            ->paginate(100);


        $count = DB::table('r_import_wo')->count();

        return view('user.erp.wo.home', compact('wo', 'count'));
    }
}
