<?php

namespace App\Http\Controllers;

use Input;
use App\Saldo;
use App\Classes\Yoga;
use App\Console\Commands\scheduleBackup;
use App\Sms;
use Moota;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\PengeluaransController;
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

		$client          = new Client(); //GuzzleHttp\Client
		$res             = $client->request('GET', 'gsm.zenziva.net/api/balance/?userkey=' . env('ZENZIVA_USERKEY'). '&passkey=' . env('ZENZIVA_PASSKEY'), []);
		$zenziva         = $res->getBody();
		$zenziva         = json_decode( $zenziva, true );
		$zenziva_credit  = $zenziva['credit'];
		$zenziva_expired = $zenziva['expired'];
		$zenziva_array = explode(' ', $zenziva_expired );

		$hari = $zenziva_array[0];
		$bulan = $zenziva_array[1];
		$tahun = $zenziva_array[2];

		$pasien_pertama_belum_dikirim = $this->pasienPertamaBelumDikirim();

		if( strtolower( $bulan ) == 'januari' ){
			$bulan = '01';
		} else if (  strtolower($bulan) == 'februari'  ){
			$bulan = '02';
		} else if (  strtolower($bulan) == 'maret'  ){
			$bulan = '03';
		} else if (  strtolower($bulan) == 'april'  ){
			$bulan = '04';
		} else if (  strtolower($bulan) == 'mei'  ){
			$bulan = '05';
		} else if (  strtolower($bulan) == 'juni'  ){
			$bulan = '06';
		} else if (  strtolower($bulan) == 'juli'  ){
			$bulan = '07';
		} else if (  strtolower($bulan) == 'agustus'  ){
			$bulan = '08';
		} else if (  strtolower($bulan) == 'september'  ){
			$bulan = '09';
		} else if (  strtolower($bulan) == 'oktober'  ){
			$bulan = '10';
		} else if (  strtolower($bulan) == 'november'  ){
			$bulan = '11';
		} else if (  strtolower($bulan) == 'desember'  ){
			$bulan = '12';
		}
		$zenziva_expired      = $tahun . '-' . $bulan . '-' . $hari;
		$zenziva_expired_safe = false;
		$vultr                = $this->vultr();

		$status = 'success';

		//jika pasien admedika yang belum dikirim ada 25 hari yang lalu, maka warning
		//
		if ( $this->countDay( $pasien_pertama_belum_dikirim->tanggal  ) > 20) {
			$status = 'warning';
		} 

		if ((strtotime( $zenziva_expired ) - strtotime('now')) < 864000) {
			$status = 'warning';
		} 

		if( $zenziva_credit < 500 ){
			$status = 'warning';
		}

		if( ($vultr['balance'] + $vultr['pending_charges']) > -20 ){
			$status = 'warning';
		}

		if( $moota_balance < 20000 ){
			$status = 'warning';
		}

		if ((strtotime( $zenziva_expired ) - strtotime('now')) < 432000) {
			$status = 'danger';
		} 

		if( $zenziva_credit < 100 ){
			$status = 'danger';
		}

		if( $moota_balance < 10000 ){
			$status = 'danger';
		}
		if( ($vultr['balance'] + $vultr['pending_charges']) > -15 ){
			$status = 'danger';
		}

		if ( $this->countDay( $pasien_pertama_belum_dikirim->tanggal  ) > 24) {
			$status = 'danger';
		} 


		$zenziva_expired = Carbon::parse($zenziva_expired);
		$time_left       = strtotime($zenziva_expired) - strtotime('now');
		$time_left       = $this->secondsToTime($time_left);
		$saldos          = Saldo::with('staf')->latest()->paginate(20);


		$jarak_hari =$this->countDay( $pasien_pertama_belum_dikirim->tanggal  );

		return view('kasirs.saldo', compact(
			'saldos',
			'status',
			'time_left',
			'pasien_pertama_belum_dikirim',
			'zenziva_expired',
			'jarak_hari',
			'vultr',
			'zenziva_credit',
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
		return DB::select($query)[0];

	}
	private function countDay($date){
		$now = time(); // or your date as well
		$your_date = strtotime($date);
		$datediff = $now - $your_date;
		return round($datediff / (60 * 60 * 24));
	}
	


}
