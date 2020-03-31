<?php

namespace App\Http\Controllers;

use Input;
use App\Classes\Yoga;
use App\Http\Requests;
use App\Http\Controllers\AsuransisController;
use App\Pendapatan;
use App\PembayaranBpjs;
use App\Invoice;
use App\Rekening;
use App\PembayaranAsuransi;
use App\PiutangDibayar;
use App\PiutangAsuransi;
use App\NotaJual;
use App\Imports\PembayaranImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Coa;
use App\Asuransi;
use App\JurnalUmum;
use App\CatatanAsuransi;
use DB;
use Carbon\Carbon;

class PendapatansController extends Controller
{
	public $input_dibayar;
	public $input_mulai;
	public $input_staf_id;
	public $input_akhir;
	public $input_tanggal_dibayar;
	public $input_asuransi_id;
	public $input_temp;
	public $input_coa_id;
	public $input_catatan_container;
	public $input_rekening_id;
	public $input_invoice_id;
	public $input_id;
	public $input_created_at;
	public $input_nama_asuransi;
	public $input_periode;
	public $input_pembayaran;
	public $input_tanggal_pembayaran;
	public $input_tujuan_kas;
	public $input_displayed_rows;
	public $input_pass;
	public $input_key;

	/**
	 * Display a listing of pendapatans
	 *
	 * @return Response
	 */
	public function __construct(){

		$this->input_dibayar           = Yoga::clean(Input::get('dibayar'));
		$this->input_mulai             = Input::get('mulai');
		$this->input_staf_id           = Input::get('staf_id');
		$this->input_akhir             = Input::get('akhir');
		$this->input_tanggal_dibayar   = Input::get('tanggal_dibayar');
		$this->input_asuransi_id       = Input::get('asuransi_id');
		$this->input_temp              = Input::get('temp');
		$this->input_coa_id            = Input::get('coa_id');
		$this->input_catatan_container = Input::get('catatan_container');
		$this->input_rekening_id       = Input::get('rekening_id');
		$this->input_invoice_id        = Input::get('invoice_id');
		$this->input_key               = Input::get('key');

		$this->input_id                 = Input::get('id'). '%';
		$this->input_created_at         = Input::get('created_at'). '%';
		$this->input_nama_asuransi      = '%' .Input::get('nama_asuransi'). '%';
		$this->input_periode            = Input::get('periode'). '%';
		$this->input_pembayaran         = Input::get('pembayaran'). '%';
		$this->input_tanggal_pembayaran = Input::get('tanggal_pembayaran'). '%';
		$this->input_tujuan_kas         = $this->strSplit(Input::get('tujuan_kas'));
		$this->input_displayed_rows     = Input::get('displayed_rows');
		$this->input_pass               = $this->input_key * $this->input_displayed_rows;

	}
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
		return $this->pembayaran_asuransi_template();
    }
    public function pembayaran_asuransi_by_id($id){
        return 'asuransi '. $id;
         return 'pembayaran_asuransi';
    }

    public function pembayaran_asuransi_show($id){
        $pembayarans = NotaJual::find($id)->pembayaranAsuransi; 
        return view('pendapatans.pembayaran_show', compact('pembayarans'));

        $asuransi_id  = Input::get('asuransi_id');
        $asuransi     = Asuransi::find($asuransi_id);
        $mulai        = Yoga::nowIfEmptyMulai(Input::get('mulai'));
        $akhir        = Yoga::nowIfEmptyAkhir(Input::get('akhir'));
		$query        = "select ";
		$query       .= "px.id as id, ";
		$query       .= "ps.nama as nama, ";
		$query       .= "asu.nama as nama_asuransi, ";
		$query       .= "asu.id as asuransi_id, ";
		$query       .= "px.tanggal as tanggal, ";
		$query       .= "pa.piutang as piutang, ";
		$query       .= "px.piutang_dibayar as piutang_dibayar , ";
		$query       .= "px.piutang_dibayar as piutang_dibayar_awal ";
		$query       .= "from piutang_asuransis as pa ";
		$query       .= "join periksas as px on px.id = pa.periksa_id ";
		$query       .= "join pasiens as ps on ps.id = px.pasien_id ";
		$query       .= "join asuransis as asu on asu.id=px.asuransi_id ";
		$query       .= "where px.asuransi_id='{$asuransi_id}' ";
		$query       .= "and px.tanggal between '{$mulai}' and '{$akhir}';";
        $periksas     = DB::select($query);
        
		$query     = "SELECT ";
		$query    .= "px.id as id, ";
		$query    .= "p.nama as nama, asu.nama as nama_asuransi,";
		$query    .= " asu.id as asuransi_id, ";
		$query    .= "px.tanggal as tanggal, ";
		$query    .= "px.piutang as piutang, ";
		$query    .= "px.piutang_dibayar as piutang_dibayar , ";
		$query    .= "px.piutang_dibayar as piutang_dibayar_awal ";
		$query    .= "from periksas as px ";
		$query    .= "join pasiens as p on px.pasien_id = p.id ";
		$query    .= "join asuransis as asu on asu.id = px.asuransi_id ";
		$query    .= "where px.piutang > 0 ";
		$query    .= "and px.piutang > px.piutang_dibayar ";
		$query    .= "and px.asuransi_id = '{$id}';";
		$periksas  = DB::select($query);

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
		$query .= "px.pasien_id as pasien_id, ";
		$query .= "ps.nama as nama_pasien, ";
		$query .= "pu.tunai as tunai, ";
		$query .= "pu.piutang as piutang, ";
		$query .= "pd.pembayaran_asuransi_id as pembayaran_asuransi_id, ";
		$query .= "pu.sudah_dibayar as pembayaran, ";
		$query .= "sum(pd.pembayaran) as total_pembayaran, ";
		$query .= "0 as akan_dibayar, ";
		$query .= "px.tanggal as tanggal, ";
		$query .= "sudah_dibayar ";
		$query .= "FROM piutang_asuransis as pu ";
		$query .= "join periksas as px on px.id=pu.periksa_id ";
		$query .= "left join piutang_dibayars as pd on pd.periksa_id=px.id ";
		$query .= "join pasiens as ps on ps.id = px.pasien_id ";
		$query .= "where date(px.tanggal) between '{$mulai} 00:00:00' and '{$akhir} 23:59:59' ";
		$query .= "and px.asuransi_id = '{$id}' ";
		/* $query .= "and pu.sudah_dibayar < pu.piutang "; */
		$query .= "group by pu.id ";
		$query .= "order by px.tanggal;";

        $result = DB::select($query);
		return $result;
	}
	

    public function lihat_pembayaran_asuransi(){
		return $this->lihat_pembayaran_asuransi_template();
    }
	public function sudahDibayar( $mulai, $akhir, $asuransi_id ){
		
		$query = "SELECT pu.id as piutang_id, ";
		$query .= "pd.id as piutang_dibayar_id, ";
		$query .= "px.id as periksa_id, ";
		$query .= "ps.nama as nama_pasien, ";
		$query .= "ps.id as pasien_id, ";
		$query .= "pu.tunai as tunai, ";
		$query .= "pu.piutang as piutang, ";
		$query .= "pu.sudah_dibayar as pembayaran, ";
		$query .= "pu.sudah_dibayar as sudah_dibayar, ";
		$query .= "0 as akan_dibayar, ";
		$query .= "px.tanggal as tanggal ";
		$query .= "FROM piutang_asuransis as pu ";
		$query .= "join periksas as px on px.id=pu.periksa_id ";
		$query .= "join pasiens as ps on ps.id = px.pasien_id ";
		$query .= "join piutang_dibayars as pd on pd.periksa_id = px.id ";
		$query .= "where date(px.tanggal) between '{$mulai} 00:00:00' and '{$akhir} 23:59:59' ";
		$query .= "and px.asuransi_id = '{$asuransi_id}' ";
		$query .= "and pu.sudah_dibayar >= pu.piutang ";
		$query .= "order by px.tanggal;";
        return DB::select($query);
	}
	
    public function asuransi_bayar(){
		/* dd(Input::all()); */ 
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

			$data = $this->inputData();
			$pesan = Yoga::suksesFlash('Asuransi <strong>' . $data['asuransi']->nama . '</strong> tanggal <strong>' . Yoga::updateDatePrep($data['mulai']). '</strong> sampai dengan <strong>' . Yoga::updateDatePrep($data['akhir']) . ' BERHASIL</strong> dibayarkan sebesar <strong><span class="uang">' . $data['dibayar'] . '</span></strong>');
			DB::commit();
			if ($data['coa_id'] == '110000') {
				return redirect('pendapatans/pembayaran/asuransi')->withPesan($pesan)->withPrint($data['pb']->id);
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
	public function pembayaran_asuransi_rekening($id){
		return $this->pembayaran_asuransi_template($id);
	}
	private function pembayaran_asuransi_template($id = null){
        $asuransi_list = [null => '-pilih-'] + Asuransi::pluck('nama', 'id')->all();
        $pembayarans   = PembayaranAsuransi::with('asuransi', 'coa')->latest()->paginate(10);
		if ($id) {
			return view('pendapatans.pembayaran_asuransi', compact('asuransi_list', 'pembayarans', 'id'));
		} else {
			return view('pendapatans.pembayaran_asuransi', compact('asuransi_list', 'pembayarans'));
		}
	}
	public function lihat_pembayaran_asuransi_by_rekening($id){
		return $this->lihat_pembayaran_asuransi_template($id);
	}
	private function lihat_pembayaran_asuransi_template($id = null){
        $asuransi_id = Input::get('asuransi_id');

		$invoices = $this->invoicesQuery($asuransi_id);
		/* $option_invoices = [ null => '-Pilih-' ]; */

		if(count($invoices)){
			foreach ($invoices as $inv) {
				$option_invoices[$inv->invoice_id] = $inv->invoice_id;
			}
		} else {
			$option_invoices = [];
		}

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

		$excel_pembayaran = [];

		if (Input::hasFile('excel_pembayaran')) {
			$file =Input::file('excel_pembayaran'); //GET FILE
			$excel_pembayaran = Excel::toArray(new PembayaranImport, $file)[0];
		}  

		$excel_pembayaran = json_encode($excel_pembayaran);
		/* dd($excel_pembayaran); */

		foreach ($sudah_dibayars as $sb) {
			$total_sudah_dibayar += $sb->pembayaran;
		}

		$arus_kas_tujuan = null;
		$tanggal_dibayar = null;

		if (isset($id)) {
			$rek = Rekening::find( $id );
			$tanggal_dibayar = Carbon::CreateFromFormat('Y-m-d H:i:s',$rek->tanggal)->format('d-m-Y');
			$arus_kas_tujuan = 110001;
		}

		/* dd($excel_pembayaran); */

		$param = compact( 
			'pembayarans', 
			'total_sudah_dibayar', 
			'excel_pembayaran', 
			'arus_kas_tujuan', 
			'tanggal_dibayar', 
			'total_belum_dibayar', 
			'asuransi', 
			'sudah_dibayars', 
			'option_invoices', 
			'mulai', 
			'akhir', 
			'asuransi_id', 
			'hutangs', 
			'pembayarans_template', 
			'kasList'
		);
		if ( isset($id) ) {
			$param['id'] = $id;
		} 
		return view('pendapatans.pembayaran_show', $param);
	}
	public function detailPA(){
		$id       = Input::get('id');
		$id = json_decode($id, true);
		$result   = [];
		if (count($id)) {
			$invoices = Invoice::with('piutang_asuransi.periksa.asuransi')->whereIn('id', $id )->get();
			foreach ($invoices as $invoice) {
				$result[] = $invoice->detail_invoice;
			}
		}
		return $result;
	}
	public function invoicesQuery($asuransi_id, $nilai = false){
		$query  = "SELECT ";
		$query .= "inv.id as invoice_id, ";
		if ($nilai) {
			$query .= "sum(pa.piutang - pa.sudah_dibayar) as total_tagihan ";
		} else {
			$query .= "count(pa.piutang - pa.sudah_dibayar) as total_tagihan ";
		}
		$query .= "FROM invoices as inv ";
		$query .= "JOIN piutang_asuransis as pa on pa.invoice_id = inv.id ";
		$query .= "JOIN periksas as px on px.id = pa.periksa_id ";
		$query .= "WHERE px.asuransi_id = '{$asuransi_id}' ";
		$query .= "AND inv.pembayaran_asuransi_id is null ";
		$query .= "GROUP BY inv.id";
		if ($nilai) {
			$query .= " HAVING sum(pa.piutang - pa.sudah_dibayar) = {$nilai} LIMIT 1;";
		} else {
			$query .= ";";
		}
		return DB::select($query);
	}
	public function inputData(){
		

			$dibayar           = $this->input_dibayar;
			$mulai             = $this->input_mulai;
			$staf_id           = $this->input_staf_id;
			$akhir             = $this->input_akhir;
			$tanggal           = Yoga::datePrep( $this->input_tanggal_dibayar );
			$asuransi_id       = $this->input_asuransi_id;
			$temp              = $this->input_temp;
			$coa_id            = $this->input_coa_id;
			$catatan_container = $this->input_catatan_container;
			$catatan_container = json_decode($catatan_container, true) ;
			
			
			$temp = json_decode($temp, true);

			// create nota_jual

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
			$pb->mulai           = $mulai;
			$pb->staf_id         = $staf_id;
			$pb->nota_jual_id    = $nota_jual_id;
			$pb->akhir           = $akhir;
			$pb->pembayaran      = $dibayar;
			$pb->tanggal_dibayar = $tanggal;
			$pb->kas_coa_id      = $coa_id;
			$confirm             = $pb->save();


			//update rekening
			try {
				$rekening                         = Rekening::findOrFail( $this->input_rekening_id );
				$rekening->pembayaran_asuransi_id = $pb->id;
				$rekening->save();
			} catch (\Exception $e) {
				
			}
			//
			//update invoices
			$invoice_ids = $this->input_invoice_id;
			$invoices    = Invoice::whereIn('id', $invoice_ids)->get();
			foreach ($invoices as $inv) {
				if (isset($inv)) {
					$inv->pembayaran_asuransi_id = $pb->id;
					$inv->save();
				}
			}
			$asuransi        = Asuransi::find($asuransi_id);
			$coa_id_asuransi = $asuransi->coa_id;
			//
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
				if (
					$tmp['akan_dibayar'] > 0 &&
					$tmp['piutang'] > $tmp['pembayaran']
				) {
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
			$catatans= [];
			PiutangDibayar::insert($bayars);
			return [
				'asuransi' => $asuransi,
				'mulai'    => $mulai,
				'akhir'    => $akhir,
				'coa_id'   => $coa_id,
				'pb'       => $pb,
				'dibayar'  => $dibayar
			];
	}
	public function cariPembayaran(){

		$data  = $this->queryData();
		$count = $this->queryData(true);
		$pages = ceil( $count/ $this->input_displayed_rows );

		return [
			'data'  => $data,
			'pages' => $pages,
			'key'   => $this->input_key,
			'rows'  => $count
		];
	}
	private function strSplit($word){
		$arr_word = str_split($word);
		$result = '%';
		foreach ($arr_word as $w) {
			$result .= $w .'%';
		}

		return $result;
	}
	private function queryData(
		$count = false
	){
		$query  = "SELECT ";
		if (!$count) {
			$query .= "pa.id, ";
			$query .= "DATE_FORMAT( pa.created_at, '%Y-%m-%d') as created_at, ";
			$query .= "asu.nama as nama_asuransi, ";
			$query .= "concat(DATE_FORMAT( pa.mulai, '%Y-%m-%d'), ' s/d ', DATE_FORMAT( pa.akhir, '%Y-%m-%d')) as periode, ";
			$query .= "pa.pembayaran as pembayaran, ";
			$query .= "pa.tanggal_dibayar as tanggal_pembayaran, ";
			$query .= "co.coa as tujuan_kas ";
		} else {
			$query .= "count(pa.id) as jumlah ";
		}
		$query .= "FROM pembayaran_asuransis as pa ";
		$query .= "JOIN asuransis as asu on asu.id = pa.asuransi_id ";
		$query .= "JOIN coas as co on co.id = pa.kas_coa_id ";
		$query .= "WHERE pa.id like '{$this->input_id}' ";
		$query .= "AND pa.created_at like '{$this->input_created_at}' ";
		$query .= "AND asu.nama like '{$this->input_nama_asuransi}' ";
		$query .= "AND concat(DATE_FORMAT( pa.mulai, '%Y-%m-%d'), ' s/d ', DATE_FORMAT( pa.akhir, '%Y-%m-%d')) like '{$this->input_periode}' ";
		$query .= "AND pa.pembayaran like '{$this->input_pembayaran}' ";
		$query .= "AND pa.tanggal_dibayar like '{$this->input_tanggal_pembayaran}' ";
		$query .= "AND co.coa like '{$this->input_tujuan_kas}' ";
		$query .= "ORDER BY pa.id desc ";
		/* dd($query); */
		if (!$count) {
			$query .= "LIMIT {$this->input_pass}, {$this->input_displayed_rows};";
		}
		if (!$count) {
			return DB::select($query);
		} else {
			return DB::select($query)[0]->jumlah;
		}
	}
	
	
	
}
