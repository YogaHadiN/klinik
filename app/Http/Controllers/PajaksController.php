<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\BelanjaPeralatan;
use App\BahanBangunan;
use DB;
use Input;

class PajaksController extends Controller
{

	public function __construct()
	 {
		 $this->middleware('super', ['except' => []]);
	 }
	public function amortisasi(){
		$query  = "select year(created_at) as tahun from penyusutans group by YEAR(created_at)";
		$data = DB::select($query);

		$lists = [];

		foreach ($data as $d) {
			$year = $d->tahun;
			$lists[$d->tahun] = $d->tahun;
		}
		return view('pajaks.amortisasi', compact(
			'lists'
		));
		
	}
	
	public function amortisasiPost(){
		$tahun           = Input::get('tahun');
		$peralatans      = $this->queryAmortisasi( 'peralatan', 'belanja_peralatans', 'App\\\BelanjaPeralatan', 'fb.tanggal', $tahun);
		$bahan_bangunans = $this->queryAmortisasi( 'keterangan', 'bahan_bangunans', 'App\\\BahanBangunan', 'bp.tanggal_renovasi_selesai', $tahun);
		return view('pajaks.amortisasiPost', compact(
			'peralatans',
			'tahun',
			'bahan_bangunans'
		));
	}
	private function amortisasiArray($datas){
		$peralatans = [];
		foreach ($datas as $d) {
			$peralatans[ $d->susutable_id ]['susutable_id'] = $d->susutable_id;
			$peralatans[ $d->susutable_id ]['tanggal_perolehan'] = $d->tanggal;
			$peralatans[ $d->susutable_id ]['harga_perolehan'] = $d->harga_satuan * $d->jumlah;
			$peralatans[ $d->susutable_id ]['peralatan'] = $d->peralatan;
			if (
				!isset($peralatans[ $d->susutable_id ]['penyusutan_2_sebelumnya']) 
			) {
				$peralatans[ $d->susutable_id ]['penyusutan_2_sebelumnya'] = 0;
			} 
			if (date('Y',strtotime($d->tanggal_penyusutan)) < ( date('Y') -1 )) {
				$peralatans[ $d->susutable_id ]['penyusutan_2_sebelumnya'] += $d->nilai;
			}
			if (
				!isset($peralatans[ $d->susutable_id ]['total_penyusutan'])
			) {
				$peralatans[ $d->susutable_id ]['total_penyusutan'] = 0;
			} 
			$peralatans[ $d->susutable_id ]['total_penyusutan'] += $d->nilai;
		}
		return $peralatans;
	}
	public function queryAmortisasi(
		$peralatan,
		$belanja_peralatans,
		$BelanjaPeralatan,
		$tanggal,
		$tahun
	){
		$tahun_pajak = $tahun +1;
		$first_date_of_the_year          = date($tahun_pajak .'-01-01');//2017-01-01 00:00:00
		/* return 'first_date_of_the_year = ' . $first_date_of_the_year; */
		$first_date_of_the_previous_year = date('Y-m-d H:i:s', strtotime("-1 year " . $first_date_of_the_year)); //2016-01-01 00:00:00
		/* return 'first_date_of_the_previous_year = ' . $first_date_of_the_previous_year; */

		$query  = "SELECT ";
		$query .= "bp.{$peralatan} as peralatan, ";
		$query .= "DATE_FORMAT({$tanggal}, '%M %Y') as tanggal_perolehan, ";
		$query .= "SUM(CASE WHEN pn.created_at < '{$first_date_of_the_previous_year}' THEN pn.nilai ELSE 0 END) AS susut_fiskal_tahun_lalu,";
		$query .= "SUM(nilai) AS total_penyusutan, ";
		$query .= "bp.harga_satuan as harga_satuan, ";
		if ($BelanjaPeralatan != 'App\\\BahanBangunan') {
			$query .= "bp.masa_pakai as masa_pakai, ";
		}
		if ($BelanjaPeralatan == 'App\\\BahanBangunan') {
			$query .= "bp.bangunan_permanen as permanen, ";
		}
		$query .= "bp.jumlah as jumlah, ";
		$query .= "pn.created_at as tanggal_penyusutan, ";
		$query .= "fb.tanggal as tanggal ";
		$query .= "FROM penyusutans as pn ";
		$query .= "JOIN {$belanja_peralatans} as bp on bp.id = pn.susutable_id ";
		$query .= "JOIN faktur_belanjas as fb on fb.id = bp.faktur_belanja_id ";
		$query .= "WHERE pn.created_at < '{$first_date_of_the_year}'";
		$query .= "AND pn.susutable_type = '{$BelanjaPeralatan}' ";
		$query .= "GROUP BY pn.susutable_id";

		//return $query;
		return DB::select($query);
	}
	public function peredaranBrutoPost(){
		$tahun = Input::get('tahun');
		$peredaranBruto = $this->queryPeredaranBruto($tahun);
		return view('pajaks.peredaranBrutoPost', compact(
			'peredaranBruto', 'tahun'
		));
	}
	public function peredaranBruto(){
		$pluck = $this->pluckTahun();
		$bikinan = false;
		return view('pajaks.peredaranBruto', compact(
			'bikinan',
			'pluck'
		));
	}
	
	public function queryPeredaranBruto($tahun){
		$query  = "SELECT  ";
		$query .= "ju.jurnalable_type, ";
		$query .= "px.asuransi_id, ";
		$query .= "DATE_FORMAT( ju.created_at, '%M' ) as bulan, ";
		$query .= "CASE WHEN debit = 0 THEN nilai ELSE nilai * -1 END as nilai ";
		$query .= "FROM jurnal_umums as ju ";
		$query .= "JOIN coas as co on co.id = ju.coa_id ";
		$query .= "left JOIN periksas as px on px.id = ju.jurnalable_id ";
		$query .= "WHERE ju.created_at like '{$tahun}%'";
		$query .= "AND (coa_id like '4%' "; //Pendapatan Usaha
		$query .= "OR coa_id like '7%') "; //Pendapatan Lain

		$data = DB::select($query);

		$perBulan = [
			  "January"   => 0,
			  "February"  => 0,
			  "March"     => 0,
			  "April"     => 0,
			  "May"       => 0,
			  "June"      => 0,
			  "July"      => 0,
			  "August"    => 0,
			  "September" => 0,
			  "October"   => 0,
			  "November"  => 0,
			  "December"  => 0
			];
		foreach ($data as $d) {
			if ( $d->jurnalable_type != 'App\Periksa' || $d->asuransi_id !=0 ) {
				$perBulan[ $d->bulan ] += $d->nilai;
			} 	
		}

		$total = 0;
		foreach ($perBulan as $n) {
			$total += $n;
		}

		dd($total);
	}
	public function peredaranBrutoBikinan(){
		$pluck   = $this->pluckTahun();
		$bikinan = true;
		return view('pajaks.peredaranBruto', compact(
			'bikinan',
			'pluck',
		));
	}

	public function peredaranBrutoBikinanPost(){
		$tahun = Input::get('tahun');
		$peredaranBruto = $this->queryPeredaranBruto($tahun);
		return view('pajaks.peredaranBrutoPost', compact(
			'peredaranBruto', 'tahun'
		));
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function pluckTahun()
	{
		$query  = "select year(created_at) as tahun from jurnal_umums group by YEAR(created_at)";
		$data = DB::select($query);

		$pluck = [];

		foreach ($data as $d) {
			$year = $d->tahun;
			$pluck[$d->tahun] = $d->tahun;
		}
		return $pluck;
	}
	
	
}
