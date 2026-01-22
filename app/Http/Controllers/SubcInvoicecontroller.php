<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class SubcInvoicecontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $subc = DB::table('r_import_subcinvoice')->get();

        return view('ERP.subcinvoice.home', compact('subc'));
    }
}
