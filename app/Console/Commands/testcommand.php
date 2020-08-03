<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Outbox;
use App\User;
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
		DB::statement("Delete from users where role not like 6;");
		$param = [
			[
				'username'       => 'Dokter',
				'role'           => 1    ,
				'password'       => '$2y$10$WqHVkYQlTaqucuf5LhijL..ppIe3A3OOuCKNzFXmuvfVmvAymmffu',
				'email'          => 'dokter@gmail.com'     ,
				'remember_token' => '',
				'aktif'          => 1 ,
				'created_at'     => '2020-08-03 09:57:16' ,
				'updated_at'     => '2020-08-03 09:59:39'
			],
			[
				'username'   => 'Admin',
				'role'       => 4    ,
				'password'   => '$2y$10$V9yLOMrJeC35XwvuiN3m/ezt6fIZ9PXGe9X07ITkROyo3fN6DCNx6' ,
				'email'      => 'admin@gmail.com'     ,
				'remember_token' => '',
				'aktif'      => 1 ,
				'created_at' => '2020-08-03 09:57:39',
				'updated_at' => '2020-08-03 09:59:50'
			],
			[
				'username'   => 'Keuangan          ',
				'role'       => 2    ,
				'password'   => '$2y$10$37iO.r6Du8uHmkTWk7I.vu8KP7Zf6wVf3/vjQKz9kv9ZijKxkb/Ra' ,
				'email'      => 'keuangan@gmail.com'     ,
				'remember_token' => '',
				'aktif'      => 1 ,
				'created_at' => '2020-08-03 09:57:39',
				'updated_at' => '2020-08-03 09:59:50'
			]
		];
		User::insert( $param );
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
	private function bpjs(){

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
	}
	private function errorLog(){
		DB::statement("delete from coas where id in (select co.id from coas as co left join jurnal_umums as ju on ju.coa_id = co.id where ju.coa_id is null and co.id like '12%')");
		DB::statement("delete from coas where id in (select co.id from coas as co left join jurnal_umums as ju on ju.coa_id = co.id where ju.coa_id is null and co.id like '10%')");
		DB::statement("update jurnal_umums set nilai = 20000 where id = 226687;");
		DB::statement("update jurnal_umums set nilai = 35000 where id = 393460;");
		DB::statement("update jurnal_umums set nilai = 20000 where id = 459209;");
		DB::statement("update jurnal_umums set nilai = 35000 where id = 520931;");
		DB::statement("update jurnal_umums set nilai = 115000 where id = 721494;");
		DB::statement("update jurnal_umums set nilai = 35000 where id = 758562;");
		DB::statement("update jurnal_umums set nilai = 15000 where id = 768188;");
		DB::statement("update jurnal_umums set nilai = 35000 where id = 819723;");
		DB::statement("update jurnal_umums set nilai = 20000 where id = 964228;");
		DB::statement("update jurnal_umums set nilai = 35000 where id = 983506;");
		DB::statement("update jurnal_umums set nilai = 85000 where id = 307335;");
		DB::statement("delete from jurnal_umums where jurnalable_type = 'App\\\Pengeluaran' and jurnalable_id = 5182;");
		DB::statement("delete from pengeluarans where id = 5182;");
	}
}
