<?php

namespace App\Http\Controllers;

use Input;

use Illuminate\Http\Request;
use App\Asuransi;
use App\Staf;
use Log;
use App\Promo;
use App\Sms;
use App\BukanPeserta;
use App\Classes\Yoga;
use App\AntrianPeriksa;
use Bitly;
use App\Pasien;
use App\AntrianPoli;
use App\Kabur;
use App\Periksa;
use App\Terapi;
use App\JurnalUmum;
use App\TransaksiPeriksa;
use App\Rujukan;
use App\PengantarPasien;
use App\SuratSakit;
use App\RegisterAnc;
use App\Usg;



class AntrianPeriksasController extends Controller
{
	/**
	 * Display a listing of antrianperiksas
	 *
	 * @return Response
	 */

	public function __construct() {
        /* $this->middleware('nomorAntrianUnik', ['only' => ['store']]); */
        /* $this->middleware('super', ['only' => ['delete','update']]); */
    }
	public function index()
	{
		$asu = array('0' => '- Pilih Asuransi -') + Asuransi::pluck('nama', 'id')->all();

		$jenis_peserta = array(

			null => ' - pilih asuransi -',
            "P" => 'Peserta',
            "S" => 'Suami',
            "I" => 'Istri',
            "A" => 'Anak'

					);

		$staf            = array('0' => '- Pilih Staf -') + Staf::pluck('nama', 'id')->all();
		$poli            = Yoga::poliList();
		$staf_list       = Staf::list();
		$antrianperiksas = AntrianPeriksa::all();
		return view('antrianperiksas.index', compact(
			'antrianperiksas',
			'staf_list',
		   	'postperiksa'
		));
	}


	/**
	 * Store a newly created antrianperiksa in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$rules = [

			'staf_id'          => 'required',
			'hamil'            => 'required',
			'kecelakaan_kerja' => 'required',
			'pasien_id'        => 'required',
			'asuransi_id'      => 'required',
			'asisten_id'       => 'required',
			'poli'             => 'required'

		];

		$validator = \Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}

		if (AntrianPoli::where( 'id',  Input::get('antrian_id') )->where('submitted', '0')->first() == null) {
			$pesan = Yoga::gagalFlash('Pasien sudah hilang dari antrian poli, mungkin sudah dimasukkan sebelumnya');
			return redirect()->back()->withPesan($pesan);
		}

		$tekanan_darah 			= Input::get('tekanan_darah');
		$berat_badan 			= Input::get('berat_badan');
		$suhu 					= Input::get('suhu');
		$tinggi_badan 			= Input::get('tinggi_badan');
		$periksa_awal 			= Yoga::periksaAwal( 
														Input::get('sistolik') . '/' . Input::get('diastolik') . ' mmHg', 
														$berat_badan, 
														$suhu, 
														$tinggi_badan
													);

		$ap = new AntrianPeriksa;
		
		$kecelakaan_kerja = Input::get('kecelakaan_kerja');
		$asuransi_id      = Input::get('asuransi_id');

		$ap->berat_badan         = $berat_badan;
		$ap->hamil               = Input::get('hamil');
		$ap->asisten_id          = Input::get('asisten_id');
		$ap->periksa_awal        = $periksa_awal;
		if ($kecelakaan_kerja == '1' && $asuransi_id == '32') {
			$asuransi_id = '0';
			$ap->keterangan = 'Pasien ini tadinya pakai asuransi BPJS tapi diganti menjadi Biaya Pribadi karena Kecelakaan Kerja / Kecelakaan Lalu Lintas tidak ditanggung BPJS, tpi PT. Jasa Raharja';
		}
		$ap->asuransi_id         = $asuransi_id;
		$ap->pasien_id           = Input::get('pasien_id');
		$ap->poli                = Input::get('poli');
		$ap->staf_id             = Input::get('staf_id');
		$ap->jam                 = Input::get('jam');
		$ap->menyusui            = Input::get('menyusui');
		if ( $asuransi_id == '32' ) {
			$ap->bukan_peserta            = Input::get('bukan_peserta');
		}
		$ap->riwayat_alergi_obat = Input::get('riwayat_alergi_obat');
		$ap->suhu                = $suhu;
		$ap->g                   = Yoga::returnNull(Input::get('G'));
		$ap->p                   = Yoga::returnNull(Input::get('P'));
		$ap->a                   = Yoga::returnNull(Input::get('A'));
		$ap->hpht                = Yoga::datePrep(Input::get('hpht'));
		$ap->tanggal             = Yoga::datePrep( Input::get('tanggal') );
		$ap->kecelakaan_kerja    = $kecelakaan_kerja;
		$ap->sistolik            = Input::get('sistolik');
		$ap->diastolik           = Input::get('diastolik');
		$ap->tinggi_badan        = $tinggi_badan;


		$ap->save();

		$antrian_poli_id         = Input::get('antrian_id');
		$pasien                  = Pasien::find(Input::get('pasien_id'));
		$antrian_poli            = AntrianPoli::find($antrian_poli_id);
		$antrian                 = $antrian_poli->antrian;
		if(isset($antrian)){
			$antrian->antriable_id   = $ap->id;
			$antrian->antriable_type = 'App\\AntrianPeriksa';
			$antrian->save();
		}
		$antrian_poli->delete();

		$promo = Promo::where('promoable_type' , 'App\AntrianPoli')->where('promoable_id', $antrian_poli_id)->first() ;
		if ( $promo ) {
			$promo->promoable_type = 'App\AntrianPeriksa';
			$promo->promoable_id = $ap->id;
			$promo->save();
		}

		PengantarPasien::where('antarable_id', $antrian_poli_id)
			->where('antarable_type', 'App\AntrianPoli')
			->update([
				'antarable_id' => $ap->id,
				'antarable_type' => 'App\AntrianPeriksa'
			]);


		return \Redirect::route('antrianpolis.index')->withPesan(Yoga::suksesFlash('<strong>' .$pasien->id . ' - ' . $pasien->nama . '</strong> berhasil masuk antrian periksa'));
	}


	/**
	 * Remove the specified antrianperiksa from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$ap            = AntrianPeriksa::with('antrian', 'pasien')->where('id',$id)->first();
		$jenis_antrian_id = $ap->antrian->jenis_antrian_id;
		$pasien_id = $ap->pasien_id;
		$nama_pasien = $ap->pasien->nama;

		$kabur            = new Kabur;
		$kabur->pasien_id = $ap->pasien_id;
		$kabur->alasan    = Input::get('alasan');
		$conf             = $kabur->save();

		$periksa = Periksa::where('antrian_periksa_id', $id)->first();
		if(isset($periksa)){
			TransaksiPeriksa::where('periksa_id', $periksa->id)->delete(); // Haput Transaksi bila ada periksa id
			Terapi::where('periksa_id', $periksa->id)->delete(); // Haput Terapi bila ada periksa id
			BukanPeserta::where('periksa_id', $periksa->id)->delete(); // Haput Terapi bila ada periksa id
			Rujukan::where('periksa_id', $periksa->id)->delete(); //hapus rujukan yang memiliki id periksa ini
			SuratSakit::where('periksa_id', $periksa->id)->delete(); // hapus surat sakit yang memiliki id periksa ini
			RegisterAnc::where('periksa_id', $periksa->id)->delete(); // hapus surat sakit yang memiliki id periksa ini
			Usg::where('periksa_id', $periksa->id)->delete(); // hapus surat sakit yang memiliki id periksa ini
			JurnalUmum::where('jurnalable_id', $periksa->id)
				->where('jurnalable_type', 'App\Periksa')
				->delete(); // hapus jurnalumum yang dimiliki pasien ini
			Periksa::destroy($periksa->id);
		}
		$ap->delete();

		return redirect('ruangperiksa/' . $jenis_antrian_id)->withPesan(Yoga::suksesFlash('Pasien <strong>' . $pasien_id . ' - ' . $nama_pasien . '</strong> Berhasil dihapus dari antrian'  ));
	}

	private function periksaKosong($pasien_id, $staf_id, $asisten_id, $ap_id, $antrian, $jamdatang){
		$periksa_id = Yoga::customId('App\Periksa');
  		 $p       = new Periksa;
		  $p->id = $periksa_id;
		  $p->asuransi_id = "0";
		  $p->pasien_id =$pasien_id;
		  $p->berat_badan = "";
		  $p->poli = "estetika";
		  $p->staf_id =$staf_id;
		  $p->asisten_id =$asisten_id;
		  $p->periksa_awal = "[]";
		  $p->jam =$jamdatang;
		  $p->jam_resep = date('H:i:s');
		  $p->keterangan_diagnosa = "";
		  $p->lewat_poli = "0";
		  $p->lewat_kasir = "0";
		  $p->lewat_kasir2 = "0";
		  $p->antrian_periksa_id =$ap_id;
		  $p->resepluar = "";
		  $p->pemeriksaan_fisik = "";
		  $p->pemeriksaan_penunjang = "";
		  $p->tanggal =date('Y-m-d');
		  $p->terapi = "[]";
		  $p->antrian =$antrian;
		  $p->jam_periksa =date('H:i:s');
		  $p->jam_selesai_periksa =date('H:i:s');
		  $p->keterangan = "";
		  $p->transaksi = '[{"jenis_tarif_id":"1","jenis_tarif":"Jasa Dokter","biaya":0},{"jenis_tarif_id":"9","jenis_tarif":"Biaya Obat","biaya":0}]';
		  $p->save();
	}

    protected $morphClass = 'App\AntrianPeriksa';
    public function promos(){
        return $this->morphMany('App\Promo', 'jurnalable');
    }
	public function editPoli($id){
		$messages = [
			'required' => ':attribute Harus Diisi',
		];

		$rules = [
			'poli' => 'required',
		];
		
		$validator = \Validator::make(Input::all(), $rules, $messages);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}

		$ap       = AntrianPeriksa::find( $id );
		$ap->poli = Input::get('poli');
		$ap->save();

		$pesan = Yoga::suksesFlash('Pasien atas nama ' . $ap->pasien->nama . ' <strong>BERHASIL</strong> dipindah ke poli ' . $ap->poli);
		return redirect()->back()->withPesan($pesan);
	}
}
