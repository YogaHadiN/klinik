<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rekening;
use App\Asuransi;
use App\AbaikanTransaksi;
use App\Classes\Yoga;
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
		private $input_pembayaran_null;


   public function __construct()
    {
		$this->input_tanggal         = Input::get('tanggal');
		$this->input_displayed_rows  = Input::get('displayed_rows');
		$this->input_key             = Input::get('key');
		$this->input_deskripsi       = Input::get('deskripsi');
		$this->input_akun_bank_id    = Input::get('akun_bank_id');
		$this->input_pembayaran_null = Input::get('pembayaran_null');
    }
	public function index($id){
		try {
			Artisan::call('cek:mutasi20terakhir');
		} catch (\Exception $e) {
			$pesan = Yoga::gagalFlash('Mutasi Moota gagal');
			session(['pesan' => $pesan]);
		}

		$ignored     = AbaikanTransaksi::all();
		$ignored_ids = [];
		foreach ($ignored as $ignore) {
			$ignored_ids[] = $ignore->transaksi_id;
		}
		$rekening = Rekening::where('akun_bank_id', $id)
			->where('debet', '0')
			->where('deskripsi', 'not like', '%cs-cs%')
			->whereNotIn('id', $ignored_ids)
			->orderBy('tanggal', 'desc')->first();

		return view('rekenings.index', compact('rekening', 'ignored_ids'));
	}
	public function search(){

		$pass                  = $this->input_key * $this->input_displayed_rows;
		$this->input_deskripsi = str_split($this->input_deskripsi);
		$str_tanggal           = $this->input_tanggal . '%';
		$str_deskripsi         = '%';
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
		$query .= "AND id not in (" . $this->ignoredId() . ")";
		if ( $this->input_pembayaran_null == '1' ) {
			$query .= "AND pembayaran_asuransi_id = '' ";
		} else if (   $this->input_pembayaran_null == '2' ){
			$query .= "AND pembayaran_asuransi_id not like '' ";
		}
		$query .= "AND ";
		$query .= "(deskripsi like '{$str_deskripsi}' and tanggal like '{$str_tanggal}') ";
		$query .= "ORDER BY tanggal desc, created_at desc ";
		/* dd($query); */
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
		$id          = Input::get('id');
		$kata_kunci  = Input::get('kata_kunci');
		$asuransi_id = Input::get('asuransi_id');
		$rekening_available = '0';
		$kata_kunci_valid  = '1';
		try {
			Rekening::findOrFail($id);
			$rekening_available = '1';
		} catch (\Exception $e) {
			$rekening_available = '0';
		}

		try {
			Asuransi::where('id', '!=', $asuransi_id )
				->where('kata_kunci', $kata_kunci)
				->firstOrFail();
			$kata_kunci_valid = '0';
		} catch (\Exception $e) {
			$kata_kunci_valid = '1';
		}

		return compact('rekening_available', 'kata_kunci_valid');
	}
	public function cekId(){
		$id = Input::get('id');
		try {
			return Rekening::findOrFail($id);
		} catch (\Exception $e) {
	
		}
	}
	private function ignoredId(){
		$ignored_ids = '';
		$abaikans = AbaikanTransaksi::all();
		foreach ($abaikans as $k => $abaikan) {
			if ($k == 0) {
				$ignored_ids .= "'" . $abaikan->transaksi_id . "'" ;
			} else {
				$ignored_ids .= ", '" . $abaikan->transaksi_id . "'";
			}
		}
		return $ignored_ids;
	}
}
