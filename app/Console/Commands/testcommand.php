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
use App\Http\Controllers\WablasController;
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
		$wa = new WablasController;

		dd($wa->pesertaBpjs('0002940330148'));
		/* dd($wa->pesertaBpjs('0001970459842')); */
	}
	private function webhook(){
		$data["license"]="5c286f1ed7121";
		$data["url"]    ="https://yourwebsite.com/listen.php"; // message data will push to this url
		$data["no_wa"]  = "6289648615564";    //sender number registered in woowa
		$data["action"] = "set";

		$url="http://api.woo-wa.com/v2.0/webhook";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$err = curl_error($ch);
		curl_close ($ch);
		if ($err) {
			dd("cURL Error #:" . $err);
		} else {
			dd( $result);
		}
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
