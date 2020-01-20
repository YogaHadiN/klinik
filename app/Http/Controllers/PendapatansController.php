<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use App\Classes\Yoga;
use App\Http\Requests;
use App\Http\Controllers\AsuransisController;
use App\Pendapatan;
use App\PembayaranBpjs;
use App\PembayaranAsuransi;
use App\PiutangDibayar;
use App\PiutangAsuransi;
use App\NotaJual;
use App\Coa;
use App\Asuransi;
use App\JurnalUmum;
use DB;

class PendapatansController extends Controller
{

	/**
	 * Display a listing of pendapatans
	 *
	 * @return Response
	 */
	public function index()
	{
		$pendapatans = Pendapatan::all();
		return view('pendapatans.index', compact('pendapatans'));
	}

	/**
	 * Show the form for creating a new pendapatan
	 *
	 * @return Response
	 */
	public function create()
	{
		$asuransis          = '';
		foreach(Asuransi::where('id', '>', 0)->get() as $ass){
			if (count( explode(".", $ass->nama ) ) > 1) {
				$text       = explode(".", $ass->nama )[1] ;
			} else {
				$text       = $ass->nama;
			}
			$text           = str_replace(")","",$text);
			$text           = str_replace("(","",$text);
			$text           = trim($text);
			if ($text) {
				$asuransis .= strtolower($text) . ' ';
			}
		}
		$asuransis          = explode(" ", $asuransis);

		$coa_ids = [
			110000 => 'Kas di kasir',
			110001 => 'Kas di Bank Mandiri',
			110003 => 'Kas di Bank BCA'
		];


		$pendapatans        = Pendapatan::with('staf')->latest()->paginate(10);

		return view('pendapatans.create', compact('pendapatans','asuransis', 'coa_ids'));
	}

	/**
	 * Store a newly created pendapatan in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = [

			'sumber_uang' => 'required',
			'nilai'       => 'required',
			'coa_id'       => 'required',
			'staf_id'     => 'required',
			'keterangan'  => 'required',
		];
		$validator = \Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
		//return Input::all();
		$nilai                   = Yoga::clean( Input::get('nilai') );
		$pendapatan              = new Pendapatan;
		$pendapatan->sumber_uang = Input::get('sumber_uang');
		$pendapatan->nilai       = $nilai;
		$pendapatan->keterangan  = Input::get('keterangan');
		$pendapatan->staf_id     = Input::get('staf_id');
		$confirm                 = $pendapatan->save();

		if ($confirm) {
			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $pendapatan->id; // kenapa ini nilainya empty / null padahal di database ada id
			$jurnal->jurnalable_type = 'App\Pendapatan';
			$jurnal->coa_id          = Input::get('coa_id');
			$jurnal->debit           = 1;
			$jurnal->nilai           = $nilai;
			$jurnal->save();

			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $pendapatan->id;
			$jurnal->jurnalable_type = 'App\Pendapatan';
			$jurnal->debit           = 0;
			$jurnal->nilai           = $nilai;
			$jurnal->save();
		}

		return redirect('pendapatans/create')->withPesan(Yoga::suksesFlash('Pendapatan telah berhasil dimasukkan'))
			->withPrint($pendapatan->id);
	}

	/**
	 * Display the specified pendapatan.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$pendapatan = Pendapatan::findOrFail($id);

		return view('pendapatans.show', compact('pendapatan'));
	}

	/**
	 * Show the form for editing the specified pendapatan.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$pendapatan = Pendapatan::find($id);

		return view('pendapatans.edit', compact('pendapatan'));
	}

	/**
	 * Update the specified pendapatan in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$pendapatan = Pendapatan::findOrFail($id);

		$validator = \Validator::make($data = Input::all(), Pendapatan::$rules);

		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}

		$pendapatan->update($data);

		return \Redirect::route('pendapatans.index');
	}

	/**
	 * Remove the specified pendapatan from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Pendapatan::destroy($id);

		return \Redirect::route('pendapatans.index');
	}
    public function pembayaran_asuransi(){
        $asuransi_list = [null => '-pilih-'] + Asuransi::pluck('nama', 'id')->all();
        $pembayarans   = PembayaranAsuransi::with('asuransi', 'coa')->latest()->paginate(10);
		return view('pendapatans.pembayaran_asuransi', compact('asuransi_list', 'pembayarans'));
    }
    public function pembayaran_asuransi_by_id($id){
        return 'asuransi '. $id;
         return 'pembayaran_asuransi';
    }

    public function pembayaran_asuransi_show($id){
        $pembayarans = NotaJual::find($id)->pembayaranAsuransi; 
        return view('pendapatans.pembayaran_show', compact('pembayarans'));

        $asuransi_id = Input::get('asuransi_id');
        $asuransi = Asuransi::find($asuransi_id);
        $mulai = Yoga::nowIfEmptyMulai(Input::get('mulai'));
        $akhir = Yoga::nowIfEmptyMulai(Input::get('akhir'));
        $query = "select px.id as id, ps.nama as nama, asu.nama as nama_asuransi, asu.id as asuransi_id, px.tanggal as tanggal, pa.piutang as piutang, px.piutang_dibayar as piutang_dibayar , px.piutang_dibayar as piutang_dibayar_awal from piutang_asuransis as pa join periksas as px on px.id = pa.periksa_id join pasiens as ps on ps.id = px.pasien_id join asuransis as asu on asu.id=px.asuransi_id where px.asuransi_id='{$asuransi_id}' and px.tanggal between '{$mulai}' and '{$akhir}';";
        $periksas = DB::select($query);
        
		$query = "SELECT px.id as id, p.nama as nama, asu.nama as nama_asuransi, asu.id as asuransi_id, px.tanggal as tanggal, px.piutang as piutang, px.piutang_dibayar as piutang_dibayar , px.piutang_dibayar as piutang_dibayar_awal from periksas as px join pasiens as p on px.pasien_id = p.id join asuransis as asu on asu.id = px.asuransi_id where px.piutang > 0 and px.piutang > px.piutang_dibayar and px.asuransi_id = '{$id}';";
		$periksas = DB::select($query);



		return view('pendapatans.pembayaran_show', compact(
			'asuransi', 
			'periksas', 
			'mulai', 
			'akhir'
		));
    }
	public function belumDibayar($mulai, $akhir, $id){
		
		$query = "SELECT pu.id as piutang_id, ";
		$query .= "px.id as periksa_id, ";
		$query .= "ps.nama as nama_pasien, ";
		$query .= "pu.tunai as tunai, ";
		$query .= "pu.piutang as piutang, ";
		$query .= "pd.pembayaran_asuransi_id as pembayaran_asuransi_id, ";
		$query .= "pu.sudah_dibayar as pembayaran, ";
		$query .= "sum(pd.pembayaran) as total_pembayaran, ";
		$query .= "0 as akan_dibayar, ";
		$query .= "pu.created_at as tanggal, ";
		$query .= "sudah_dibayar ";
		$query .= "FROM piutang_asuransis as pu ";
		$query .= "join periksas as px on px.id=pu.periksa_id ";
		$query .= "left join piutang_dibayars as pd on pd.periksa_id=px.id ";
		$query .= "join pasiens as ps on ps.id = px.pasien_id ";
		$query .= "where date(px.tanggal) between '{$mulai}' and '{$akhir}' ";
		$query .= "and px.asuransi_id = '{$id}' ";
		$query .= "and pu.sudah_dibayar < pu.piutang ";
		$query .= "group by pu.id;";

        return DB::select($query);
	}
	

    public function lihat_pembayaran_asuransi(){
        $asuransi_id = Input::get('asuransi_id');


		$total_sudah_dibayar = 0;
		$total_belum_dibayar = 0;

        $mulai       = Yoga::datePrep( Input::get('mulai') );
        $akhir       = Yoga::datePrep( Input::get('akhir') );

        $kasList     = [ null => '-Pilih-' ] + Coa::where('id', 'like', '110%')->pluck('coa', 'id')->all();
        $pembayarans = $this->belumDibayar($mulai, $akhir, $asuransi_id);

		/* return $pembayarans; */


        foreach ($pembayarans as $k => $pemb) {
            if ($pemb->pembayaran == null) {
                $pembayarans[$k]->pembayaran = 0;
            }
			$total_belum_dibayar += $pemb->piutang - $pemb->sudah_dibayar;
        }

		/* return $total_belum_dibayar; */

        $asuransi              = Asuransi::find($asuransi_id);
		$PendapatansController = new PendapatansController;
		$asuransis             = new AsuransisController;
		$hutangs               = $asuransis->hutangs_template($asuransi_id);
		$pembayarans_template  = $asuransis->pembayarans_template($asuransi_id);

        $sudah_dibayars = $this->sudahDibayar( $mulai, $akhir, $asuransi_id );


		foreach ($sudah_dibayars as $sb) {
			$total_sudah_dibayar += $sb->pembayaran;
		}


		return view('pendapatans.pembayaran_show', compact(
			'pembayarans', 
			'total_sudah_dibayar', 
			'total_belum_dibayar', 
			'asuransi', 
			'sudah_dibayars', 
			'mulai', 
			'akhir', 
			'asuransi_id', 
			'hutangs', 
			'pembayarans_template', 
			'kasList'
		));
    }
	public function sudahDibayar( $mulai, $akhir, $asuransi_id ){
		
		$query = "SELECT pu.id as piutang_id, ";
		$query .= "pd.id as piutang_dibayar_id, ";
		$query .= "px.id as periksa_id, ";
		$query .= "ps.nama as nama_pasien, ";
		$query .= "pu.tunai as tunai, ";
		$query .= "pu.piutang as piutang, ";
		$query .= "pu.sudah_dibayar as pembayaran, ";
		$query .= "pu.sudah_dibayar as sudah_dibayar, ";
		$query .= "0 as akan_dibayar, ";
		$query .= "px.created_at as tanggal ";
		$query .= "FROM piutang_asuransis as pu ";
		$query .= "join periksas as px on px.id=pu.periksa_id ";
		$query .= "join pasiens as ps on ps.id = px.pasien_id ";
		$query .= "join piutang_dibayars as pd on pd.periksa_id = px.id ";
		$query .= "where date(px.tanggal) between '{$mulai}' and '{$akhir}' ";
		$query .= "and px.asuransi_id = '{$asuransi_id}' ";
		$query .= "and pu.sudah_dibayar >= pu.piutang;";

        return DB::select($query);
	}
	
    public function asuransi_bayar(){
		/* return Input::all(); */ 
		DB::beginTransaction();
		try {
			$rules = [
				 'tanggal_dibayar' => 'date|required',
				 'mulai'           => 'date|required',
				 'akhir'           => 'date|required',
				 'staf_id'         => 'required',
				 'asuransi_id'     => 'required',
				 'coa_id'          => 'required'
			];

			$validator = \Validator::make(Input::all(), $rules);

			if ($validator->fails())
			{
				return \Redirect::back()->withErrors($validator)->withInput();
			}

			$dibayar     = Yoga::clean( Input::get('dibayar') );
			$mulai       = Input::get('mulai');
			$staf_id     = Input::get('staf_id');
			$akhir       = Input::get('akhir');
			$tanggal     = Yoga::datePrep( Input::get('tanggal_dibayar') );
			$asuransi_id = Input::get('asuransi_id');
			$temp        = Input::get('temp');
			$coa_id      = Input::get('coa_id');
			
			
			$temp = json_decode($temp, true);

			// add table nota_jual

			$nota_jual_id     = Yoga::customId('App\NotaJual');
			$nj               = new NotaJual;
			$nj->id           = $nota_jual_id;
			$nj->tipe_jual_id = 2;
			$nj->tanggal      = $tanggal;
			$nj->staf_id      = $staf_id;
			$nj->save();

			//create pembayaran_asuransis
			
			$pb                  = new PembayaranAsuransi;
			$pb->asuransi_id     = $asuransi_id;
			$pb->mulai           = Yoga::datePrep( $mulai );
			$pb->staf_id         = $staf_id;
			$pb->nota_jual_id    = $nota_jual_id;
			$pb->akhir           = Yoga::datePrep($akhir);
			$pb->pembayaran      = $dibayar;
			$pb->tanggal_dibayar = $tanggal;
			$pb->kas_coa_id      = $coa_id;
			$confirm             = $pb->save();

			$coa_id_asuransi = Asuransi::find($asuransi_id)->coa_id;// Piutang Asuransi



			// insert jurnal_umums
			if ($confirm) {
				$jurnals = [];
				$jurnals[] = [
					'jurnalable_id'   => $nota_jual_id,
					'jurnalable_type' => 'App\NotaJual',
					'coa_id'          => $coa_id, //coa_kas_di_bank_mandiri = 110001;
					'debit'           => 1,
					'nilai'           => $dibayar,
					'created_at'      => date('Y-m-d H:i:s'),
					'updated_at'      => date('Y-m-d H:i:s')
				];

				$jurnals[] = [
					'jurnalable_id'   => $nota_jual_id,
					'jurnalable_type' => 'App\NotaJual',
					'coa_id'          => $coa_id_asuransi,
					'debit'           => 0,
					'nilai'           => $dibayar,
					'created_at'      => date('Y-m-d H:i:s'),
					'updated_at'      => date('Y-m-d H:i:s')
				];
				JurnalUmum::insert($jurnals);
			}
			$bayars = [];
			foreach ($temp as $tmp) {
				if ($tmp['akan_dibayar'] > 0) {
					//update piutang_asuransis
					$pt                = PiutangAsuransi::find($tmp['piutang_id']);
					$pt->sudah_dibayar = $pt->sudah_dibayar + $tmp['akan_dibayar'];
					if ($pt->save()) {
						$bayars[] = [
							'periksa_id'             => $tmp['periksa_id'],
							'pembayaran'             => $tmp['akan_dibayar'],
							'pembayaran_asuransi_id' => $pb->id,
							'created_at'             => date('Y-m-d H:i:s'),
							'updated_at'             => date('Y-m-d H:i:s')
						];
					}
				}
			}
			//piutang_dibayars insert
			PiutangDibayar::insert($bayars);
			$pesan = Yoga::suksesFlash('Asuransi <strong>' . Asuransi::find($asuransi_id)->nama . '</strong> tanggal <strong>' . Yoga::updateDatePrep($mulai). '</strong> sampai dengan <strong>' . Yoga::updateDatePrep($akhir) . ' BERHASIL</strong> dibayarkan sebesar <strong><span class="uang">' . $dibayar . '</span></strong>');
			DB::commit();
			if ($coa_id == '110000') {
				return redirect('pendapatans/pembayaran/asuransi')->withPesan($pesan)->withPrint($pb->id);
			} else {
				return redirect('pendapatans/pembayaran/asuransi')->withPesan($pesan);
			}
		} catch (\Exception $e) {
			DB::rollback();
			throw $e;
		}
    }
	public function pembayaran_bpjs(){
		$bpjs = PembayaranBpjs::orderBy('tanggal_pembayaran', 'desc')->get();
		return view('pembayaran_bpjs.index', compact( 'bpjs' ));
	}
	public function pembayaran_bpjs_post(){
		$nilai = Yoga::clean( Input::get('nilai') );
		$staf_id = Input::get('staf_id');
		$tanggal_pembayaran = Yoga::datePrep( Input::get('tanggal_pembayaran') );
		$periode_bulan = Yoga::blnPrep( Input::get('periode_bulan') );
		$hari_terakhir_bulan = date('Y-m-t 23:59:59', strtotime($periode_bulan . '-01'));

		$bpjs = new PembayaranBpjs;
		$bpjs->staf_id = Input::get('staf_id');
		$bpjs->nilai = $nilai;
		$bpjs->mulai_tanggal = $periode_bulan . '-01 00:00:00';
		$bpjs->akhir_tanggal = $hari_terakhir_bulan;
		$bpjs->tanggal_pembayaran = $tanggal_pembayaran;
		$confirm = $bpjs->save();

		if ($confirm) {
			
			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $bpjs->id; // kenapa ini nilainya empty / null padahal di database ada id
			$jurnal->jurnalable_type = 'App\PembayaranBpjs';
			$jurnal->coa_id          = 110004;
			$jurnal->debit           = 1;
			$jurnal->created_at      = $hari_terakhir_bulan;
			$jurnal->updated_at      = $hari_terakhir_bulan;
			$jurnal->nilai           = $nilai;
			$jurnal->save();

			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $bpjs->id;
			$jurnal->jurnalable_type = 'App\PembayaranBpjs';
			$jurnal->coa_id          =  400045 ;// pendapatan kapitasi bpjs
			$jurnal->debit           = 0;
			$jurnal->created_at      = $hari_terakhir_bulan;
			$jurnal->updated_at      = $hari_terakhir_bulan;
			$jurnal->nilai           = $nilai;
			$jurnal->save();

		}
		$pesan = Yoga::suksesFlash('Input pembayaran kapitasi bpjs bulan ' . $periode_bulan . ' telah berhasil');
		return redirect()->back()->withPesan($pesan);
	}
	
}