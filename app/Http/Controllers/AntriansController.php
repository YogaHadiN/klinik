<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Antrian;

class AntriansController extends Controller
{
	public function create(){
		return view('antrians.create');
	}
	public function store(){

		try {
			$antrian = Antrian::where('created_at', date('Y-m-d') . '%')->firstOfFail();
			$a->antrian_terakhir = (int) $a->antrian_terakhir + 1;
			$a->save();
		} catch (\Exception $e) {
			$antrian                   = new Antrian;
			$antrian->antrian_terakhir = 1;
			$antrian->save();
		}
		return $a->antrian_terakhir;
	}
}
