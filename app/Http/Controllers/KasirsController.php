<?php

namespace App\Http\Controllers;

use Input;
use App\Saldo;
use App\Classes\Yoga;
use App\Console\Commands\scheduleBackup;
use App\Sms;
use App\Http\Controllers\PengeluaransController;

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
		$saldos = Saldo::latest()->paginate(20);
		return view('kasirs.saldo', compact('saldos'));
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


}
