<?php
namespace App\Http\Controllers;

use Input;
use App\Http\Requests;
use App\Asuransi;
use App\Berkas;
use App\Tarif;
use App\TipeAsuransi;
use App\Email;
use App\Telpon;
use App\Pic;
use App\PembayaranAsuransi;
use App\Http\Controllers\CustomController;
use App\Coa;
use App\CatatanAsuransi;
use App\TipeTindakan;
use App\Classes\Yoga;
use App\Http\Requests\AsuransiValidation;

use DB;


class AsuransisController extends Controller
{

	public $input_nama             = '';
	public $input_pic             = '';
	public $input_alamat           = '';
	public $input_telpon          = '';
	public $input_tanggal_berakhir = '';
	public $input_penagihan        = '';
	public $input_gigi             = '';
	public $input_rujukan          = '';
	public $input_tipe_asuransi    = '';
	public $input_email            = '';
	public $input_umum             = '';
	public $input_kali_obat        = '';
	public $input_kata_kunci       = '';
	public $input_aktif;
	public $hasfile;
	public $input_id;
	public $input_nama_file;
	public $input_file;
	public $berkasable_type;
	public $input_folder;

   public function __construct()
    {
        $this->middleware('super', ['only'   => 'delete']);
        $this->middleware('admin', ['except' => []]);
		$this->input_nama                     = ucwords(strtolower(Input::get('nama')));
		$this->input_alamat                   = Input::get('alamat');
		$this->input_pic                      = Input::get('pic');
		$this->input_hp_pic                   = Input::get('hp_pic');
		$this->input_telpon                   = Input::get('telpon');
		$this->input_email                    = Input::get('email');
		$this->input_tanggal_berakhir         = Yoga::datePrep(Input::get('tanggal_berakhir'));
		$this->input_penagihan                = Yoga::cleanArrayJson(Input::get('penagihan'));
		$this->input_gigi                     = Yoga::cleanArrayJson(Input::get('gigi'));
		$this->input_rujukan                  = Yoga::cleanArrayJson(Input::get('rujukan'));
		$this->input_tipe_asuransi            = Input::get('tipe_asuransi');
		$this->input_umum                     = Yoga::cleanArrayJson(Input::get('umum'));
		$this->input_kali_obat                = Input::get('kali_obat');
		$this->input_kata_kunci               = Input::get('kata_kunci');
		$this->hasfile                        = Input::hasFile('file');
		$this->input_id                       = Input::get('asuransi_id');
		$this->input_nama_file                = Input::get('nama_file');
		$this->input_aktif                    = Input::get('aktif');
		$this->input_file                     = Input::file('file');
		$this->berkasable_type                = 'App\\Asuransi';
		$this->input_folder                   = 'asuransi';
    }
	/**
	 * Display a listing of asuransis
	 *
	 * @return Response
	 */
	public function index()
	{
		$asuransis = Asuransi::where('id', '>', 0)->get();

		$asur = [];

		/* return $asuransis->first()->belum; */

		foreach ($asuransis as $key => $asu) {
			$asur[] = [
				'id'     => $asu->id,
				'nama'   => $asu->nama,
				'alamat' => $asu->alamat,
				'pic'    => $asu->pic,
				'hp_pic' => $asu->hp_pic
			];
		}
		
		return view('asuransis.index', compact('asuransis'));
	}

	/**
	 * Show the form for creating a new asuransi
	 *
	 * @return Response
	 */
	public function create()
	{	
		$tarifs             = $this->tarifTemp()['tarif'];
		$tipe_tindakans     = TipeTindakan::all();
		$tipe_asuransi_list = $this->tipe_asuransi_list();
		$px                 = new CustomController;
		$warna              = $px->warna;
		return view('asuransis.create', compact(
			'warna', 
			'tipe_tindakans', 
			'tipe_asuransi_list', 
			'tarifs'
		));
	}

	/**
	 * Store a newly created asuransi in storage.
	 *
	 * @return Response
	 */
	public function store() {
		DB::beginTransaction();
		try {
			$asuransi         = new Asuransi;
			$asuransi->id     = Yoga::customId('App\Asuransi');
			$asuransi         = $this->inputData($asuransi);

			$coa_id               = (int)Coa::where('id', 'like', '111%')->orderBy('id', 'desc')->first()->id + 1;
			$coa                  = new Coa;
			$coa->id              = $coa_id;
			$coa->kelompok_coa_id = '11';
			$coa->coa             = 'Piutang Asuransi ' . $asuransi->nama;
			$coa->save();


			$asuransi->coa_id = $coa_id;
			$asuransi->save();

			$tarifs = Input::get('tarifs');
			$tarifs = json_decode($tarifs, true);

			$data = [];

			foreach ($tarifs as $tarif_pribadi) {
				$data [] = [
					'biaya'                 => $tarif_pribadi['biaya'],
					'asuransi_id'           => $asuransi->id,
					'jenis_tarif_id'        => $tarif_pribadi['jenis_tarif_id'],
					'tipe_tindakan_id'      => $tarif_pribadi['tipe_tindakan_id'],
					'bhp_items'             => $tarif_pribadi['bhp_items'],
					'jasa_dokter'           => $tarif_pribadi['jasa_dokter'],
					'jasa_dokter_tanpa_sip' => $tarif_pribadi['jasa_dokter']
				];
			}
			Tarif::insert($data);
			DB::commit();
			return \Redirect::route('asuransis.index')->withPesan(Yoga::suksesFlash('<strong>Asuransi ' . ucwords(strtolower(Input::get('nama')))  .'</strong> berhasil dibuat'));
		} catch (\Exception $e) {
			DB::rollback();
			throw $e;
		}
	}

	/**
	 * Display the specified asuransi.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$asuransi = Asuransi::findOrFail($id);

		return view('asuransis.show', compact('asuransi'));
	}

	/**
	 * Show the form for editing the specified asuransi.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$tarifTemp          = $this->tarifTemp($id);
		$tarifs             = $tarifTemp['tarif'];
		$tipe_tindakans     = TipeTindakan::all();
		$tipe_asuransi_list = $this->tipe_asuransi_list();
		$px                 = new CustomController;
		$warna              = $px->warna;
		$asuransi           = $tarifTemp['asuransi'];

		return view('asuransis.edit', compact(
			'asuransi', 
			'warna', 
			'tipe_tindakans', 
			'tipe_asuransi_list', 
			'tarifs'
		));
	}

	/**
	 * Update the specified asuransi in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(AsuransiValidation $request, $id)
	{
		DB::beginTransaction();
		try {
			$asuransi = Asuransi::findOrFail($id);

			Pic::where('asuransi_id', $id)->delete();
			Email::where('emailable_id', $id)->where('emailable_type', 'App\\Asuransi')->delete();
			Telpon::where('telponable_id', $id)->where('telponable_type', 'App\\Asuransi')->delete();

			$asuransi = $this->inputData($asuransi);

			$tarifs   = Input::get('tarifs');
			$tarifs   = json_decode($tarifs, true);

			foreach ($tarifs as $tarif) {
				$tf                   = Tarif::find($tarif['id']);
				$tf->biaya            = $tarif['biaya'];
				$tf->jasa_dokter      = $tarif['jasa_dokter'];
				$tf->tipe_tindakan_id = $tarif['tipe_tindakan_id'];
				$confirm = $tf->save();

				if (!$confirm) {
					return 'update gagal';
				}
			}
			DB::commit();
			return \Redirect::route('asuransis.index')->withPesan(Yoga::suksesFlash('<strong>Asuransi ' . Input::get('nama') . '</strong> berhasil diubah'));
		} catch (\Exception $e) {
			DB::rollback();
			throw $e;
		}
	}

	/**
	 * Remove the specified asuransi from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{	
		$asuransi = Asuransi::find($id);
		$nama     = $asuransi->nama;
		Tarif::where('asuransi_id', $id)->delete();
		Coa::destroy($asuransi->coa_id);
		Email::where('emailable_id', $asuransi->id)->where('emailable_type', 'App\\Asuransi')->delete();
		Pic::where('asuransi_id', $asuransi->id)->delete();
		$asuransi->delete();


		return \Redirect::route('asuransis.index')->withPesan(Yoga::suksesFlash('<strong>Asuransi ' . $nama . '</strong> berhasil dihapus'));
	}

	public function riwayat($id){
		//$periksas = Periksa::where('asuransi_id', $id)->orderBy('created_at', 'desc')->get();
		//

		$hutangs     = $this->hutangs_template($id);
		/* return $hutangs; */
		$asuransi    = Asuransi::find( $id );
		$pembayarans = $this->pembayarans_template($id);

		return view('asuransis.hutangPembayaran', compact(
			'asuransi',
			'hutangs',
			'pembayarans'
		));
	}
	public function hutangPerBulan($bulan, $tahun){

		$query  ="SELECT ";
		$query .="bl.id, ";
		$query .="bl.tanggal, ";
		$query .="bl.nama_asuransi, ";
		$query .="sum(bl.hutang) as hutang, ";
		$query .="sum(bl.sudah_dibayar) as sudah_dibayar ";
		$query .="FROM ( ";
		$query .="SELECT year(ju.created_at) as tahun, ";
		$query .="month(ju.created_at) as bulan, ";
		$query .="ju.created_at as tanggal, ";
		$query .="ju.nilai as hutang, ";
		$query .="asu.nama as nama_asuransi, ";
		$query .="asu.id as id, ";
		$query .="sum(byr.pembayaran) as sudah_dibayar ";
		$query .="FROM jurnal_umums as ju ";
		$query .="join periksas as px on px.id = ju.jurnalable_id ";
		$query .="join pasiens as ps on ps.id = px.pasien_id ";
		$query .="join asuransis as asu on px.asuransi_id = asu.id ";
		$query .="left join piutang_dibayars as byr on px.id = byr.periksa_id ";
		$query .="where jurnalable_type = 'App\\\Periksa' ";
		$query .="AND px.created_at like '{$tahun}-{$bulan}%' ";
		$query .="AND px.asuransi_id > 0 ";
		$query .="AND ju.coa_id like '111%' ";
		$query .="AND ju.debit = '1' ";
		$query .="Group by ju.id) bl ";
		$query .="Group by bl.id";

		$data = DB::select($query);


		/* return $data; */
		$total_hutang = 0;
		$total_sudah_dibayar = 0;
		foreach ($data as $d) {
			$total_hutang           += $d->hutang;
			$total_sudah_dibayar    += $d->sudah_dibayar;
		}
		
		return view('asuransis.hutangPerBulan', compact(
			'data',
			'total_hutang',
			'total_sudah_dibayar',
			'tahun',
			'bulan'
		));
	}
	
	public function hutang($year){
		$query  = "SELECT ";
		$query .= "bl.tanggal, ";
		$query .= "bl.bulan, ";
		$query .= "bl.tahun, ";
		$query .= "sum(bl.hutang) as piutang, ";
		$query .= "sum(bl.sudah_dibayar) as sudah_dibayar ";
		$query .= "FROM ( ";
		$query .= "SELECT ";
		$query .= "year(ju.created_at) as tahun,";
		$query .= " month(ju.created_at) as bulan,";
		$query .= " ju.created_at as tanggal,";
		$query .= " ju.nilai as hutang,";
		$query .= " sum(byr.pembayaran) as sudah_dibayar";
		$query .= " FROM jurnal_umums as ju";
		$query .= " join periksas as px on px.id = ju.jurnalable_id";
		$query .= " join pasiens as ps on ps.id = px.pasien_id";
		$query .= " join asuransis as asu on px.asuransi_id = asu.id";
		$query .= " left join piutang_dibayars as byr on px.id = byr.periksa_id";
		$query .= " where jurnalable_type = 'App\\\Periksa'";
		$query .= " AND px.asuransi_id > 0";
		$query .= " AND ju.coa_id like '111%'";
		$query .= " AND ju.debit = '1'";
		$query .= " AND px.tanggal like '{$year}%' ";
		$query .= " GROUP BY ju.id) bl";
		$query .= " GROUP BY bl.tahun DESC, bl.bulan DESC;";

		$data_piutang = DB::select($query);

		/* return view('asuransis.hutang', compact( */
		/* 	'data', */
		/* 	'data_piutang', */
		/* 	'total_hutang', */
		/* 	'total_sudah_dibayar', */
		/* 	'tahun', */
		/* 	'bulan' */
		/* )); */

		return view('asuransis.hutang', compact(
			'data_piutang'
		));
	}
	public function hutangs_template($id){

		$query  ="select ";
		$query .="sum(bl.hutang) as hutang, ";
		$query .="sum(bl.sudah_dibayar) as sudah_dibayar, ";
		$query .="bl.jumlah_pembayaran, ";
		$query .="asuransi_id, ";
		$query .="bl.nama_asuransi as nama_asuransi, ";
		$query .="month(bl.tanggal) as bulan, ";
		$query .="year(bl.tanggal) as tahun, ";
		$query .="bl.tanggal ";
		$query .="from ( ";
		$query .="select ";
		$query .="pias.created_at as tanggal, ";
		$query .="pias.piutang as hutang, ";
		$query .="asu.nama as nama_asuransi, ";
		$query .="asu.id as asuransi_id, ";
		$query .="sum( pd.pembayaran ) as sudah_dibayar, ";
		$query .="count( pd.pembayaran ) as jumlah_pembayaran, ";
		$query .="pias.id as id ";
		$query .="from piutang_asuransis as pias ";
		$query .="join periksas as px on px.id = pias.periksa_id ";
		$query .="join asuransis as asu on px.asuransi_id = asu.id ";
		$query .="left join piutang_dibayars as pd on pd.periksa_id = px.id ";
		$query .="where px.asuransi_id = '{$id}' ";
		/* $query .="having count(pd.pembayaran) > 1 "; */
		$query .="group by pias.id ";
		$query .=")bl ";
		$query .="group by year(tanggal) desc, month(tanggal) desc ";
		$result = DB::select($query);

		return $result;
	}
	public function pembayarans_template($id){
		return PembayaranAsuransi::with('staf')->where('asuransi_id', $id)->orderBy('tanggal_dibayar', 'desc')->get();
	}

	public function piutangAsuransiSudahDibayar( $asuransi_id, $mulai, $akhir ){

		$asuransi             = Asuransi::find( $asuransi_id );
		$pendapatanController = new PendapatansController;
        $sudah_dibayars       = $pendapatanController->sudahDibayar( $mulai, $akhir, $asuransi_id );

		$total_tunai          = 0;
		$total_piutang        = 0;
		$total_sudah_dibayar  = 0;
		$total_sisa_piutang   = 0;

		foreach ($sudah_dibayars as $sudah) {
			$total_tunai         += $sudah->tunai;
			$total_piutang       += $sudah->piutang;
			$total_sudah_dibayar += $sudah->sudah_dibayar;
			$total_sisa_piutang  += $sudah->piutang - $sudah->sudah_dibayar;
		}

		$query  = "SELECT ";
		$query .= "peas.tanggal_dibayar as tanggal_dibayar, ";
		$query .= "peas.mulai as mulai, ";
		$query .= "peas.id as id, ";
		$query .= "peas.akhir as akhir, ";
		$query .= "peas.pembayaran as pembayaran, ";
		$query .= "peas.created_at as tanggal_input, ";
		$query .= "asu.nama as nama_asuransi, ";
		$query .= "st.nama as nama_staf, ";
		$query .= "co.coa as coa ";
		$query .= "FROM pembayaran_asuransis as peas ";
		$query .= "JOIN asuransis as asu on asu.id = peas.asuransi_id ";
		$query .= "JOIN coas as co on co.id = peas.kas_coa_id ";
		$query .= "JOIN stafs as st on st.id = peas.staf_id ";
		$query .= "WHERE ( mulai like '" .date('Y-m', strtotime($mulai)) . '%'. "' or akhir like '" .date('Y-m', strtotime($akhir)) . '%'. "'  ) ";
		$query .= "AND asu.id = '{$asuransi_id}';";

		$pembayaran_asuransi = DB::select($query);

		$total_pembayaran= 0;

		foreach ($pembayaran_asuransi as $pem) {
			$total_pembayaran += $pem->pembayaran;
		}

		return view('asuransis.sudah_dibayar', compact(
			'asuransi',
			'mulai',
			'pembayaran_asuransi',
			'total_piutang',
			'total_tunai',
			'total_sudah_dibayar',
			'total_sisa_piutang',
			'akhir',
			'sudah_dibayars',
			'total_pembayaran'
		));
	}
	
	public function piutangAsuransiBelumDibayar($asuransi_id, $mulai, $akhir  ){
		$asuransi             = Asuransi::find( $asuransi_id );
		$pendapatanController = new PendapatansController;
        $belum_dibayars       = $pendapatanController->belumDibayar( $mulai, $akhir, $asuransi_id );

		$total_piutang       = 0;
		$total_sudah_dibayar = 0;
		$total_sisa_piutang  = 0;

		foreach ($belum_dibayars as $belum) {
			$total_piutang       += $belum->piutang;
			$total_sudah_dibayar += $belum->total_pembayaran;
			$total_sisa_piutang  += $belum->piutang - $belum->total_pembayaran;
		}

		return view('asuransis.belum_dibayar', compact(
			'asuransi',
			'mulai',
			'total_piutang',
			'total_sudah_dibayar',
			'total_sisa_piutang',
			'akhir',
			'belum_dibayars'
		));
	}
	public function riwayatHutang(){

		$hutangs = $this->hutangs_template( Input::get('asuransi_id') );

		$arr = [];
		foreach ($hutangs as $i => $hutang) {
			$arr[] = [
				'bulan'         => Yoga::bulan($hutang->bulan),
				'nama_asuransi' => $hutang->nama_asuransi,
				'asuransi_id' => $hutang->asuransi_id,
				'sudah_dibayar' => Yoga::buatrp($hutang->sudah_dibayar),
				'hutang'        => Yoga::buatrp($hutang->hutang),
				'tahun'         => $hutang->tahun
			];
		}
		return json_encode($arr);

	}

	public function querySemuaPiutangPerBulan($asuransi_id, $mulai, $akhir  ){
		
		$query  = "SELECT ";
		$query .= "px.created_at as tanggal_periksa, ";
		$query .= "px.id as periksa_id, ";
		$query .= "ps.nama as nama_pasien, ";
		$query .= "asu.nama as nama_asuransi, ";
		$query .= "pa.tunai as tunai, ";
		$query .= "pa.piutang as piutang, ";
		$query .= "sum(pd.pembayaran) as sudah_dibayar ";
		$query .= "FROM piutang_asuransis as pa ";
		$query .= "JOIN periksas as px on px.id = pa.periksa_id ";
		$query .= "LEFT JOIN piutang_dibayars as pd on pd.periksa_id = px.id ";
		$query .= "JOIN pasiens as ps on ps.id = px.pasien_id ";
		$query .= "JOIN asuransis as asu on asu.id = px.asuransi_id ";
		$query .= "WHERE asu.id = '{$asuransi_id}' ";
		$query .= "AND ( DATE(pa.created_at) between '{$mulai}' and '{$akhir}' ) ";
		$query .= "group by pa.id";

		return DB::select($query);
	}
	



	public function piutangAsuransi($asuransi_id, $mulai, $akhir  ){


		$piutangs = $this->querySemuaPiutangPerBulan($asuransi_id, $mulai, $akhir  );

		$asuransi = Asuransi::find( $asuransi_id );

		$total_tunai         = 0;
		$total_piutang       = 0;
		$total_sudah_dibayar = 0;

		foreach ($piutangs as $piutang) {
			$total_tunai         += $piutang->tunai;
			$total_piutang       += $piutang->piutang;
			$total_sudah_dibayar += $piutang->sudah_dibayar;
		}

		return view('asuransis.semua_piutang', compact(
			'mulai',
			'asuransi',
			'akhir',
			'piutangs',
			'total_piutang',
			'total_sudah_dibayar',
			'total_tunai'
		));
	}
	
	public function catatan(){
		$catatans = CatatanAsuransi::all();
		return view('asuransis.catatan', compact(
			'catatans'
		));
	}
	private function inputData($asuransi){
		$asuransi->nama             = $this->input_nama;
		$asuransi->alamat           = $this->input_alamat;
		$asuransi->tanggal_berakhir = $this->input_tanggal_berakhir;
		$asuransi->penagihan        = $this->input_penagihan;
		$asuransi->gigi             = $this->input_gigi;
		$asuransi->rujukan          = $this->input_rujukan;
		$asuransi->tipe_asuransi    = $this->input_tipe_asuransi;
		$asuransi->umum             = $this->input_umum;
		$asuransi->kali_obat        = $this->input_kali_obat;
		$asuransi->kata_kunci       = $this->input_kata_kunci;
		$asuransi->aktif            = $this->input_aktif;
		$asuransi->save();

		$timestamp = date('Y-m-d H:i:s');
		$emails = [];
		foreach ( $this->input_email as $email) {
			if ( !empty($email) ) {
				$emails[] = [
					'email' => $email,
					'emailable_id' => $asuransi->id,
					'emailable_type' => 'App\\Asuransi',
					'created_at' => $timestamp,
					'updated_at' => $timestamp
				];
			}
		}
		$pics = [];
		foreach ($this->input_pic as $k =>$pic) {
			if ( !empty( $pic ) ) {
				$pics[] = [
					'nama' => $pic,
					'nomor_telepon' => $this->input_hp_pic[$k],
					'asuransi_id' => $asuransi->id,
					'created_at' => $timestamp,
					'updated_at' => $timestamp
				];
			}
		}
		$telpons = [];
		foreach ( $this->input_telpon as $telpon) {
			if ( !empty($telpon) ) {
				$telpons[] = [
					'nomor'          => $telpon,
					'telponable_id'   => $asuransi->id,
					'telponable_type' => 'App\\Asuransi',
					'created_at'     => $timestamp,
					'updated_at'     => $timestamp
				];
			}
		}
		Email::insert($emails);
		Pic::insert($pics);
		Telpon::insert($telpons);
		return $asuransi;
	}
	public function kataKunciUnique(){
		$kata_kunci  = strtolower(trim(Input::get('kata_kunci')));
		$asuransi_id = Input::get('asuransi_id');
		if (empty( $kata_kunci)) {
			return '1';
		}
		if (isset( $asuransi_id )) {
			try {
				Asuransi::where('kata_kunci', $kata_kunci)->whereNot('id', $asuransi_id)->firstOrFail();
				return '0';
			} catch (\Exception $e) {
				return '1';
			}
		} else {
			try {
				Asuransi::where('kata_kunci', $kata_kunci)->firstOrFail();
				return '0';
			} catch (\Exception $e) {
				return '1';
			}
		}
	}
	public function tarifTemp($id = 0){
		$asuransi = Asuransi::with('pic', 'emails', 'tarif.jenisTarif', 'tarif.tipeTindakan')->where('id',$id)->first();
		$trf    = $asuransi->tarif;
		$tarifs = [];
		foreach ($trf as $t) {
			$tarifs[] = [
				'jenis_tarif'           => $t->jenisTarif->jenis_tarif,
				'jenis_tarif_id'        => $t->jenis_tarif_id,
				'id'                    => $t->id,
				'jasa_dokter'           => $t->jasa_dokter,
				'tipe_tindakan_id'      => $t->tipe_tindakan_id,
				'tipe_tindakan'         => $t->tipeTindakan->tipe_tindakan,
				'bhp_items'             => $t->bhp_items,
				'jasa_dokter_tanpa_sip' => $t->jasa_dokter_tanpa_sip,
				'biaya'                 => $t->biaya
			];
		}
		return [
			'tarif' => $tarifs,
			'asuransi' => $asuransi
		];
	}
	public function uploadBerkas(){
		if($this->hasfile) {
			$id           = $this->input_id;
			$nama_file    = $this->input_nama_file;
			$upload_cover = $this->input_file;
			$extension    = $upload_cover->getClientOriginalExtension();

			$berkas                  = new Berkas;
			$berkas->berkasable_id   = $id;
			$berkas->berkasable_type = $this->berkasable_type;
			$berkas->nama_file       = $nama_file;
			$berkas->save();


			$filename =	 $berkas->id . '.' . $extension;

			//menyimpan bpjs_image ke folder public/img
			//
			$destination_path = public_path() . DIRECTORY_SEPARATOR . 'berkas/' . $this->input_folder . '/' . $id;

			// Mengambil file yang di upload
			//
			//
			/* return [$filename, $destination_path]; */

			$upload_cover->move($destination_path , $filename);
			return $berkas->id;
			
		} else {
			return null;
		}
	}
	public function hapusBerkas(){
		$berkas_id = Input::get('berkas_id');
		if ( Berkas::destroy( $berkas_id ) ) {
			return '1';
		} else {
			return '0';
		}
	}
	private function tipe_asuransi_list(){
		$tipe_asuransi_list = [];
		foreach (TipeAsuransi::all() as $k => $value) {
			$tipe_asuransi_list[$value->id] = $value->tipe_asuransi;
		}
		return $tipe_asuransi_list;
	}
}
