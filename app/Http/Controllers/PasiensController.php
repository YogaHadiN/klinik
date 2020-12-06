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


	public $input_alamat;
	public $input_asuransi_id;
	public $input_sex;
	public $input_jenis_peserta;
	public $input_nama_ayah;
	public $input_nama_ibu;
	public $input_nama;
	public $input_nama_peserta;
	public $input_nomor_asuransi;
	public $input_nomor_ktp;
	public $input_nomor_asuransi_bpjs;
	public $input_no_telp;
	public $input_tanggal_lahir;
	public $input_jangan_disms;
	public $input_id;
	public $input_antrian_id;
	public $input_punya_asuransi;
	public $input_bpjs_image;
	public $input_ktp_image;
	public $input_image;

	public $dataIndexPasien;
	public $dataCreatePasien;

   public function __construct()
    {
		$ps                              = new Pasien;
		$this->input_alamat              = Input::get('alamat');
		$this->input_asuransi_id         = $this->asuransiId(Input::get('asuransi_id'));
		$this->input_sex                 = Input::get('sex');
		$this->input_jenis_peserta       = Input::get('jenis_peserta');
		$this->input_nama_ayah           = ucwords(strtolower(Input::get('nama_ayah')));;
		$this->input_nama_ibu            = ucwords(strtolower(Input::get('nama_ibu')));;
		$this->input_nama                = ucwords(strtolower(Input::get('nama')))  . ', ' . Input::get('panggilan');
		$this->input_nama_peserta        = ucwords(strtolower(Input::get('nama_peserta')));;
		$this->input_nomor_asuransi      = Input::get('nomor_asuransi');
		$this->input_punya_asuransi = Input::get('punya_asuransi');
		$this->input_nomor_ktp           = Input::get('no_ktp');
		$this->input_nomor_asuransi_bpjs = $this->nomorAsuransiBpjs(Input::get('nomor_asuransi'), $this->input_asuransi_id);
		$this->input_no_telp             = Input::get('no_telp');
		$this->input_tanggal_lahir       = Yoga::datePrep(Input::get('tanggal_lahir'));
		$this->input_jangan_disms        = Input::get('jangan_disms');
		$this->input_id                  = Yoga::customId('App\Pasien');
		$this->input_bpjs_image          = $ps->imageUpload('bpjs','bpjs_image', $this->input_id);
		$this->input_ktp_image           = $ps->imageUpload('ktp','ktp_image', $this->input_id);
		$this->input_image               = $ps->imageUploadWajah('img', 'image', $this->input_id);

		$this->dataIndexPasien = [
			'statusPernikahan' => $ps->statusPernikahan(),
			'panggilan'        => $ps->panggilan(),
			'asuransi'         => Yoga::asuransiList(),
			'jenis_peserta'    => $ps->jenisPeserta(),
			'staf'             => Yoga::stafList(),
			'poli'             => [
				null => '- Pilih Poli -',
				'darurat' => 'Poli Gawat Darurat'
			],
			'peserta'          => [ null => '- Pilih -', '0' => 'Peserta Klinik', '1' => 'Bukan Peserta Klinik']
		];
		$this->dataCreatePasien = [
			'statusPernikahan' => $ps->statusPernikahan(),
			'panggilan'        => $ps->panggilan(),
			'asuransi'         => Yoga::asuransiList(),
			'jenis_peserta'    => $ps->jenisPeserta(),
			'staf'             => Yoga::stafList(),
			'poli'             => [
				null => '- Pilih Poli -',
				'darurat' => 'Poli Gawat Darurat'
			],
			'pasienSurvey'          => $this->pasienSurvey()
		];

        /* $this->middleware('nomorAntrianUnik', ['only' => ['store']]); */
        $this->middleware('super', ['only' => 'delete']);
    }

	/**
	 * Display a listing of pasiens
	 *
	 * @return Response
	 */
	public function index()	{
		return view('pasiens.index', $this->dataIndexPasien);
	}

	/**
	 * Store a newly created pasien in storage.
	 *
	 * @return Response
	 */
	public function create(){
		return view('pasiens.create', $this->dataCreatePasien);
	}
	
	public function store(Request $request){
		$rules = [
			"nama"      => "required",
			"sex"       => "required",
			"panggilan" => "required"
		];

		if ( $this->input_punya_asuransi == '1' ) {
			  $rules["asuransi_id"]    = "required";
			  $rules["jenis_peserta"]  = "required";
			  $rules["nomor_asuransi"] = "required";
		}
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
		return $this->inputDataPasien();
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
	public function editAtAntrian($id, $antrian_id){
		$data = $this->editForm($id);
		$data['antrian_id'] = $antrian_id;
		return view('pasiens.edit', $data );
	}
	
	/**
	* undocumented function
	*
	* @return void
	*/
	private function editForm($id)
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

		$asuransi =  Asuransi::list();

		$jenis_peserta = array(
			null => ' - pilih asuransi -',  
			"P" => 'Peserta',
			"S" => 'Suami',
			"I" => 'Istri',
			"A" => 'Anak'
		);

		$antrian_id = null;

		$staf = array('0' => '- Pilih Staf -') + Staf::pluck('nama', 'id')->all();
		$pasienSurvey = $this->pasienSurvey();
		$poli = Yoga::poliList();
		return compact(
			'pasien',
			'asuransi',
			'statusPernikahan',
			'pasienSurvey',
			'panggilan',
			'jenis_peserta',
			'antrian_id',
			'staf',
			'poli'
		);
	}
	
	
	public function edit($id)
	{
		return view('pasiens.edit', $this->editForm($id) );
	}

	/**
	 * Update the specified pasien in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id){
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


			$antrian_id =  Input::get('antrian_id');
			if ( !empty( $antrian_id ) ) {
				return redirect("antrians/proses/" . $antrian_id)->withPesan(Yoga::suksesFlash('Data pasien <strong>' . $pasien->id . ' - ' . $pasien->nama . '</strong> berhasil dirubah'));
			} 

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
	public function inputDataPasien(){
		$pasien                      = new Pasien;
		$pasien->alamat              = $this->input_alamat;
		$pasien->asuransi_id         = $this->input_asuransi_id;
		$pasien->sex                 = $this->input_sex;
		$pasien->jenis_peserta       = $this->input_jenis_peserta;
		$pasien->nama_ayah           = $this->input_nama_ayah;
		$pasien->nama_ibu            = $this->input_nama_ibu;
		$pasien->nama                = $this->input_nama;
		$pasien->nama_peserta        = $this->input_nama_peserta;
		$pasien->nomor_asuransi      = $this->input_nomor_asuransi;
		$pasien->nomor_ktp           = $this->input_nomor_ktp;
		$pasien->nomor_asuransi_bpjs = $this->input_nomor_asuransi_bpjs;
		$pasien->no_telp             = $this->input_no_telp;
		$pasien->tanggal_lahir       = $this->input_tanggal_lahir;
		$pasien->jangan_disms        = $this->input_jangan_disms;
		$pasien->id                  = $this->input_id;
		$pasien->bpjs_image          = $this->input_bpjs_image;
		$pasien->ktp_image           = $this->input_ktp_image;
		$pasien->image               = $this->input_image;

		$pasien->save();
		$ap                    = new AntrianPolisController;
		$ap->input_pasien_id   = $pasien->id;
		$ap->input_asuransi_id = $pasien->asuransi_id;
		$ap->input_antrian_id  = $this->input_antrian_id;

		$ap->input_poli        = 'darurat';
		$ap->input_tanggal     = date('Y-m-d');
		$ap->input_jam         = date("H:i:s");
		$ap                    = $ap->inputDataAntrianPoli();

		$pesan = Yoga::suksesFlash( '<strong>' . $pasien->id . ' - ' . $pasien->nama . '</strong> Berhasil dibuat dan berhasil masuk antrian Nurse Station' );
		return redirect('antrianpolis')
			->withPesan($pesan);
	}

	public function asuransiId($asu_id){
		if (empty(trim($asu_id))) {
			$asuransi_id = 0;
		} else {
			$asuransi_id = $asu_id;
		}
		return $asuransi_id;
	}
	public function nomorAsuransiBpjs($nomor_asuransi, $asur_id){
		if ($asur_id == '32') {
			return Input::get('nomor_asuransi');
		}
		return null;
	}
	
	
	

}
