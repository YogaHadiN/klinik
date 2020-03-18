<?php
namespace App\Http\Controllers;

use Input;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Classes\Yoga;
use App\Alergi;
use App\Http\Controllers\AntrianPolisController;
use App\Periksa;
use App\Pasien;
use App\Asuransi;
use App\AntrianPoli;
use App\Staf;

class PasiensController extends Controller
{

   /**
    * Buat construct untuk middleware super, jadi hanya bisa di lakukan oleh Pak Yoga
    *
    */
   public function __construct()
    {
        $this->middleware('nomorAntrianUnik', ['only' => ['store']]);
        $this->middleware('super', ['only' => 'delete']);
    }

	/**
	 * Display a listing of pasiens
	 *
	 * @return Response
	 */
	public function index()	{
		$ps               = new Pasien;
		$statusPernikahan = $ps->statusPernikahan();
		$panggilan        = $ps->panggilan();
		$asuransi         = Yoga::asuransiList();
		$jenis_peserta    = $ps->jenisPeserta();
		$staf             = Yoga::stafList();
		$poli             = Yoga::poliList();
		$peserta          = [ null => '- Pilih -', '0' => 'Peserta Klinik', '1' => 'Bukan Peserta Klinik'];
		return view('pasiens.index')
			->withAsuransi($asuransi)
			->with('statusPernikahan', $statusPernikahan)
			->with('panggilan', $panggilan)
			->with('peserta', $peserta)
			->withJenis_peserta($jenis_peserta)
			->withStaf($staf)
			->withPoli($poli);
	}

	/**
	 * Store a newly created pasien in storage.
	 *
	 * @return Response
	 */
	public function create(){
		$ps = new Pasien;
		$statusPernikahan = $ps->statusPernikahan();
		$panggilan = $ps->panggilan();
		$asuransi = Yoga::asuransiList();
		$jenis_peserta = $ps->jenisPeserta();
		$staf = Yoga::stafList();
		$poli = Yoga::poliList();
		$pasienSurvey = $this->pasienSurvey();
		
		return view('pasiens.create')
			->withAsuransi($asuransi)
			->with('statusPernikahan', $statusPernikahan)
			->with('pasienSurvey', $pasienSurvey)
			->with('panggilan', $panggilan)
			->with('jenis_peserta', $jenis_peserta)
			->withStaf($staf)
			->withPoli($poli);
	}
	
	public function store(Request $request){

		$rules = [
			"nama"      => "required",
			"sex"       => "required",
			"panggilan" => "required"
		];

		if ( Input::get('punya_asuransi') == '1' ) {
			  $rules["asuransi_id"]    = "required";
			  $rules["jenis_peserta"]  = "required";
			  $rules["nomor_asuransi"] = "required";
		}
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}

		if (empty(trim(Input::get('asuransi_id')))) {
			$asuransi_id = 0;
		} else {
			$asuransi_id = Input::get('asuransi_id');
		}

		$id = Yoga::customId('App\Pasien');

		$pasien                 = new Pasien;
		$pasien->alamat         = Input::get('alamat');
		$pasien->asuransi_id    = $asuransi_id;
		$pasien->sex            = Input::get('sex');
		$pasien->jenis_peserta  = Input::get('jenis_peserta');
		$pasien->nama_ayah      = ucwords(strtolower(Input::get('nama_ayah')));
		$pasien->nama_ibu       = ucwords(strtolower(Input::get('nama_ibu')));
		$pasien->nama           = ucwords(strtolower(Input::get('nama')))  . ', ' . Input::get('panggilan');
		$pasien->nama_peserta   = ucwords(strtolower(Input::get('nama_peserta')));
		$pasien->nomor_asuransi = Input::get('nomor_asuransi');
		$pasien->nomor_ktp = Input::get('no_ktp');
		if ($asuransi_id == '32') {
			$pasien->nomor_asuransi_bpjs = Input::get('nomor_asuransi');
		}
		$pasien->no_telp        = Input::get('no_telp');
		$pasien->tanggal_lahir  = Yoga::datePrep(Input::get('tanggal_lahir'));
		$pasien->jangan_disms   = Input::get('jangan_disms');
		$pasien->id             = $id;
		$pasien->bpjs_image     = $pasien->imageUpload('bpjs','bpjs_image', $id);
		$pasien->ktp_image      = $pasien->imageUpload('ktp', 'ktp_image', $id);
		$pasien->image          = $pasien->imageUploadWajah('img', 'image', $id);
		$pasien->save();
	
		$ap                    = new AntrianPolisController;
		$ap->input_pasien_id   = $id;
		$ap->input_asuransi_id = $asuransi_id;
		$ap->input_poli        = Input::get('poli');
		$ap->input_staf_id     = Input::get('staf_id');
		$ap->input_tanggal     = date('Y-m-d');
		$ap->input_antrian     = Input::get('antrian');
		$ap->input_jam         = date("H:i:s");
		$ap                    = $ap->inputDataAntrianPoli();


		$pesan = Yoga::suksesFlash( '<strong>' . $id . ' - ' . $pasien->nama . '</strong> Berhasil dibuat dan berhasil masuk antrian Nurse Station' );

		return redirect('antrianpolis')
			->withPesan($pesan);
	}
	
	

	/**
	 * Display the specified pasien.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$periksas = Periksa::with(
			'pasien', 
			'staf' ,
			'asuransi', 
			'suratSakit', 
			'gambars', 
			'usg', 
			'registerAnc', 
			'rujukan.tujuanRujuk', 
			'terapii.merek', 
			'diagnosa.icd10'
	   	)->where('pasien_id', $id)->orderBy('tanggal', 'desc')->paginate(10);

		if($periksas->count() > 0){
			return view('pasiens.show', compact('periksas'));
		}else {
			return redirect('pasiens')->withPesan(Yoga::gagalFlash('Tidak ada Riwayat Untuk Ditampilkan'));
		}
	}

	/**
	 * Show the form for editing the specified pasien.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$pasien = Pasien::find($id);

		$statusPernikahan = array( null => '- Status Pernikahan -',
									'Pernah' => 'Pernah Menikah',
									'Belum' => 'Belum Menikah'
									);
		$panggilan = array(
			null => '-Panggilan-',
			'Tn' => 'Tn',
			'Ny' => 'Ny',
			'Nn' => 'Nn',
			'An' => 'An',
			'By' => 'By'
			);

		$asuransi = array('0' => '- Pilih Asuransi -') + Asuransi::pluck('nama', 'id')->all();

		$jenis_peserta = array(

			null => ' - pilih asuransi -',  
			"P" => 'Peserta',
			"S" => 'Suami',
			"I" => 'Istri',
			"A" => 'Anak'

			);
		$staf = array('0' => '- Pilih Staf -') + Staf::pluck('nama', 'id')->all();
		$pasienSurvey = $this->pasienSurvey();
		$poli = Yoga::poliList();
		return view('pasiens.edit')
			->withPasien($pasien)
			->withAsuransi($asuransi)
			->with('statusPernikahan', $statusPernikahan)
			->with('pasienSurvey', $pasienSurvey)
			->with('panggilan', $panggilan)
			->with('jenis_peserta', $jenis_peserta)
			->withStaf($staf)
			->withPoli($poli);
	}

	/**
	 * Update the specified pasien in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$pasien = Pasien::findOrFail($id);
		$validator = \Validator::make($data = Input::all(), Pasien::$rules);

		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
			$pn = new Pasien;
			if (empty(trim(Input::get('asuransi_id')))) {
				$asuransi_id = 0;
			} else {
				$asuransi_id = Input::get('asuransi_id');
			}

			$pasien                 = Pasien::find($id);
			$pasien->alamat         = Input::get('alamat');
			$pasien->asuransi_id    = $asuransi_id;
			$pasien->sex            = Input::get('sex');
			$pasien->jenis_peserta  = Input::get('jenis_peserta');
			$pasien->nama_ayah      = Input::get('nama_ayah');
			$pasien->nama_ibu       = Input::get('nama_ibu');
			$pasien->nama           = Input::get('nama');
			$pasien->jangan_disms   = Input::get('jangan_disms');
			$pasien->nama_peserta   = Input::get('nama_peserta');
			$pasien->nomor_asuransi = Input::get('nomor_asuransi');
			$pasien->nomor_ktp      = Input::get('nomor_ktp');
			if ( $asuransi_id == '32') {
				$pasien->nomor_asuransi_bpjs = Input::get('nomor_asuransi');
			}
			$pasien->no_telp        = Input::get('no_telp');
			if (!empty(Input::hasFile('image'))) {
				$pasien->image      	= $pn->imageUploadWajah('img','image', $id);
			}
			if (Input::hasFile('bpjs_image')) {
				$pasien->bpjs_image     = $pn->imageUpload('bpjs','bpjs_image', $id);
			}
			if (Input::hasFile('ktp_image')) {
				$pasien->ktp_image      = $pn->imageUpload('ktp', 'ktp_image', $id);
			}
			$pasien->tanggal_lahir   = Yoga::datePrep(Input::get('tanggal_lahir'));
			$pasien->save();



			if ( !empty( Input::get('back') ) ) {
				
				return redirect( Input::get('back') )->withPesan(Yoga::suksesFlash('Data pasien <strong>' . $pasien->id . ' - ' . $pasien->nama . '</strong> berhasil dirubah'));
			} 
		return \Redirect::route('pasiens.index')->withPesan(Yoga::suksesFlash('Data pasien <strong>' . $pasien->id . ' - ' . $pasien->nama . '</strong> berhasil dirubah'));
	}
	/**
	 * Remove the specified pasien from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$pasien = Pasien::find($id);
		if (!Pasien::destroy($id)) return redirect()->back();
		$pesan = Yoga::suksesFlash('Pasien ' . $id . ' - ' . $pasien->nama . ' berhasil dihapus');
		return \Redirect::route('pasiens.index')->withPesan($pesan);
	}
	
	private function pasienSurvey(){
		 return [ 
			'0' => 'Pasien tidak keberatan menerima SMS survey',
			'1' => 'Pasien keberatan menerima SMS survey'
	   	];
	}
	public function alergi($id){
		$alergies = Alergi::where('pasien_id', $id)->get();
		$pasien   = Pasien::find($id);
		return view('pasiens/alergi', compact(
			'alergies',
			'pasien'
		));
	}
	public function alergiCreate($id){
		$generiks = Generik::list();
		$pasien = Pasien::find($id);
		return view('pasiens/alergiCreate', compact(
			'generiks',
			'pasien'
		));
		
	}
	
	
	public function alergiDelete($id){
		$alergies = Alergi::where('pasien_id', $id)->get();
		return view('pasiens/alergi', compact(
			'alergies',
			'pasien'
		));
	}
	
	

}
