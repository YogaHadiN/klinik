<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rekening;
use Input;
use DB;

class RekeningController extends Controller
{
	public function index($id){
		$rekenings = Rekening::where('akun_bank_id', $id)
			/* ->where('debet', '0') */
			->orderBy('tanggal', 'desc')->paginate(20);
		return view('rekenings.index', compact(
			'rekenings'
		));
	}
	public function search(){
		$tanggal   = Input::get('tanggal');
		$deskripsi = Input::get('deskripsi');
		$akun_bank_id = Input::get('akun_bank_id');

		$deskripsi = str_split($deskripsi);

		$str_tanggal = $tanggal . '%';
				
		$str_deskripsi ='%';
		foreach ($deskripsi as $k => $t) {
			if ($k != 0) {
				$str_deskripsi = $str_deskripsi . '%' . $t;
			} else {
				$str_deskripsi = $str_deskripsi . $t;
			}
		}
		$str_deskripsi = $str_deskripsi . '%';

		$query  = "SELECT ";
		$query .= "str_to_date(tanggal, '%Y-%m-%d') as tanggal, ";
		$query .= "deskripsi, ";
		$query .= "nilai, ";
		$query .= "saldo_akhir ";
		$query .= "FROM rekenings ";
		$query .= "WHERE akun_bank_id = '{$akun_bank_id}' ";
		$query .= "AND debet = 0 ";
		$query .= "AND ";
		$query .= "(deskripsi like '{$str_deskripsi}' and tanggal like '{$str_tanggal}') ";
		$query .= "ORDER BY tanggal desc, created_at desc;";

		$data = DB::select($query);
		/* return $query; */
		return $data;

	}
	
}
