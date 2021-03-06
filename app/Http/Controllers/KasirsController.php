<?php

namespace App\Http\Controllers;

use Input;
use App\Saldo;
use App\PesertaBpjsPerbulan;
use App\Classes\Yoga;
use App\Console\Commands\scheduleBackup;
use App\Sms;
use Moota;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\PengeluaransController;
use App\Http\Controllers\WablasController;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Http\Requests;
use App\Periksa;
use Vultr\VultrClient;
use Vultr\Adapter\GuzzleHttpAdapter;

class KasirsController extends Controller
{

	/**
	 * Display a listing of the resource.
	 * GET /kasirs
	 *
	 * @return Response
	 */
	public function index()
	{
		// return 'kasir koncto';

		$antriansurveys = Periksa::where('lewat_kasir', '1')->where('lewat_poli', '1')->where('lewat_kasir2', '0')->get();

		return view('surveys.index', compact('antriansurveys'));

	}

	public function saldo(){
		$moota_balance = Moota::balance()['balance'];

		$wablas     = new WablasController;
		$infoWablas = $wablas->infoWablas();

		$quota      = $infoWablas['quota'];
		$expired    = $infoWablas['expired'];

		$pasien_pertama_belum_dikirim = $this->pasienPertamaBelumDikirim();

		/* dd($pasien_pertama_belum_dikirim); */
		$vultr                = $this->vultr();

		$status = 'success';

		$admedikaWarning = 'primary';
		//jika pasien admedika yang belum dikirim ada 25 hari yang lalu, maka warning
		//
		if ( $this->countDay( $pasien_pertama_belum_dikirim  ) > 20) {
			$status          = 'warning';
			$admedikaWarning = 'warning';
		} 

		//
		//jika balance vultr kurang dari 20 maka warning
		//
		//
		$vultrWarning = 'primary';
		if( ($vultr['balance'] + $vultr['pending_charges']) > -20 ){
			$status = 'warning';
			$vultrWarning = 'warning';
		}

		//
		//jika balance moota kurang dari 20000 maka warning
		//
		$mootaWarning = 'primary';
		if( $moota_balance < 20000 ){
			$status = 'warning';
			$mootaWarning = 'warning';
		}

		//
		//jika sudah tanggal 6 dan belum diupload daftar peserta bpjs bulan itu maka warning
		//
		//

		$statusBpjsPerBulan   = 'primary';
		$peserta_bpjs_perbulan = PesertaBpjsPerbulan::where('created_at', 'like', date('Y-m') . '%')->first();

		if ( date('d') > 6 && is_null( $peserta_bpjs_perbulan ) ) {
			$statusBpjsPerBulan = 'warning';
			$status             = 'warning';
		}

		$wablasWarning        = 'primary';
		if( $quota < 1000 ){
			$status = 'warning';
			$wablasWarning = 'warning';
		}

		if( Yoga::dateDiffNow($expired) < 10 ){
			$status = 'warning';
			$wablasWarning = 'warning';
		}

		if( $moota_balance < 10000 ){
			$status = 'danger';
			$mootaWarning = 'danger';
		}
		if( ($vultr['balance'] + $vultr['pending_charges']) > -15 ){
			$status       = 'danger';
			$vultrWarning = 'danger';
		}

		if ( $this->countDay( $pasien_pertama_belum_dikirim  ) > 24) {
			$status          = 'danger';
			$admedikaWarning = 'danger';
		} 

		if( $quota < 500 ){
			$status        = 'danger';
			$wablasWarning = 'danger';
		}

		if( Yoga::dateDiffNow($expired) < 3 ){
			$status        = 'danger';
			$wablasWarning = 'danger';
		}

		
		//
		//jika sudah diatas tanggal 11 dan belum diupload daftar peserta bpjs bulan itu maka fail
		//
		//
		if ( date('d') >10 && is_null( $peserta_bpjs_perbulan ) ) {
			$statusBpjsPerBulan = 'danger';
			$status             = 'danger';
		}

		$saldos          = Saldo::with('staf')->latest()->paginate(20);

		$jarak_hari =$this->countDay( $pasien_pertama_belum_dikirim  );


		return view('kasirs.saldo', compact(
			'saldos',
			'admedikaWarning',
			'statusBpjsPerBulan',
			'vultrWarning',
			'wablasWarning',
			'mootaWarning',
			'quota',
			'expired',
			'status',
			'pasien_pertama_belum_dikirim',
			'jarak_hari',
			'vultr',
			'moota_balance'
		));
	}
	
	public function saldoPost(){
		$rules = [
			'saldo'   => 'required',
			'staf_id' => 'required',
		];
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
		$saldo = Yoga::clean( Input::get('saldo') );
		$saldo_saat_ini = 0;
		$selisih = 0;

		$checkout = new PengeluaransController;
		$saldo_saat_ini = $checkout->parameterKasir()['uang_di_kasir'];

		$selisih = $saldo - $saldo_saat_ini;

		$sl                 = new Saldo;
		$sl->saldo          = $saldo;
		$sl->saldo_saat_ini = $saldo_saat_ini;
		$sl->selisih        = $selisih;
		$sl->staf_id        = Input::get('staf_id');
		$confirm            = $sl->save();

		//backup database
		$kernel = new scheduleBackup;
		$kernel->handle();

		if ($selisih > 0) {
			$pesanSms = 'Ada kelebihan uang di kasir sebesar ' . Yoga::buatrp($selisih). 'saldo di kasir sebesar ' . Yoga::buatrp($saldo_saat_ini);
			Sms::send(env("NO_HP_OWNER"),  $pesanSms );
			Sms::send(env("NO_HP_OWNER2"), $pesanSms  );
		} else if( $selisih < 0 ){
			$pesanSms = 'Ada kekurangan uang di kasir sebesar ' . Yoga::buatrp($selisih). 'saldo di kasir sebesar ' . Yoga::buatrp($saldo_saat_ini);
			Sms::send(env("NO_HP_OWNER"), $pesanSms );
			Sms::send(env("NO_HP_OWNER2"), $pesanSms);
		}

		if ($confirm) {
			$pesan = Yoga::suksesFlash('Penghitungan Saldo <strong>BERHASIL</strong> dilakukan');
		} else {
			$pesan = Yoga::gagalFlash('Penghitungan Saldo <strong>GAGAL</strong> dilakukan');
		}
		return redirect()->back()->withPesan($pesan);
	}
	private function secondsToTime($seconds) {
		$dtF = new \DateTime('@0');
		$dtT = new \DateTime("@$seconds");
		return $dtF->diff($dtT)->format('%a hari lagi');
	}
	private	function vultr(){
		$client = new VultrClient(
			new GuzzleHttpAdapter(env('VULTR_KEY'))
		);
		$result = $client->metaData()->getAccountInfo();
		return $result;
	}
	public function pasienPertamaBelumDikirim(){
		
		$query  = "SELECT ";
		$query .= "px.tanggal, ";
		$query .= "px.jam, ";
		$query .= "asu.nama as nama_asuransi, ";
		$query .= "ps.nama as nama_pasien ";
		$query .= "FROM piutang_asuransis as pa ";
		$query .= "JOIN periksas as px on px.id = pa.periksa_id ";
		$query .= "JOIN pasiens as ps on ps.id = px.pasien_id ";
		$query .= "JOIN asuransis as asu on asu.id = px.asuransi_id ";
		$query .= "JOIN tipe_asuransis as ta on ta.id = asu.tipe_asuransi ";
		$query .= "WHERE invoice_id is null ";
		$query .= "AND px.tanggal > '2020-02-01 00:00:00' ";
		$query .= "AND ta.id = 3 "; // tipe asuransi admedika
		$query .= "ORDER BY px.tanggal asc ";
		$query .= "LIMIT 1;";

		$pasienBelumDikirim = DB::select($query);
		if ( count( $pasienBelumDikirim )  ) {
			return $pasienBelumDikirim[0];
		}

		return null;

	}
	private function countDay($pasienBelumDikirim){
		if ( is_null($pasienBelumDikirim) ) {
			return 0;
		}
		$now = time(); // or your date as well
		$your_date = strtotime($pasienBelumDikirim->tanggal);
		$datediff = $now - $your_date;
		return round($datediff / (60 * 60 * 24));
	}
	


}
