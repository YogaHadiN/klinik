<?php

namespace App\Http\Controllers;

use Input;
use App\Saldo;
use App\Classes\Yoga;
use App\Console\Commands\scheduleBackup;
use App\Sms;
use Moota;
use Carbon\Carbon;
use App\Http\Controllers\PengeluaransController;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Http\Requests;
use App\Periksa;

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


		$status = 'success';

		if ((strtotime( $zenziva_expired ) - strtotime('now')) < 864000) {
			$status = 'warning';
		} 

		$zenziva_credit_safe = false;
		if( $zenziva_credit < 500 ){
			$status = 'warning';
		}

		$moota_balance_safe = false;
		if( $moota_balance < 20000 ){
			$status = 'warning';
		}

		if ((strtotime( $zenziva_expired ) - strtotime('now')) < 432000) {
			$status = 'danger';
		} 

		$zenziva_credit_safe = false;
		if( $zenziva_credit < 100 ){
			$status = 'danger';
		}

		$moota_balance_safe = false;
		if( $moota_balance < 10000 ){
			$status = 'danger';
		}

		$zenziva_expired = Carbon::parse($zenziva_expired);
		$time_left = strtotime($zenziva_expired) - strtotime('now');
		$time_left = $this->secondsToTime($time_left);
		$saldos          = Saldo::latest()->paginate(20);
		return view('kasirs.saldo', compact(
			'saldos',
			'status',
			'time_left',
			'zenziva_expired',
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


}
