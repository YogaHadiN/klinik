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
				'pasien_id' => '10',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '10',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 

			[
				'pasien_id' => '10',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '10',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '10',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
			[
				'pasien_id' => '10',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			], 
		];

		PasienRujukBalik::insert($datas);

   	}
}
