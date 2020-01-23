<?php

namespace App\Http\Controllers;

use Input;

use App\Http\Requests;

use App\FakturBeli;
use App\NotaJual;
use App\JurnalUmum;
use App\Classes\Yoga;
use App\Supplier;

class NotaJualsController extends Controller
{

    public function index(){
        $nota_juals = NotaJual::latest()->get();
        return view('nota_juals.index', compact('nota_juals'));
    }
    public function show($id){
         $nota_jual = NotaJual::find($id);
		 $jurnalumums = JurnalUmum::where('jurnalable_type', 'App\NotaJual')
								 ->where('jurnalable_id', $id)
								 ->groupBy('jurnalable_id')
								 ->get();
         return view('nota_juals.show', compact('nota_jual', 'jurnalumums'));
    }
    public function edit($id){
         $nota_jual = NotaJual::find($id);
         return view('nota_juals.edit', compact('nota_jual'));
    }
    
    
}
