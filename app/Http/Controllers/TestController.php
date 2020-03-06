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
use App\AkunBank;
use App\Rekening;
use App\Http\Handler;
use App\Console\Commands\sendMeLaravelLog;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Vultr\VultrClient;
use Vultr\Adapter\GuzzleHttpAdapter;
use App\Imports\PembayaranImport;
use Maatwebsite\Excel\Facades\Excel;


class TestController extends Controller
{

	public function index(){

		return view('test.index');

	}
	public function post(){
		if (Input::hasFile('rekening')) {
			$file =Input::file('rekening'); //GET FILE
			$excel_pembayaran = Excel::toArray(new PembayaranImport, $file)[0];
			$data = [];
			$timestamp = date('Y-m-d H:i:s');
			foreach ($excel_pembayaran as $k => $e) {
				$data[] = [
					'id' => $k +1,
					'akun_bank_id' => 'wnazGyxGWGA',
					'tanggal'      => $e['tanggal'],
					'deskripsi'    => $e['deskripsi'],
					'nilai'        => $e['nilai'],
					'saldo_akhir'  => 0,
					'debet'        => 0,
					'created_at' => $timestamp,
					'updated_at' => $timestamp
				];
			}
			Rekening::insert($data);
		}  
	}
	
}
