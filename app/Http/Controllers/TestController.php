<?php



namespace App\Http\Controllers;

use Input;

use App\Http\Requests;

use DB;
use Moota;
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
use App\Imports\PembayaranImport;
use Maatwebsite\Excel\Facades\Excel;


class TestController extends Controller
{

	public function index(){

		$json = Moota::mutation('wnazGyxGWGA')->month();

		$json = json_decode($json, true);

		$data = [];
		foreach ($json as $j) {
			$data[] = [
				'akun_bank_id' => $j['akun_bank_id'],
				'transaksi_id' => $j['transaksi_id'],
				'tanggal'      => $j['tanggal'],
				'deskripsi'    => $j['deskripsi'],
				'nilai'        => $j['nilai'],
				'saldo_akhir'  => $j['saldo_akhir'],
				'debet'        => $j['debet'],
				'created_at'   => $j['created_at']
			];
		}



	}
}
