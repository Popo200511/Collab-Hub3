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
        $wo = DB::table('r_import_wo')->get();

        return view('ERP.wo.home', compact('wo'));
    }
}
