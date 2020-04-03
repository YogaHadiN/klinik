<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Outbox;
use App\Pengeluaran;
use App\Woowa;
use App\Panggilan;
use App\Rekening;
use App\Sms;
use App\AntrianPoli;
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
    protected $description = 'Command description';

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
		DB::statement("CREATE TABLE pasien_rujuk_baliks ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, pasien_id VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");

		$timestamp = date('Y-m-d H:i:s');
		
		$datas = [
			[
				'pasien_id' => '170803024',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '190722005',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '171122008',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => 'A140000441',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '171128022',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '160630020',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => 'S153400119',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '181216007',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => 'H153400002',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '151001024',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '190429008',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '170925011',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => 'P140000060',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '161003009',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '170522009',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '190916009',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '533-11',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '160623006',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '160227034',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '180912006',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '181202023',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => 'S150000522',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => 'M153400188',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => 'V153400002',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => 'H153400034',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '171218013',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => 'I153400032',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '151130019',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '170522024',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '160826001',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '180125002',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '170803023',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '170702023',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'created_at' => $timestamp,
				'pasien_id' => '181225013',
				'updated_at' => $timestamp
			], 
		];
		PasienRujukBalik::insert($datas);

   	}
}
