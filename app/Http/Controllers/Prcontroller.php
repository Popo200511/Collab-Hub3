<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Prcontroller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    // แปลง A → 0, B → 1, Z → 25, AA → 26 เป็นต้น
    private function excelColumnToIndex($letters)
    {
        $result = 0;
        $len    = strlen($letters);

        for ($i = 0; $i < $len; $i++) {
            $result = $result * 26 + (ord($letters[$i]) - 64);
        }
        return $result - 1;
    }

    // pr.purchase
    public function purchase()
    {
        // ใช้ paginate แทน get เพื่อลดภาระ RAM
        $importpurchase = DB::table('r_import_purchase')
                        ->orderBy('id', 'desc') // แนะนำให้เรียงลำดับด้วยครับ จะได้ดูง่าย
                        ->paginate(100);
                        
                        
        $recordCount    = DB::table('r_import_purchase')->count();

        return view('user.erp.pr.purchase', compact('importpurchase', 'recordCount'));
    }

    

}
