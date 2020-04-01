<?php

namespace App\Http\Controllers;

use Input;
use App\Http\Requests;
use App\AntrianPeriksa;
use App\JenisAntrian;
use App\Classes\Yoga;
use App\Periksa;
use App\Poli;
use Endroid\QrCode\QrCode;

class RuangPeriksaController extends Controller
{

	protected $staf_list;
	private $poli_list;

	public function __construct(){
		$this->staf_list = Yoga::stafList();
		$this->poli_list = Poli::pluck('poli', 'id')->all();
	}
	

	public function index($jenis_antrian_id){
		$jenis_antrian = JenisAntrian::find($jenis_antrian_id);

		$poli_ids = [];
		foreach ($jenis_antrian->poli_antrian as $poli) {
			$poli_ids[] = $poli->poli_id;
		}


		$antrianperiksa = AntrianPeriksa::with(
			'pasien',
			'antars',
			'antrian',
		   	'staf',
		   	'asuransi'
		)
			->whereIn('poli', $poli_ids)
			->get();

		$postperiksa = Periksa::with(
			'staf', 
			'asuransi', 
			'antrian',
			'pasien', 
			'suratSakit', 
			'rujukan.tujuanRujuk', 
			'kontrol'
			/* 'antars' */
		)->whereIn('poli', $poli_ids)
		->whereRaw("lewat_poli = 1 and lewat_kasir2 = 0")->orderBy('tanggal')->get();

		return view('antrianperiksas.index')
			->withPostperiksa($postperiksa)
			->withAntrianperiksa($antrianperiksa)
			->with('staf_list', $this->staf_list)
			->with('poli_list', $this->poli_list)
			->withPoli('umum');
	}
	/* public function umum(){ */
	/* 	$antrianperiksa = AntrianPeriksa::with( */
	/* 		'pasien', */
	/* 		'antars', */
	/* 	   	'staf', */
	/* 	   	'asuransi' */
	/* 	) */
	/* 		->where('poli', '=', 'umum') */
	/* 		->orWhere('poli', 'luka') */
	/* 		->orWhere('poli', 'sks') */
	/* 		->orderBy('antrian', 'asc') */
	/* 		->get(); */

	/* 	$postperiksa = Periksa::with( */
	/* 		'staf', */ 
	/* 		'asuransi', */ 
	/* 		'pasien', */ 
	/* 		'suratSakit', */ 
	/* 		'rujukan.tujuanRujuk', */ 
	/* 		'kontrol' */
	/* 		/1* 'antars' *1/ */
	/* 	)->whereRaw("lewat_poli = 1 and lewat_kasir2 = 0 and ( poli='umum' or poli='sks' or poli='luka' )")->orderBy('tanggal')->orderBy('antrian')->get(); */
	/* 	return view('antrianperiksas.index') */
	/* 		->withPostperiksa($postperiksa) */
	/* 		->withAntrianperiksa($antrianperiksa) */
	/* 		->with('staf_list', $this->staf_list) */
	/* 		->with('poli_list', $this->poli_list) */
	/* 		->withPoli('umum'); */
	/* } */
	/* public function kandungan(){ */
	/* 	$poli = 'kandungan'; */
	/* 	$antrianperiksa = AntrianPeriksa::with('pasien', 'staf', 'asuransi')->where('poli', '=', $poli)->get(); */
	/* 	$postperiksa = Periksa::with( */
	/* 		'staf', */ 
	/* 		'asuransi', */ 
	/* 		'pasien', */ 
	/* 		'suratSakit', */ 
	/* 		'rujukan.tujuanRujuk', */ 
	/* 		'kontrol' */
	/* 	)->whereRaw("lewat_poli = 1 and lewat_kasir2 = 0 and ( poli='kandungan' or poli='KB 1 Bulan' or poli='KB 3 Bulan')")->orderBy('tanggal')->orderBy('antrian')->get(); */
	/* 	return view('antrianperiksas.index') */
	/* 		->withPostperiksa($postperiksa) */
	/* 		->withAntrianperiksa($antrianperiksa) */
	/* 		->with('staf_list', $this->staf_list) */
	/* 		->with('poli_list', $this->poli_list) */
	/* 		->withPoli($poli); */
	/* } */
	/* public function suntikkb(){ */
	/* 	$antrianperiksa = AntrianPeriksa::with('pasien', 'staf', 'asuransi')->where('poli', 'like', 'kb %')->get(); */
	/* 	$postperiksa = Periksa::with( */
	/* 		'staf', */ 
	/* 		'asuransi', */ 
	/* 		'pasien', */ 
	/* 		'suratSakit', */ 
	/* 		'rujukan.tujuanRujuk', */ 
	/* 		'kontrol' */
	/* 	)->whereRaw("lewat_poli = 1 and lewat_kasir2 = 0 and poli='kandungan'")->orderBy('tanggal')->orderBy('antrian')->get(); */
	/* 	return view('antrianperiksas.index') */
	/* 		->withPostperiksa($postperiksa) */
	/* 		->withAntrianperiksa($antrianperiksa) */
	/* 		->with('staf_list', $this->staf_list) */
	/* 		->with('poli_list', $this->poli_list) */
	/* 		->withPoli('Suntik KB'); */
	/* } */
	/* public function anc(){ */
	/* 	$poli = 'anc'; */
	/* 	$antrianperiksa = AntrianPeriksa::with('pasien', 'staf', 'asuransi')->where('poli', '=', $poli)->get(); */
	/* 	$postperiksa = Periksa::with( */
	/* 		'staf', */ 
	/* 		'asuransi', */ 
	/* 		'pasien', */ 
	/* 		'suratSakit', */ 
	/* 		'rujukan.tujuanRujuk', */ 
	/* 		'kontrol' */
	/* 	)->whereRaw("lewat_poli = 1 and lewat_kasir2 = 0 and poli='{$poli}'")->orderBy('tanggal')->orderBy('antrian')->get(); */
	/* 	return view('antrianperiksas.index') */
	/* 		->withPostperiksa($postperiksa) */
	/* 		->withAntrianperiksa($antrianperiksa) */
	/* 		->with('staf_list', $this->staf_list) */
	/* 		->with('poli_list', $this->poli_list) */
	/* 		->withPoli($poli); */
	/* } */
    /* public function usg(){ */
	/* 	$poli = 'usg'; */
	/* 	$antrianperiksa = AntrianPeriksa::with('pasien', 'staf', 'asuransi')->where('poli', '=', $poli)->get(); */
	/* 	$postperiksa = Periksa::with( */
	/* 		'staf', */ 
	/* 		'asuransi', */ 
	/* 		'pasien', */ 
	/* 		'suratSakit', */ 
	/* 		'rujukan.tujuanRujuk', */ 
	/* 		'kontrol' */
	/* 	)->whereRaw("lewat_poli = 1 and lewat_kasir2 = 0 and poli='{$poli}'")->orderBy('tanggal')->orderBy('antrian')->get(); */
	/* 	return view('antrianperiksas.index') */
	/* 		->withPostperiksa($postperiksa) */
	/* 		->withAntrianperiksa($antrianperiksa) */
	/* 		->with('staf_list', $this->staf_list) */
	/* 		->with('poli_list', $this->poli_list) */
	/* 		->withPoli($poli); */
	/* } */
	/* public function usgabdomen(){ */
	/* 	$poli = 'usgabdomen'; */
	/* 	$antrianperiksa = AntrianPeriksa::with('pasien', 'staf', 'asuransi')->where('poli', '=', $poli)->get(); */
	/* 	$postperiksa = Periksa::with( */
	/* 		'staf', */ 
	/* 		'asuransi', */ 
	/* 		'pasien', */ 
	/* 		'suratSakit', */ 
	/* 		'rujukan.tujuanRujuk', */ 
	/* 		'kontrol' */
	/* 	)->whereRaw("lewat_poli = 1 and lewat_kasir2 = 0 and poli='{$poli}'")->orderBy('tanggal')->orderBy('antrian')->get(); */
	/* 	return view('antrianperiksas.index') */
	/* 		->withPostperiksa($postperiksa) */
	/* 		->withAntrianperiksa($antrianperiksa) */
	/* 		->with('staf_list', $this->staf_list) */
	/* 		->with('poli_list', $this->poli_list) */
	/* 		->withPoli($poli); */
	/* } */
	/* public function gigi(){ */
	/* 	$poli = 'gigi'; */
	/* 	$antrianperiksa = AntrianPeriksa::with('pasien', 'staf', 'asuransi')->where('poli', '=', $poli)->get(); */
	/* 	$postperiksa = Periksa::with( */
	/* 		'staf', */ 
	/* 		'asuransi', */ 
	/* 		'pasien', */ 
	/* 		'suratSakit', */ 
	/* 		'rujukan.tujuanRujuk', */ 
	/* 		'kontrol' */
	/* 	)->whereRaw("lewat_poli = 1 and lewat_kasir2 = 0 and poli='gigi'")->orderBy('tanggal')->orderBy('antrian')->get(); */
	/* 	return view('antrianperiksas.index') */
	/* 		->withPostperiksa($postperiksa) */
	/* 		->withAntrianperiksa($antrianperiksa) */
	/* 		->with('staf_list', $this->staf_list) */
	/* 		->with('poli_list', $this->poli_list) */
	/* 		->withPoli($poli); */
	/* } */
	/* public function darurat(){ */
	/* 	$poli = 'darurat'; */
	/* 	$antrianperiksa = AntrianPeriksa::with('pasien', 'staf', 'asuransi')->where('poli', '=', $poli)->orderBy('tanggal')->orderBy('antrian')->get(); */
	/* 	$postperiksa = Periksa::with( */
	/* 		'staf', */ 
	/* 		'asuransi', */ 
	/* 		'pasien', */ 
	/* 		'suratSakit', */ 
	/* 		'rujukan.tujuanRujuk', */ 
	/* 		'kontrol' */
	/* 	)->whereRaw("lewat_poli = 1 and lewat_kasir2 = 0 and poli='darurat'")->get(); */
	/* 	return view('antrianperiksas.index') */
	/* 		->withPostperiksa($postperiksa) */
	/* 		->withAntrianperiksa($antrianperiksa) */
	/* 		->with('staf_list', $this->staf_list) */
	/* 		->with('poli_list', $this->poli_list) */
	/* 		->withPoli($poli); */
	/* } */
	/* public function estetika(){ */
	/* 	$poli = 'estetika'; */
	/* 	$antrianperiksa = AntrianPeriksa::with('pasien', 'staf', 'asuransi')->where('poli', '=', $poli)->orderBy('tanggal')->orderBy('antrian')->get(); */
	/* 	$postperiksa = Periksa::with( */
	/* 		'staf', */ 
	/* 		'asuransi', */ 
	/* 		'pasien', */ 
	/* 		'suratSakit', */ 
	/* 		'rujukan.tujuanRujuk', */ 
	/* 		'kontrol' */
	/* 	)->whereRaw("lewat_poli = 1 and lewat_kasir2 = 0 and poli='estetika'")->get(); */

	/* 	return view('antrianperiksas.index') */
	/* 		->withPostperiksa($postperiksa) */
	/* 		->withAntrianperiksa($antrianperiksa) */
	/* 		->with('staf_list', $this->staf_list) */
	/* 		->with('poli_list', $this->poli_list) */
	/* 		->withPoli($poli); */
	/* } */
	

}

