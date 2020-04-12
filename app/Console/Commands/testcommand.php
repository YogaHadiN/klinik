<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Outbox;
use App\Pengeluaran;
use App\Woowa;
use App\Role;
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
		DB::statement("CREATE TABLE roles ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, role VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");
		$timestamp = date('Y-m-d H:i:s');
		$datas = [
			[
                'role' => 'Dokter',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
                'role' => 'Kasir',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
                'role' => 'Bidan',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
                'role' => 'Admin',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
                'role' => 'Dokter Gigi',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
                'role' => 'Super Admin',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			]
		];
		Role::insert($datas);
   	}
}
