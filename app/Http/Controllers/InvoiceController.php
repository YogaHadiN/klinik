<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PiutangAsuransi;

class InvoiceController extends Controller
{
	public function show($id){
		$id = str_replace('!', '/', $id);

		$piutangs = PiutangAsuransi::with('periksa.pasien','periksa.asuransi')->where('invoice_id', $id)->get();
		return view('invoices.show', compact(
			'piutangs'
		));
	}
	public function test($id){
		dd('test');
	}
	
}
