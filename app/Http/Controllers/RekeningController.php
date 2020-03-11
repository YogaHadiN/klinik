<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rekening;
use Input;
use Artisan;
use DB;

class RekeningController extends Controller
{

		private $input_tanggal;
		private $input_displayed_rows;
		private $input_key;
		private $input_deskripsi;
		private $input_akun_bank_id;

   public function __construct()
    {
		$this->input_tanggal        = Input::get('tanggal');
		$this->input_displayed_rows = Input::get('displayed_rows');
		$this->input_key            = Input::get('key');
		$this->input_deskripsi      = Input::get('deskripsi');
		$this->input_akun_bank_id   = Input::get('akun_bank_id');
    }
	public function index($id){
		Artisan::call('cek:mutasi20terakhir');
		$rekening = Rekening::where('akun_bank_id', $id)
			->where('debet', '0')
			->orderBy('tanggal', 'desc')->first();
		return view('rekenings.index', compact('rekening'));
	}
	public function search(){

		$pass = $this->input_key * $this->input_displayed_rows;

		$this->input_deskripsi = str_split($this->input_deskripsi);

		$str_tanggal = $this->input_tanggal . '%';
				
		$str_deskripsi ='%';
		foreach ($this->input_deskripsi as $k => $t) {
			if ($k != 0) {
				$str_deskripsi = $str_deskripsi . '%' . $t;
			} else {
				$str_deskripsi = $str_deskripsi . $t;
			}
		}
		$str_deskripsi = $str_deskripsi . '%';
		$data          = $this->queryData( $str_deskripsi, $str_tanggal, $pass);
		$count         = $this->queryData( $str_deskripsi, $str_tanggal, $pass, true);

		$pages = ceil( $count/ $this->input_displayed_rows );
		/* return $query; */
		return [
			'data'  => $data,
			'pages' => $pages,
			'key'   => $this->input_key,
			'rows'  => $count
		];

	}
	private function queryData(
		$str_deskripsi,
		$str_tanggal,
		$pass,
		$count = false
	){
		$query  = "SELECT ";
		if (!$count) {
			$query .= "str_to_date(tanggal, '%Y-%m-%d') as tanggal, ";
			$query .= "deskripsi, ";
			$query .= "id, ";
			$query .= "pembayaran_asuransi_id, ";
			$query .= "nilai, ";
			$query .= "saldo_akhir ";
		} else {
			$query .= "count(id) as jumlah ";
		}
		$query .= "FROM rekenings ";
		$query .= "WHERE akun_bank_id = '{$this->input_akun_bank_id}' ";
		$query .= "AND debet = 0 ";
		$query .= "AND deskripsi not like '%PURI WIDIYANI MARTIADEWI%' ";
		$query .= "AND deskripsi not like '%Bunga Rekening%' ";
		$query .= "AND ";
		$query .= "(deskripsi like '{$str_deskripsi}' and tanggal like '{$str_tanggal}') ";
		$query .= "ORDER BY tanggal desc, created_at desc ";
		if (!$count) {
			$query .= "LIMIT {$pass}, {$this->input_displayed_rows};";
		}
		if (!$count) {
			return DB::select($query);
		} else {
			/* dd(DB::select($query)[0]->jumlah); */
			return DB::select($query)[0]->jumlah;
		}
	}
	public function available(){
		$id = Input::get('id');
		try {
			Rekening::findOrFail($id);
			return '1';
		} catch (\Exception $e) {
			return '0';
		}
	}
	public function cekId(){
		$id = Input::get('id');
		try {
			return Rekening::findOrFail($id);
		} catch (\Exception $e) {
	
		}
	}
}
