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
use App\Imports\PembayaranImport;
use Maatwebsite\Excel\Facades\Excel;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Vultr\VultrClient;
use Vultr\Adapter\GuzzleHttpAdapter;


class TestController extends Controller
{

	public function index(){
		$data = "testtesttest";
		$secretKey = "secretkey";
		 
		// Computes the signature by hashing the salt with the secret key as the key
		$signature = hash_hmac('sha256', $data, $secretKey, true);
		 
		// base64 encode…
		$encodedSignature = base64_encode($signature);
		// urlencode…
		// $encodedSignature = urlencode($encodedSignature);
		 
		return "Voila! A signature: " . $encodedSignature;

	}
}
