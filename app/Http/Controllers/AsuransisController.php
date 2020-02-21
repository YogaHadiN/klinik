<?php
namespace App\Http\Controllers;

use Input;
use App\Http\Requests;
use App\Asuransi;
use App\Tarif;
use App\PembayaranAsuransi;
use App\Coa;
use App\CatatanAsuransi;
use App\Classes\Yoga;

use DB;


class AsuransisController extends Controller
{

   public function __construct()
    {
        $this->middleware('super', ['only' => 'delete']);
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
		$tarifs = Tarif::where('asuransi_id', '0')->get()	;
		return view('asuransis.create', compact('tarifs'));
	}

	/**
	 * Store a newly created asuransi in storage.
	 *
	 * @return Response
	 */
	public function store()
	{


		$asuransi_id = Yoga::customId('App\Asuransi');
		$asuransi = new Asuransi;
		$asuransi->id = $asuransi_id;
		$asuransi->alamat = Input::get('alamat');
		$asuransi->tipe_asuransi = Input::get('tipe_asuransi');
		$asuransi->nama = ucwords(strtolower(Input::get('nama')));
		$asuransi->hp_pic = Input::get('hp_pic');
		$asuransi->no_telp = Input::get('no_telp');
		$asuransi->email = Input::get('email');
		$asuransi->pic = Input::get('pic');
		$asuransi->kali_obat = 1.25;
		$asuransi->tanggal_berakhir = Yoga::datePrep(Input::get('tanggal_berakhir'));
		$asuransi->umum = Yoga::cleanArrayJson(Input::get('umum'));
		$asuransi->gigi = Yoga::cleanArrayJson(Input::get('gigi'));
		$asuransi->rujukan = Yoga::cleanArrayJson(Input::get('rujukan'));
		$asuransi->penagihan = Yoga::cleanArrayJson(Input::get('penagihan'));

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
				'biaya' => $tarif_pribadi['biaya'], 
				'asuransi_id' => $asuransi_id,
				'jenis_tarif_id' => $tarif_pribadi['jenis_tarif_id'],
				'tipe_tindakan_id' => $tarif_pribadi['tipe_tindakan_id'],
				'bhp_items' => $tarif_pribadi['bhp_items'],
				'jasa_dokter' => $tarif_pribadi['jasa_dokter'],
				'jasa_dokter_tanpa_sip' => $tarif_pribadi['jasa_dokter']
			];
		}



		Tarif::insert($data);
		return \Redirect::route('asuransis.index')->withPesan(Yoga::suksesFlash('<strong>Asuransi ' . ucwords(strtolower(Input::get('nama')))  .'</strong> berhasil dibuat'));
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
		$asuransi = Asuransi::find($id);
		$tarifs = Tarif::where('asuransi_id', $id)->get();
		return view('asuransis.edit', compact(
			'asuransi', 
			'tarifs'
		));
	}

	/**
	 * Update the specified asuransi in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$asuransi = Asuransi::findOrFail($id);


		Yoga::cleanArrayJson(Input::get('umum'));

		$asuransi = Asuransi::find($id);
		$asuransi->alamat = Input::get('alamat');
		$asuransi->tipe_asuransi = Input::get('tipe_asuransi');
		$asuransi->nama = Input::get('nama');
		$asuransi->hp_pic = Input::get('hp_pic');
		$asuransi->no_telp = Input::get('no_telp');
		$asuransi->kali_obat = Input::get('kali_obat');
		$asuransi->email = Input::get('email');
		$asuransi->pic = Input::get('pic');
		$asuransi->gigi = Yoga::cleanArrayJson(Input::get('gigi'));
		$asuransi->umum = Yoga::cleanArrayJson(Input::get('umum'));
		$asuransi->penagihan = Yoga::cleanArrayJson(Input::get('penagihan'));
		$asuransi->rujukan = Yoga::cleanArrayJson(Input::get('rujukan'));
		$asuransi->tanggal_berakhir = Yoga::datePrep(Input::get('tanggal_berakhir'));
		$asuransi->save();


		if ( $id == '32' ) {
			$query = "update stafs set notified=0;";
			DB::statement($query);

		}

		$tarifs = Input::get('tarifs');

		$tarifs = json_decode($tarifs, true);

		foreach ($tarifs as $tarif) {
			$tf = Tarif::find($tarif['id']);
			$tf->biaya = $tarif['biaya'];
			$tf->jasa_dokter = $tarif['jasa_dokter'];
			$tf->tipe_tindakan_id = $tarif['tipe_tindakan_id'];
			$confirm = $tf->save();

			if (!$confirm) {
				return 'update gagal';
			}
		}
		return \Redirect::route('asuransis.index')->withPesan(Yoga::suksesFlash('<strong>Asuransi ' . Input::get('nama') . '</strong> berhasil diubah'));
	}

	/**
	 * Remove the specified asuransi from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{	
		$nama = Asuransi::find($id)->nama;
		Asuransi::find($id)->delete();
		Tarif::where('asuransi_id', $id)->delete();


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
	
	public function hutang(){


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
		return DB::select($query);
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
}
