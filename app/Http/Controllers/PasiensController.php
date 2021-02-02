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
use DB;

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
	public $input_panggilan;
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
	public $input_prolanis_dm;
	public $input_prolanis_ht;

	public $dataIndexPasien;
	public $dataCreatePasien;


   public function __construct()
    {

		$ps                              = new Pasien;
		$this->input_alamat              = Input::get('alamat');
		$this->input_asuransi_id         = $this->asuransiId(Input::get('asuransi_id'));
		$this->input_sex                 = Input::get('sex');
		$this->input_panggilan           = Input::get('panggilan');
		$this->input_jenis_peserta       = Input::get('jenis_peserta');
		$this->input_nama_ayah           = ucwords(strtolower(Input::get('nama_ayah')));;
		$this->input_nama_ibu            = ucwords(strtolower(Input::get('nama_ibu')));;
		$this->input_nama                = ucwords(strtolower(Input::get('nama')));
		$this->input_nama_peserta        = ucwords(strtolower(Input::get('nama_peserta')));;
		$this->input_nomor_asuransi      = Input::get('nomor_asuransi');
		$this->input_punya_asuransi      = Input::get('punya_asuransi');
		$this->input_nomor_ktp           = Input::get('nomor_ktp');
		$this->input_nomor_asuransi_bpjs = $this->nomorAsuransiBpjs(Input::get('nomor_asuransi'), $this->input_asuransi_id);
		$this->input_no_telp             = Input::get('no_telp');
		$this->input_tanggal_lahir       = Yoga::datePrep(Input::get('tanggal_lahir'));
		$this->input_jangan_disms        = Input::get('jangan_disms');
		$this->input_bpjs_image          = $ps->imageUpload('bpjs','bpjs_image', $this->input_id);
		$this->input_ktp_image           = $ps->imageUpload('ktp','ktp_image', $this->input_id);
		$this->input_image               = $ps->imageUploadWajah('img', 'image', $this->input_id);
		$this->input_prolanis_dm               = Input::get('prolanis_dm');
		$this->input_prolanis_ht               = Input::get('prolanis_ht');

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

		$pasien     = new Pasien;
		$pasien->id = Yoga::customId('App\Pasien');
		$pasien     = $this->inputDataPasien($pasien);
		$ap         = $this->inputDataAntrianPoli($pasien);

		$pesan = Yoga::suksesFlash( '<strong>' . $pasien->id . ' - ' . $pasien->nama . '</strong> Berhasil dibuat dan berhasil masuk antrian Nurse Station' );
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

			$pasien = Pasien::find($id);
			$pasien = $this->inputDataPasien($pasien);

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
	public function inputDataPasien($pasien){

		$pasien->alamat              = $this->input_alamat;
		$pasien->panggilan           = $this->input_panggilan;
		$pasien->prolanis_dm         = $this->input_prolanis_dm;
		$pasien->prolanis_ht         = $this->input_prolanis_ht;
		$pasien->asuransi_id         = $this->input_asuransi_id;
		$pasien->sex                 = $this->input_sex;
		$pasien->jenis_peserta       = $this->input_jenis_peserta;
		$pasien->nama_ayah           = $this->input_nama_ayah;
		$pasien->nama_ibu            = $this->input_nama_ibu;
		$pasien->nama                = $this->input_nama;
		$pasien->panggilan           = $this->input_panggilan;
		$pasien->nama_peserta        = $this->input_nama_peserta;
		$pasien->nomor_asuransi      = $this->input_nomor_asuransi;
		$pasien->nomor_ktp           = $this->input_nomor_ktp;
		$pasien->nomor_asuransi_bpjs = $this->input_nomor_asuransi_bpjs;
		$pasien->no_telp             = $this->input_no_telp;
		$pasien->tanggal_lahir       = $this->input_tanggal_lahir;
		$pasien->jangan_disms        = $this->input_jangan_disms;
		if (!empty( $this->input_bpjs_image )) {
			$pasien->bpjs_image          = $this->input_bpjs_image;
		}
		if (!empty($this->input_ktp_image)) {
			$pasien->ktp_image           = $this->input_ktp_image;
		}
		if (!empty($this->input_image)) {
			$pasien->image               = $this->input_image;
		}

		$pasien->save();
		return $pasien;
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
	public function pc2020(){
		$query  = "select ";
		$query .= "psn.id, ";
		/* $query .= "asu.nama as nama_asuransi, "; */
		$query .= "psn.nama as nama_pasien ";
		$query .= "from pasiens as psn ";
		$query .= "join periksas as prx on prx.pasien_id = psn.id ";
		/* $query .= "join asuransis as asu on asu.id = psn.id "; */
		$query .= "where ";
		$query .= "prx.tanggal like '2020%' ";
		$query .= "and (prx.asuransi_id = 200216001 ";
		$query .= "or prx.asuransi_id = 200312001 ";
		$query .= "or prx.asuransi_id = 200312002 ";
		$query .= "or prx.asuransi_id = 37) ";
		$query .= "group by psn.id;";
		/* dd($query); */
		$pasiens = DB::select($query);
		return view('pasiens.pc2020', compact(
			'pasiens'
		));
	}
	public function prolanisTerkendali(){
		return view('pasiens.prolanis_terkendali');
	}
	public function prolanisTerkendaliPerBulan(){
		$bulan      = Input::get('bulan');
		$tahun      = Input::get('tahun');

		$bulanTahun = $bulan . '-' . $tahun;
		$tahunBulan = $tahun . '-' . $bulan;

		$data = $this->queryDataProlanisPerBulan($tahunBulan);

		$prolanis_dm = [];
		$prolanis_ht = [];

		foreach ($data as $d) {
			$prolanis_ht = $this->templateProlanisPeriksa($prolanis_ht, $d, 'prolanis_ht');
			$prolanis_dm = $this->templateProlanisPeriksa($prolanis_dm, $d, 'prolanis_dm');
		}
		/* dd( compact('prolanis_dm', 'prolanis_ht') ); */
		return view('pasiens.prolanis_perbulan', compact(
			'prolanis_ht',
			'bulanTahun',
			'tahunBulan',
			'prolanis_dm'
		));
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	public function templateProlanisPeriksa($prolanis, $d, $jenis_prolanis)
	{
		if ( $d->$jenis_prolanis ) {
			$prolanis[$d->periksa_id]['nama']           = $d->nama;
			$prolanis[$d->periksa_id]['tanggal']        = $d->tanggal;
			$prolanis[$d->periksa_id]['tanggal_lahir']  = $d->tanggal_lahir;
			$prolanis[$d->periksa_id]['alamat']         = $d->alamat;
			$prolanis[$d->periksa_id]['sistolik']       = $d->sistolik;
			$prolanis[$d->periksa_id]['diastolik']      = $d->diastolik;
			$prolanis[$d->periksa_id]['nama_asuransi']  = $d->nama_asuransi;
			$prolanis[$d->periksa_id]['nomor_asuransi'] = $d->nomor_asuransi;
			if ( 
				$d->jenis_tarif_id == '116'
			) {
				$prolanis[$d->periksa_id]['gula_darah'] = $d->keterangan_pemeriksaan;
			}
		}
		return $prolanis;
	}
	public function queryDataProlanisPerBulan($tahunBulan){
		$query  = "SELECT ";
		$query .= "prx.tanggal as tanggal, ";
		$query .= "psn.nama as nama, ";
		$query .= "jtf.jenis_tarif as jenis_tarif, ";
		$query .= "psn.prolanis_dm as prolanis_dm, ";
		$query .= "psn.prolanis_ht as prolanis_ht, ";
		$query .= "psn.tanggal_lahir as tanggal_lahir, ";
		$query .= "psn.alamat as alamat, ";
		$query .= "trx.keterangan_pemeriksaan as keterangan_pemeriksaan, ";
		$query .= "prx.sistolik as sistolik, ";
		$query .= "prx.diastolik as diastolik, ";
		$query .= "prx.nomor_asuransi as nomor_asuransi, ";
		$query .= "prx.id as periksa_id, ";
		$query .= "asu.nama as nama_asuransi, ";
		$query .= "jtf.id as jenis_tarif_id ";
		$query .= "FROM periksas as prx ";
		$query .= "JOIN pasiens as psn on psn.id = prx.pasien_id ";
		$query .= "JOIN asuransis as asu on asu.id = prx.asuransi_id ";
		$query .= "LEFT JOIN transaksi_periksas as trx on prx.id = trx.periksa_id ";
		$query .= "JOIN jenis_tarifs as jtf on jtf.id = trx.jenis_tarif_id ";
		$query .= "WHERE prx.tanggal like '{$tahunBulan}%' ";
		$query .= "AND (psn.prolanis_ht = 1 or psn.prolanis_dm = 1) ";
		$query .= "AND prx.asuransi_id = 32";
		return DB::select($query);
	}
	public function inputDataAntrianPoli($pasien){
		$ap                    = new AntrianPolisController;
		$ap->input_pasien_id   = $pasien->id;
		$ap->input_asuransi_id = $pasien->asuransi_id;
		$ap->input_antrian_id  = $this->input_antrian_id;

		$ap->input_poli        = 'darurat';
		$ap->input_tanggal     = date('Y-m-d');
		$ap->input_jam         = date("H:i:s");
		return $ap->inputDataAntrianPoli();
	}
	
}
