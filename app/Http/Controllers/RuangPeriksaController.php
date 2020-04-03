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
        $this->middleware('backIfNotFound', ['only' => ['index']]);
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
}

