<?php

namespace App\Http\Controllers;

use Input;
use App\Http\Requests;
use App\Asuransi;
use App\Tarif;
use App\BayarDokter;
use App\Classes\Yoga;

class BayarDoktersController extends Controller
{
    public function index(){
        $bayardokters = BayarDokter::latest()->paginate(15);
        return view('bayar_dokters.index', compact('bayardokters'));
    }
        
    //
}
