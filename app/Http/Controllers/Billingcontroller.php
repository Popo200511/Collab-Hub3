<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Billingcontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $billing = DB::table('r_import_biling')->orderBy('id','asc')->paginate(100);
        $count   = DB::table('r_import_biling')->count('refCode');

        //dd($billing);

        return view('user.erp.billing.home', compact('billing', 'count'));
    }


}
