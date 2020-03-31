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
		DB::statement("CREATE TABLE abaikan_transaksis ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, transaksi_id VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");
		DB::statement("DELETE FROM raks where id = 'X1'");
		DB::statement("DELETE FROM mereks where rak_id = 'X1'");
		DB::statement("UPDATE asuransis set kali_obat = '1.25' where kali_obat is null");

		$timestamp = date('Y-m-d H:i:s');
		
		$abaikans = [
			[
				'transaksi_id' => '0LWdpRR7oWe',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'transaksi_id' => 'mVz5v0nQ4jv',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'transaksi_id' => 'nazGOrOaazG',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			]
		];
		AbaikanTransaksi::insert($abaikans);
		Artisan::call('test:console');
   	}
}
