<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rekening;

class RekeningController extends Controller
{
	public function index($id){
		$rekenings = Rekening::where('akun_bank_id', $id)
			->where('debet', '0')
			->orderBy('tanggal', 'desc')->paginate(20);
		return view('rekenings.index', compact(
			'rekenings'
		));
	}
}
