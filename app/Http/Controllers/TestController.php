<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;

use App\Http\Requests;

use DB;
use App\Asuransi;
use App\CheckoutKasir;
use App\BayarGaji;
use App\Pasien;
use App\User;
use App\Staf;
use App\Rak;
use App\JurnalUmum;
use App\TransaksiPeriksa;
use App\Terapi;
use App\Dispensing;
use App\Rujukan;
use App\SuratSakit;
use App\RegisterAnc;
use App\Usg;
use App\GambarPeriksa;
use App\Periksa;
use App\Merek;
use App\BukanPeserta;
use App\Formula;
use App\Komposisi;
use App\Classes\Yoga;
use App\Http\Handler;
use App\Console\Commands\sendMeLaravelLog;


class TestController extends Controller
{

	public function index(){
		test;
		/* $data = []; */
		/* $data[] = env('NAMA_KLINIK'); */
		/* $data[] = env('ALAMAT_KLINIK'); */
		/* $data[] = env('ALAMAT_KLINIK_LINE1'); */
		/* $data[] = env('ALAMAT_KLINIK_LINE2'); */
		/* $data[] = env('TELPON_KLINIK'); */

		/* $data[] = env('NO_HP_OWNER'); */
		/* $data[] = env('NO_HP_OWNER2'); */

		/* $data[] = env('ZENZIVA_USERKEY'); */
		/* $data[] = env('ZENZIVA_PASSKEY'); */

		/* $data[] = env('NPWP'); */
		/* $data[] = env('NAMA_BADAN_USAHA'); */
		/* dd($data); */
	}
}
