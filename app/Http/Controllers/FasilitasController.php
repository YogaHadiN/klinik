<?php

namespace App\Http\Controllers;

use Input;

use App\Http\Requests;
use App\Fasilitas;
use DB;
use App\Pasien;
use App\Antrian;
use App\Panggilan;
use App\JurnalUmum;
use App\Periksa;
use App\AntrianPeriksa;
use App\TransaksiPeriksa;
use App\Kabur;
use App\Staf;
use App\Http\Controllers\PasiensController;
use App\Http\Controllers\AntrianPolisController;
use App\Dispensing;
use App\BukanPeserta;
use App\AntrianPoli;
use App\Classes\Yoga;
use App\RumahSakit;
use App\Terapi;
use App\Rujukan;
use App\SuratSakit;
use App\RegisterAnc;
use App\GambarPeriksa;
use App\Usg;
use App\Sms;
use App\DeletedPeriksa;


class FasilitasController extends Controller
{
	public $input_nomor_bpjs;

	public function __construct(){
        $this->middleware('redirectBackIfIdAntrianNotFound', ['only' => ['prosesAntrian']]);
		$this->input_nomor_bpjs = Input::get('nomor_bpjs');
	}
	
    public function antrian_pasien(){
		$antrianperiksa = AntrianPeriksa::with('pasien')->take(10)->get(['pasien_id']);
		return view('fasilitas.antrian', compact('antrianperiksa'));
    }
    public function survey(){
		return view('surveys.survey');
    }
	public function input_telp(){
		return view('fasilitas.input_telp');
	}
	public function input_tgl_lahir($poli){
		return view('fasilitas.input_tgl_lahir', compact(
			'poli'
		));
	}
	public function post_tgl_lahir($poli){
		$tanggal = Yoga::datePrep( Input::get('tanggal_lahir') );
		$pasiens = Pasien::where('tanggal_lahir', $tanggal)->get();
		if ($pasiens->count() < 1) {
			$pesan = Yoga::gagalFlash('Tidak ada Pasien yang terdaftar dengan Tanggl Lahir ' . Input::get('tanggal_lahir') . '<br /><strong> Silahkan Ulangi Kembali </strong>');
			return redirect('fasilitas/antrian_pasien')->withPesan($pesan);
		}
		return view('fasilitas.cari_pasien', compact(
			'pasiens',
			'poli',
			'tanggal'
		));
	}
	public function cari_asuransi($poli, $pasien_id){
		$tanggal = Input::get('tanggal');
		$pasien = Pasien::find($pasien_id);
		if ($poli == 'estetika') {
			$pesan = $this->postAntrianPoli($poli, $pasien_id, 0);
			return redirect('fasilitas/antrian_pasien')->withPesan($pesan);
		}
		return view('fasilitas.cari_asuransi', compact(
			'tanggal',
			'poli',
			'pasien'
		));
	}
	public function submit_antrian($poli, $pasien_id, $asuransi_id){
		$pesan = $this->postAntrianPoli($poli, $pasien_id, $asuransi_id);
		return redirect('fasilitas/antrian_pasien')->withPesan($pesan);
	}
	public function postAntrianPoli($poli, $pasien_id, $asuransi_id){
		$antrianPoli = ( isset( AntrianPoli::latest()->first()->antrian ) )?  AntrianPoli::latest()->first()->antrian : null;
		$antrianPeriksa = ( isset( AntrianPeriksa::latest()->first()->antrian ) )? AntrianPeriksa::latest()->first()->antrian : null; 
		$antrian = [
			$antrianPeriksa,
			$antrianPoli
		];
		$antrian = (int)max($antrian) + 1; 
		$ap       = new AntrianPoli;
		$ap->poli   = $poli;
		$ap->pasien_id   = $pasien_id;
		if ($asuransi_id !='x') {
			$ap->asuransi_id   = $asuransi_id;
		}
		$ap->tanggal   = date('Y-m-d');
		$ap->jam   = date('H:i:s');
		$ap->self_register   = 1;
		$ap->antrian   = $antrian;
		$ap->asuransi_id   = $asuransi_id;
		$confirm = $ap->save();
		if ($confirm) {
			$pesan = Yoga::suksesFlash( '<strong>' . $ap->pasien->id . ' - ' . $ap->pasien->nama . '</strong> Berhasil masuk antrian' );
			if ($asuransi_id != '0' && $asuransi_id != '32') {
				$pesan .= " Mohon berikan kartu asuransi / pengantar berobat ke kasir";
			}
		} else {
			$pesan = Yoga::gagalFlash('<strong>' . $ap->pasien->id . ' - ' . $ap->pasien->nama . '</strong> Gagal masuk antrian');
		}			
		return $pesan;
	}
	
	
	public function konfirmasi(){
		$id = Input::get('konfirmasi_antrian_poli_id');
		$ap       = AntrianPoli::find($id);
		$ap->self_register   = null;
		$confirm = $ap->save();
		if ($confirm) {
			$pesan = Yoga::suksesFlash(''  . $ap->pasien_id . ' - ' . $ap->pasien->nama . ' <strong>BERHASIL</strong> ');
		} else {
			$pesan = Yoga::gagalFlash(''  . $ap->pasien_id . ' - ' . $ap->pasien->nama . ' <strong>GAGAL</strong> ');
		}
		return redirect()->back()->withPesan($pesan);
	}
	
	
	public function antrianPoliDestroy(){
		$rules = [
			'id' => 'required',
			'pasien_id' => 'required',
		];
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}

		$kb            = new Kabur;
		$kb->pasien_id = Input::get('pasien_id');
		$kb->alasan    = Input::get('alasan_kabur');
		$kb->save();

		try {
			$ap = AntrianPoli::findOrFail( Input::get('id') );
		} catch (\Exception $e) {
			$pesan = Yoga::gagalFlash('Antrian Tidak ditemukan, mungkin sudah dihapus sebelumnya');
			return redirect()->back()->withPesan($pesan);
		}
		$nama_pasien = $ap->pasien->nama;
		$confirm     = $ap->delete();


		if ($confirm) {
			$pesan = Yoga::suksesFlash('<strong>' . $nama_pasien . '</strong> Berhasil dihapus dari Antrian');
		} else {
			$pesan = Yoga::gagalFlash('<strong>' . $nama_pasien . '</strong> Gagal dihapus dari Antrian');
		}

		return redirect()->back()->withPesan($pesan);


	}
	public function antrianPeriksaDestroy(){
		$rules = [
			'id'           => 'required',
			'pasien_id'    => 'required',
			'alasan_kabur' => 'required',
			'staf_id'      => 'required'
		];
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}

		try {
			$last_kabur_id = (int)Kabur::orderBy('id', 'desc')->firstOrFail()->id + 1;
		} catch (\Exception $e) {
			$last_kabur_id = 1;
		}

		$send_sms            = false;
		$periksa_deleted_ids = [];
		$timestamp           = date('Y-m-d H:i:s');
		$deleted_periksas    = [];
		$id                  = Input::get('id');
		$ap                  = AntrianPeriksa::find($id);
		$pasien_id = $ap->pasien_id;
		$nama_pasien = $ap->pasien->nama_pasien;

		$kabur            = new Kabur;
		$kabur->pasien_id = Input::get('pasien_id');
		$kabur->staf_id   = Input::get('staf_id');
		$kabur->alasan    = Input::get('alasan_kabur');
		$terapi_deletes = [];
		DB::beginTransaction();
		try {
			$periksa = Periksa::with('pasien')->where('antrian_periksa_id', $id)->first();
			if($periksa != null && $periksa->lewat_kasir2 != 1){
				$terapis = Terapi::with('merek.rak')->where('periksa_id', $periksa->id)->get(); // Haput Terapi bila ada periksa id
				foreach ($terapis as $t) {
					$terapi_deletes[] = $t->id;
					$rak       = $t->merek->rak;
					$rak->stok = $rak->stok + $t->jumlah;
					$rak->save();
				}
				$periksa_deleted_ids[] = $periksa->id;
				$deleted_periksas[] = [
					'staf_id'    => Input::get('staf_id'),
					'pasien_id'  => $pasien_id,
					'kabur_id'   => $last_kabur_id,
					'periksa_id' => $periksa->id,
					'created_at' => $timestamp,
					'updated_at' => $timestamp
				];
			}
			$kabur->save();
			AntrianPeriksa::destroy($id);
			DeletedPeriksa::insert($deleted_periksas);
			if ($periksa != null) {
				Sms::send(env('NO_HP_OWNER'), 'Telah dihapus pemeriksaan atas nama ' . $pasien_id . ' - ' . $nama_pasien . ' oleh ' . Staf::find( Input::get('staf_id') )->nama );
				TransaksiPeriksa::where('periksa_id', $periksa->id)->delete(); // Haput Transaksi bila ada periksa id
				Terapi::where('periksa_id', $periksa->id)->delete(); // Haput Terapi bila ada periksa id
				Dispensing::whereIn('dispensable_id', $terapi_deletes)
							->where('dispensable_type', 'App\Terapi')
							->delete();
				BukanPeserta::where('periksa_id', $periksa->id)->delete(); // Haput Terapi bila ada periksa id
				Rujukan::where('periksa_id', $periksa->id)->delete(); //hapus rujukan yang memiliki id periksa ini
				SuratSakit::where('periksa_id', $periksa->id)->delete(); // hapus surat sakit yang memiliki id periksa ini
				RegisterAnc::where('periksa_id', $periksa->id)->delete(); // hapus surat sakit yang memiliki id periksa ini
				Usg::where('periksa_id', $periksa->id)->delete(); // hapus surat sakit yang memiliki id periksa ini
				GambarPeriksa::where('gambarable_id', $periksa->id)
								->where('gambarable_type', 'App\Periksa')
								->delete(); // hapus gambar_periksas yang memiliki id periksa ini
				Periksa::destroy($periksa_deleted_ids);
			}
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			throw $e;
		}
		return redirect()->back()->withPesan(Yoga::suksesFlash('Pasien <strong>' . $ap->pasien_id . ' - ' . $ap->pasien->nama . '</strong> Berhasil dihapus dari antrian'  ));
	}
	public function antrianPost($id){
		$antrians = Antrian::with('jenis_antrian')->where('created_at', 'like', date('Y-m-d') . '%')
							->where('jenis_antrian_id',$id)
							->orderBy('nomor', 'desc')
							->first();

		if ( is_null( $antrians ) ) {
			$antrian                   = new Antrian;
			$antrian->nomor            = 1 ;
			$antrian->nomor_bpjs       = $this->input_nomor_bpjs;
			$antrian->jenis_antrian_id = $id ;
			$antrian->save();

		} else {
			$antrian_terakhir          = $antrians->nomor + 1;
			$antrian                   = new Antrian;
			$antrian->nomor            = $antrian_terakhir ;
			$antrian->nomor_bpjs       = $this->input_nomor_bpjs;
			$antrian->jenis_antrian_id = $id ;
			$antrian->save();
		}
		$antrian->antriable_id   = $antrian->id;
		$antrian->antriable_type = 'App\\Antrian';
		$antrian->save();
		$apc                     = new AntrianPolisController;
		$apc->updateJumlahAntrian();
		return $antrian;
	}
	public function antrian($id){
		$antrian = $this->antrianPost( $id );
		/* return $antrian; */
		return redirect('fasilitas/antrian_pasien')
			->withPrint($antrian->id);
	}
	public function antrianAjax($id){
		$antrian       = $this->antrianPost( $id );
		$nomor_antrian = $antrian->jenis_antrian->prefix . $antrian->nomor;
		$jenis_antrian = ucwords( $antrian->jenis_antrian->jenis_antrian );
		$timestamp = date('Y-m-d H:i:s');
		return compact('nomor_antrian', 'jenis_antrian', 'timestamp' );
	}
	
	public function listAntrian(){
		$antrians = Antrian::where('antriable_type', 'App\\Antrian')->get();
		return view('fasilitas.list_antrian', compact(
			'antrians'
		));
	}
	public function prosesAntrian($id){
		$antrian                                              = Antrian::with('jenis_antrian.poli_antrian.poli')->where('id', $id )->first();
		$p                                                    = new PasiensController;
		$polis[null]                                          = '-Pilih Poli-';
		foreach ($antrian->jenis_antrian->poli_antrian as $k => $poli) {
			$polis[ $poli->poli_id ]                               = $poli->poli->poli;
		}
		$p->dataIndexPasien['poli']                           = $polis;
		$p->dataIndexPasien['antrian']                        = $antrian;
		return $p->index();
	}
	public function antrianPoliPost($id){
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

			$apc = new AntrianPolisController;

			$apc->input_antrian_id   = $id;
			$ap = $apc->inputDataAntrianPoli();
			DB::commit();
			return $apc->arahkanAP($ap);

		} catch (\Exception $e) {
			DB::rollback();
			throw $e;
		}
	}
	public function deleteAntrian($id){
		$antrian      = Antrian::find( $id );
		$nomor_antrian = $antrian->nomor_antrian; 
		$pesan        = Yoga::suksesFlash('Antrian ' . $nomor_antrian . ' BERHASIL dihapus');
		$apc          = new AntrianPolisController;
		$apc->updateJumlahAntrian();

		$whatsapp_registration = $antrian->whatsapp_registration;

		if ( isset( $$whatsapp_registration ) ) {
			$message = 'Mohon maaf antrian pendaftaran anda melalui whatsapp telah dihapus, Anda dapat mengulangi lagi pada saat anda sudah tiba di klinik' ;
			Sms::send( $$whatsapp_registration->no_telp, $message);
			$whatsapp_registration->delete();
		}
		$antrian->delete();
		return redirect()->back()->withPesan($pesan);
	}
	public function createPasien($id){
		$ps          = new PasiensController;
		$polis[null] = ' - Pilih Poli - ';
		$antrian     = Antrian::find( $id );
		foreach ($antrian->jenis_antrian->poli_antrian as $poli) {
			$polis[ $poli->poli_id ] = $poli->poli->poli;
		}
		$ps->dataCreatePasien['poli']    = $polis;
		$ps->dataCreatePasien['antrian'] = $antrian;
		return $ps->create();
	}
	public function storePasien($id){
		$rules = [
			"nama"      => "required",
			"sex"       => "required",
			"panggilan" => "required"
		];

		$pc = new PasiensController;

		if ( $pc->input_punya_asuransi == '1' ) {
			  $rules["asuransi_id"]    = "required";
			  $rules["jenis_peserta"]  = "required";
			  $rules["nomor_asuransi"] = "required";
		}
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
		$pc->input_antrian_id = $id;
		$pasien = new Pasien;
		$pasien = $pc->inputDataPasien($pasien);
		$ap     = $this->inputDataAntrianPoli($pasien);

		$pesan = Yoga::suksesFlash( '<strong>' . $pasien->id . ' - ' . $pasien->nama . '</strong> Berhasil dibuat dan berhasil masuk antrian Nurse Station' );
		return redirect('antrianpolis')
			->withPesan($pesan);

	}
	public function getTambahAntrian($id){
		$antrian       = $this->antrianPost( $id );
		$nomor_antrian = $antrian->jenis_antrian->prefix . $antrian->nomor;
		$jenis_antrian = ucwords( $antrian->jenis_antrian->jenis_antrian );

		$pesan = Yoga::suksesFlash(
			'Antrian baru <strong> ' . $nomor_antrian . '</strong> ke ' . $jenis_antrian . '	Berhasil ditambahkan'
		);
		return redirect()->back()->withPesan($pesan);
	}
}
