<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Periksa;
use Carbon\Carbon;
use Input;
use PDF;
use DB;
use App\Classes\Yoga;

class LaporanBpjsController extends Controller
{
	public $bulan;
	public function __construct()
	{
		/* dd(Input::all()); */ 
		$this->bulan    = Carbon::CreateFromFormat('m-Y',Input::get('bulanTahun'));
		
	}
	
	public function hipertensi(){
		return view('laporans.bpjs.hipertensi', [
			'periksas' => $this->queryHipertensi(),
			'bulan'    => $this->bulan
		]);
	}
	public function hipertensiPdf(){

		$pdf   = PDF::loadView(
					'laporans.bpjs.hipertensipdf', 
					[
						'periksas' => $this->queryHipertensi(),
						'bulan'    => $this->bulan
					])
				->setPaper('a4');
        return $pdf->stream();
	}
	
	public function dm(){
		$periksas = Periksa::with('diagnosa.icd10', 'pasien')
							->where('asuransi_id', '32')
							->whereRaw("tanggal like '{$this->bulan->format('Y-m')}%'" )
							->get();
		return view('laporans.bpjs.dm', [
			'periksas' => $this->periksas,
			'bulan'    => $this->bulan
		]);
	}
	
	public function diagnosa(){
		return view('laporans.bpjs.diagnosa', [
			'periksas' => $this->queryDiagnosaRujukan(),
			'bulan'    => $this->bulan
		]);
	}
	public function diagnosaPdf(){
		$pdf   = PDF::loadView(
					'laporans.bpjs.diagnosapdf', 
					[
						'periksas' => $this->queryDiagnosaRujukan(),
						'bulan'    => $this->bulan
					])
				->setPaper('a4');
        return $pdf->stream();
	}

	/**
	* undocumented function
	*
	* @return void
	*/
	private function queryDiagnosaRujukan()
	{
		$query  = "SELECT ";
		$query .= "prx.tanggal as tanggal, ";
		$query .= "psn.nama as nama_pasien, ";
		$query .= "psn.nomor_asuransi_bpjs as nomor_asuransi_bpjs, ";
		$query .= "concat( dgn.icd10_id, ' - ', icd.diagnosaICD ) as diagnosa ";
		$query .= "FROM periksas as prx ";
		$query .= "JOIN pasiens as psn on psn.id = prx.pasien_id ";
		$query .= "JOIN diagnosas as dgn on dgn.id = prx.diagnosa_id ";
		$query .= "JOIN icd10s as icd on icd.id = dgn.icd10_id ";
		$query .= "RIGHT JOIN rujukans as rjk on rjk.periksa_id = prx.id ";
		$query .= "WHERE prx.asuransi_id = 32 ";
		$query .= "AND tanggal like '{$this->bulan->format('Y-m')}%' ";
		return DB::select($query);
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function queryHipertensi()
	{
		$query  = "SELECT ";
		$query .= "prx.tanggal as tanggal, ";
		$query .= "psn.nama as nama_pasien, ";
		$query .= "psn.nomor_asuransi_bpjs as nomor_asuransi_bpjs, ";
		$query .= "prx.sistolik as sistolik, ";
		$query .= "prx.diastolik as diastolik, ";
		$query .= "psn.tanggal_lahir as tanggal_lahir, ";
		$query .= "TIMESTAMPDIFF(YEAR, psn.tanggal_lahir, CURDATE()) AS age ";
		$query .= "FROM periksas as prx ";
		$query .= "JOIN pasiens as psn on psn.id = prx.pasien_id ";
		$query .= "RIGHT JOIN rujukans as rjk on rjk.periksa_id = prx.id ";
		$query .= "WHERE prx.asuransi_id = 32 ";
		$query .= "AND tanggal like '{$this->bulan->format('Y-m')}%' ";
		$query .= "AND prx.sistolik not like '' ";
		$query .= "AND prx.diastolik not like '' ";
		return DB::select($query);
	}
	
	
	
}
