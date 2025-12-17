<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class Dropdowncontroller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    function total(){
        $areas = DB::table('area')->get();

        $data = DB::table('main_csv as m')
            ->leftJoin('saq_csv as s', 'm.id', '=', 's.id_saq')
            ->leftJoin('cr_csv as c', 'm.id', '=', 'c.id_cr')
            ->leftJoin('tssr_csv as t', 'm.id', '=', 't.id_tssr')
            ->leftJoin('civilwork_csv as cw', 'm.id', '=', 'cw.id_cw')
            ->get();
        
        return view('are', compact('areas','data'));
      
    }

}
    
    
