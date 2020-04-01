<?php


namespace App\Http\Controllers;

use Input;

use App\Http\Requests;
use App\Events\FormSubmitted;
use App\Antrian;
use App\JenisAntrian;
use Bitly;
use App\Sms;
use App\Asuransi;
use App\AntrianPeriksa;
use App\Promo;
use App\Periksa;
use App\Perujuk;
use App\Pasien;
use App\AntrianPoli;
use App\Complain;
use App\Kabur;
use App\Classes\Yoga;
use DB;

class AntrianPolisController extends Controller
{

	public $input_pasien_id;
	public $input_asuransi_id;
	public $input_poli;
	public $input_staf_id;
	public $input_tanggal;
	public $input_jam;
	public $input_kecelakaan_kerja;
	public $input_self_register;
	public $input_bukan_peserta;
	public $input_antrian_id;

	public function __construct() {
		$this->input_pasien_id     = Input::get('pasien_id');
		$this->input_asuransi_id   = Input::get('asuransi_id');
		$this->input_poli          = Input::get('poli');
		$this->input_staf_id       = Input::get('staf_id');
		$this->input_tanggal       = Yoga::datePrep( Input::get('tanggal') );
		$this->input_bukan_peserta = Input::get('bukan_peserta');
        /* $this->middleware('nomorAntrianUnik', ['only' => ['store']]); */
    }
	/**
	 * Display a listing of antrianpolis
	 *
	 * @return Response
	 */
	public function index()
	{
		$asu = array(null => '- Pilih Asuransi -') + Asuransi::pluck('nama', 'id')->all();
		$jenis_peserta = array(
			null => ' - pilih asuransi -',  
			"P"  => 'Peserta',
			"S"  => 'Suami',
			"I"  => 'Istri',
			"A"  => 'Anak'
		);
		$usg = array(
			null => ' - pilih -',  
			0    => 'Bukan USG',
			1    => 'USG'
		);

		$peserta = [ null => '- Pilih -', '0' => 'Peserta Klinik', '1' => 'Bukan Peserta Klinik'];
		$perujuks_list = [null => ' - pilih perujuk -'] + Perujuk::pluck('nama', 'id')->all();

		$antrianpolis  = AntrianPoli::with('pasien', 'asuransi', 'antars', 'antrian')
								->where('submitted', 0)
								->get();

		$perjanjian = [];
		$staf = Yoga::stafList();
		foreach ($antrianpolis as $p) {
			$perjanjian[$p->tanggal->format('d-m-Y')][] = $p;
		}

		
		return view('antrianpolis.index')
			->withAntrianpolis($antrianpolis)
			->with('perujuks_list', $perujuks_list)
			->withUsg($usg)
			->withAsu($asu)
			->withStaf($staf)
			->withPeserta($peserta)
			->withPerjanjian($perjanjian);
	}

	/**
	 * Show the form for creating a new antrianpoli
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('antrianpolis.create');
	}

	/**
	 * Store a newly created antrianpoli in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		DB::beginTransaction();
		try {
			if (empty(Pasien::find(Input::get('pasien_id'))->image) && Input::get('asuransi_id') == '32') {
				return redirect('pasiens/' . Input::get('pasien_id') . '/edit')->withCek('Gambar <strong>Foto pasien (bila anak2) atau gambar KTP pasien (bila DEWASA) </strong> harus dimasukkan terlebih dahulu');
			}

			$rules = [
				'tanggal'   => 'required',
				'pasien_id' => 'required',
				'poli'      => 'required'
			];
			
			$validator = \Validator::make(Input::all(), $rules);
			
			if ($validator->fails())
			{
				return \Redirect::back()->withErrors($validator)->withInput();
			}

			$ap = $this->inputDataAntrianPoli();
			DB::commit();
			return $this->arahkanAP($ap);

		} catch (\Exception $e) {
			DB::rollback();
			throw $e;
		}
	}

	/**
	 * Display the specified antrianpoli.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$antrianpoli = AntrianPoli::findOrFail($id);

		return view('antrianpolis.show', compact('antrianpoli'));
	}

	/**
	 * Show the form for editing the specified antrianpoli.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$antrianpoli = AntrianPoli::find($id);

		return view('antrianpolis.edit', compact('antrianpoli'));
	}

	/**
	 * Update the specified antrianpoli in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$antrianpoli = AntrianPoli::findOrFail($id);

		$antrianpoli->update($data);

		return \Redirect::route('antrianpolis.index');
	}

	/**
	 * Remove the specified antrianpoli from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{

		$antrianpoli = AntrianPoli::find($id);
		$pasien_id = $antrianpoli->pasien_id;
		$nama = $antrianpoli->pasien->nama;

		$kabur            = new Kabur;
		$kabur->pasien_id = $pasien_id;
		$kabur->alasan    = Input::get('alasan_kabur');
		$conf             = $kabur->save();

		if ($conf) {
			$antrianpoli->delete();
		}



		return \Redirect::route('antrianpolis.index')->withPesan(Yoga::suksesFlash('pasien <strong>' .$pasien_id . ' -  ' . $nama .'</strong> berhasil dihapus dari Antrian'));
	}

    protected $morphClass = 'App\AntrianPoli';
    public function promos(){
        return $this->morphMany('App\Promo', 'jurnalable');
    }

	public function sendWaAntrian($totalAntrian, $ap){

		$tanggal            = $ap->tanggal;
		$antrian            = $ap->antrian;
		$no_telp            = $ap->pasien->no_telp;
		$no_telp_string     = $no_telp;
		$antrian_pasien_ini = array_search($antrian, $totalAntrian['antrians']) +1;
		/* if ( gethostname() == 'Yogas-Mac' ) { */
		$no_telp = '081381912803';
		/* } */
		$sisa_antrian =   $antrian_pasien_ini - $totalAntrian['antrian_saat_ini'] ;

		$text = 'Pasien Yth. Nomor Antrian Anda adalah \n\n *' . $antrian_pasien_ini ;
	    $text .=	'* \n\nAntrian yang diperiksa saat ini adalah\n\n *' . $totalAntrian['antrian_saat_ini'];
		$text .= '* \n\nSaat ini (' . date('d M y H:i:s'). ') Masih ada\n\n *';
		$text .= $sisa_antrian . ' antrian lagi*\n\n';
		$text .= 'Sebelum giliran anda dipanggil. ';
		$text .= 'Mohon agar dapat membuka link berikut untuk mengetahui antrian terakhir secara berkala: \n\n';
		$text .= Bitly::getUrl('http://45.76.186.44/antrianperiksa/' . $ap->id);
		/* $text .= '\n\n.'; */
		/* $text .= $no_telp_string; */
		/* $text .= '\n\n.'; */
		/* $text .= '\nBapak/Ibu dapat menunggu antrian periksa di rumah, dan datang kembali ke klinik saat antrian sudah dekat, untuk mencegah menunggu terlalu lama, dan mencegah penularan penyakit. Terima kasih'; */
		/* $text .= 'Sistem akan mengirimkan whatsapp untuk mengingatkan anda jika tersisa 5 antrian lagi dan 1 antrian lagi sebelum anda dipanggil. Terima kasih' ; */

		Sms::send($no_telp, $text);

	}
	public function totalAntrian($ap){
		/* $tanggal = $ap->tanggal; */
		/* $antrian = $ap->antrian; */
		/* $no_telp = $ap->pasien->no_telp; */
		/* $antrians = []; */
		/* $apx_per_tanggal = AntrianPeriksa::where('tanggal',  $tanggal) */
		/* 								->whereIn('poli', ['umum', 'sks', 'luka']) */
		/* 								->get(); */
		/* $apl_per_tanggal = AntrianPoli::where('tanggal',  $tanggal) */
		/* 								->whereIn('poli', ['umum', 'sks', 'luka']) */
		/* 								->get(); */
		/* $px_per_tanggal = Periksa::where('tanggal',  $tanggal) */
		/* 								->whereIn('poli', ['umum', 'sks', 'luka']) */
		/* 								->orderBy('antrian', 'desc') */
		/* 								->get(); */

		/* foreach ($apx_per_tanggal as $apx) { */
		/* 	$antrians[$apx->pasien_id] = $apx->antrian; */
		/* } */

		/* foreach ($apl_per_tanggal as $apx) { */
		/* 	$antrians[$apx->pasien_id] = $apx->antrian; */
		/* } */
		/* foreach ($px_per_tanggal as $apx) { */
		/* 	$antrians[$apx->pasien_id] = $apx->antrian; */
		/* } */

		/* sort($antrians); */
		/* if ( $px_per_tanggal->count() >2 ) { */
		/* 	$antrian_saat_ini   = array_search($px_per_tanggal->first()->antrian, $antrians); */
		/* } else { */
		/* 	$antrian_saat_ini   = 0; */
		/* } */

		/* $result = compact( */
		/* 	'antrians', */
		/* 	'antrian_saat_ini' */
		/* ); */
		/* return $result; */
	}
	public function inputDataAntrianPoli(){
		$ap                            = new AntrianPoli;
		$ap->asuransi_id               = $this->input_asuransi_id;
		$ap->pasien_id                 = $this->input_pasien_id;
		$ap->poli                      = $this->input_poli;
		$ap->staf_id                   = $this->input_staf_id;
		if ( $this->input_asuransi_id == '32' ) {
			$ap->bukan_peserta         = $this->input_bukan_peserta;
		}
		$ap->jam                       = date("H:i:s");
		$ap->tanggal                   = $this->input_tanggal;
		$ap->save();

		if ( isset($this->input_antrian_id) ) {
			$antrian_id         = $this->input_antrian_id;
			$an                 = Antrian::find($antrian_id);
			$an->antriable_id   = $ap->id;
			$an->antriable_type = 'App\\AntrianPoli';
			$an->save();
		}
		/* if ( */
		/* 	$ap->poli == 'umum' || */
		/* 	$ap->poli == 'luka' || */
		/* 	$ap->poli == 'sks' */
		/* ) { */
		/* 	$totalAntrian = $this->totalAntrian($ap); */
		/* 	$this->sendWaAntrian($totalAntrian, $ap); */
		/* } */
		$this->updateJumlahAntrian();
		return $ap;

	}

	public function arahkanAP($ap){
		$pasien = Pasien::find($this->input_pasien_id);

		$pesan = Yoga::suksesFlash('<strong>' . $pasien->id . ' - ' . $pasien->nama . '</strong> Berhasil masuk antrian Nurse Station Dan <strong>Komplain berhasil didokumentasikan</strong>');
		
		if ($this->input_asuransi_id == '32') {
			return redirect('antrianpolis/pengantar/create/' . $ap->id)->withPesan(Yoga::suksesFlash('Harap Isi dulu pengantar pasien sebagai data kunjungan sehat'));
		}

		if ( $ap->poli == 'usg' ) {
			return redirect('antrianpolis')
				->withPrint($ap)
				->withPesan($pesan);
		}
		return redirect('antrianpolis')
			->withPesan($pesan);
	}
	public function updateJumlahAntrian(){
		$count         = Antrian::with('jenis_antrian.poli_antrian', 'jenis_antrian.antrian_terakhir')->where('antriable_type', 'App\\Antrian')->count();
		$data['count'] = $count;
		$antrians      = Antrian::with('jenis_antrian.poli_antrian', 'jenis_antrian.antrian_terakhir')->where('created_at', 'like', date('Y-m-d') . '%')->get();
		$jenis_antrian = JenisAntrian::with('antrian_terakhir.jenis_antrian')->orderBy('updated_at', 'desc')->get();

		if ( isset($jenis_antrian->first()->antrian_terakhir)) {
			$data['panggilan']['nomor_antrian'] = $jenis_antrian->first()->antrian_terakhir->nomor_antrian; 
		} else {
			$data['panggilan']['nomor_antrian'] = null;
		}

		$data['panggilan']['poli'] = ucwords($jenis_antrian->first()->jenis_antrian);

		foreach ($jenis_antrian as $ja) {
			if (isset($ja->antrian_terakhir ) && strpos($ja->updated_at, date('Y-m-d')) !== false ) {
				$data['antrian_terakhir_per_poli'][$ja->id] = $ja->antrian_terakhir->nomor_antrian;
			} else {
				$data['antrian_terakhir_per_poli'][$ja->id] = '-';
			}
		}
		foreach ($antrians as $antrian) {
			if( isset($data['data'][ $antrian->jenis_antrian_id ]['jumlah']) ){
				$data['data'][ $antrian->jenis_antrian_id ]['jumlah']++;
			} else {
				$data['data'][ $antrian->jenis_antrian_id ]['jumlah'] = 1;
			}
			if (isset($antrian->jenis_antrian->antrian_terakhir)) {
				$data['data'][ $antrian->jenis_antrian_id ]['nomor_antrian_terakhir'] = $antrian->jenis_antrian->antrian_terakhir->nomor_antrian;
			} else {
				$data['data'][ $antrian->jenis_antrian_id ]['nomor_antrian_terakhir'] = '-';
			}
		}

		foreach ($jenis_antrian as $ja) {
			if (!isset($data['data'][$ja->id]) && $ja->id <5) {
				$data['data'][$ja->id]['nomor_antrian_terakhir'] = '-';
				$data['data'][$ja->id]['jumlah']                 = 0;
			}
		}
		event(new FormSubmitted($data));
	}
}
