<?php


namespace App\Http\Controllers;

use Input;

use App\Http\Requests;
use App\Asuransi;
use App\Promo;
use App\Perujuk;
use App\Pasien;
use App\AntrianPoli;
use App\Complain;
use App\Kabur;
use App\Classes\Yoga;
use DB;

class AntrianPolisController extends Controller
{


	public function __construct() {
        $this->middleware('nomorAntrianUnik', ['only' => ['store']]);
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
		$antrianpolis  = AntrianPoli::with('pasien', 'asuransi', 'antars')
								//->where('tanggal', '<=', date('Y-m-d'))
								->orderBy('antrian', 'asc')->get();

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
		/* return Input::all(); */ 
		//DB::beginTransaction();
		//try {
			if (empty(Pasien::find(Input::get('pasien_id'))->image) && Input::get('asuransi_id') == '32') {
				return redirect('pasiens/' . Input::get('pasien_id') . '/edit')->withCek('Gambar <strong>Foto pasien (bila anak2) atau gambar KTP pasien (bila DEWASA) </strong> harus dimasukkan terlebih dahulu');
			}

			$rules = [
				'tanggal' => 'required',
				'pasien_id' => 'required',
				'poli' => 'required'
			];
			
			$validator = \Validator::make(Input::all(), $rules);
			
			if ($validator->fails())
			{
				return \Redirect::back()->withErrors($validator)->withInput();
			}

			$ap                      = new AntrianPoli;
			$ap->antrian             = Input::get('antrian');
			$ap->asuransi_id         = Input::get('asuransi_id');
			$ap->pasien_id           = Input::get('pasien_id');
			$ap->poli                = Input::get('poli');
			$ap->staf_id             = Input::get('staf_id');
			if ( Input::get('poli') == '32' ) {
				$ap->bukan_peserta   = Input::get('bukan_peserta');
			}
			$ap->jam                 = date("H:i:s");
			$ap->tanggal             = Yoga::datePrep( Input::get('tanggal') );
			$ap->save();

			$pasien = Pasien::find(Input::get('pasien_id'));

			if(Input::get('staf_id_complain') != ''){
				$conf = false;
				$complain = new Complain;
				$complain->pasien_id = Input::get('pasien_id');
				$complain->tanggal = date('Y-m-d');
				$complain->staf_id = Input::get('staf_id_complain');
				$complain->complain = Input::get('complain');
				$conf = $complain->save();
			}

			if ( !empty(trim(   Input::get('no_ktp')   ))) {

				$conf = false;
				$promo = Promo::where('no_ktp', Input::get('no_ktp'))->where('tahun', date('Y'))->get();

				if ( $promo->count() > 0 ) {
					$p                  = new Promo;
					$p->no_ktp          = Input::get('no_ktp');
					$p->tahun           = date('Y');
					$p->promoable_id = $antrian_poli_id;
					$p->promoable_type = 'App\AntrianPoli';
					$p->save();
				} else {
					$conf = false;
				}

				if (!$conf) {
					$pesan = Yoga::gagalFlash('<strong>Mohon Maag</strong> Promo sudah pernah digunakan sebelumnya untuk Tahun ini');
				}
			}

			$pesan = Yoga::suksesFlash('<strong>' . $pasien->id . ' - ' . $pasien->nama . '</strong> Berhasil masuk antrian Nurse Station Dan <strong>Komplain berhasil didokumentasikan</strong>');

			
			if (Input::get('asuransi_id') == '32') {
				return redirect('antrianpolis/pengantar/create/' . $ap->id)->withPesan(Yoga::suksesFlash('Harap Isi dulu pengantar pasien sebagai data kunjungan sehat'));
			}

			if ( $ap->poli == 'usg' ) {
				return redirect('antrianpolis')
					->withPrint($ap)
					->withPesan($pesan);
			}
			//DB::commit();
			return redirect('antrianpolis')
				->withPesan($pesan);
		//} catch (\Exception $e) {
			//DB::rollback();
			//throw $e;
		//}
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
		return dd( Input::all() );

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

}
