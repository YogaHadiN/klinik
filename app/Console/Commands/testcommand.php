<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Outbox;
use App\Pengeluaran;
use App\Woowa;
use App\PengantarPasien;
use App\Role;
use App\Panggilan;
use App\Rekening;
use App\Sms;
use App\StatusBpjs;
use App\AntrianPoli;
use App\KunjunganSakit;
use App\PembayaranAsuransi;
use App\CatatanAsuransi;
use App\AbaikanTransaksi;
use App\PiutangDibayar;
use App\NotaJual;
use App\PoliAntrian;
use App\KirimBerkas;
use App\PasienRujukBalik;
use App\JenisTarif;
use App\Pasien;
use App\Invoice;
use App\Terapi;
use App\AntrianPeriksa;
use App\Tarif;
use App\FakturBelanja;
use App\JurnalUmum;
use App\Periksa;
use App\JenisAntrian;
use App\Telpon;
use DB;
use Artisan;
use Mail;
use Log;
use Input;

class testcommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is spartaaaa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


		$uri="https://dvlp.bpjs-kesehatan.go.id:9081/pcare-rest-v3.0/dokter/0/13"; //url web service bpjs;
		/* $uri="https://dvlp.bpjs-kesehatan.go.id:9081/pcare-rest-v3.0/provider/0/3"; //url web service bpjs; */
		/* $uri="https://dvlp.bpjs-kesehatan.go.id:9081/pcare-rest-v3.0/peserta/0001183422677"; //url web service bpjs; */
		$consID 	= "27802"; //customer ID anda
		$secretKey 	= "6nNF409D69"; //secretKey anda

		$pcareUname = "klinik_jatielok"; //username pcare
		$pcarePWD 	= "*Bpjs2020"; //password pcare anda
		$kdAplikasi	= "095"; //kode aplikasi

		$stamp		= time();
		$data 		= $consID.'&'.$stamp;

		$signature = hash_hmac('sha256', $data, $secretKey, true);
		$encodedSignature = base64_encode($signature);	
		$encodedAuthorization = base64_encode($pcareUname.':'.$pcarePWD.':'.$kdAplikasi);	

		$headers = array( 
					"Accept: application/json", 
					"X-cons-id:".$consID, 
					"X-timestamp: ".$stamp, 
					"X-signature: ".$encodedSignature, 
					"X-authorization: Basic " .$encodedAuthorization 
				); 

		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		$data = curl_exec($ch);
		curl_close($ch);
		dd($data);
		/* return $data; */
	}
	private function thisCoba(){
		$client->request('GET', '/get', [
			'headers' => [
				'User-Agent' => 'testing/1.0',
				'Accept'     => 'application/json',
				'X-Foo'      => ['Bar', 'Baz']
			]
		]);
	}
}
