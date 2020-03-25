<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Outbox;
use App\Pengeluaran;
use App\Woowa;
use App\Panggilan;
use App\Sms;
use App\AntrianPoli;
use App\PembayaranAsuransi;
use App\CatatanAsuransi;
use Artisan;
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
		DB::statement("UPDATE rekenings set pembayaran_asuransi_id = null where pembayaran_asuransi_id in (878,877,861)");
		DB::statement("CREATE TABLE jenis_antrians ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, jenis_antrian VARCHAR(30) NOT NULL, prefix VARCHAR(30) NOT NULL, antrian_terakhir_id VARCHAR(255) NULL, created_at timestamp, updated_at timestamp);");
		DB::statement("CREATE TABLE poli_antrians ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, jenis_antrian_id VARCHAR(30) NOT NULL, poli_id VARCHAR(30) NOT NULL, created_at timestamp, updated_at timestamp);");
		$timestamp = date('Y-m-d H:i:s');
		$jenis_antrians = [
			[
				'jenis_antrian' => 'poli umum',
				'prefix' => 'A',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian' => 'poli gigi',
				'prefix' => 'B',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian' => 'poli kebidanan/kandungan',
				'prefix' => 'C',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian' => 'poli estetika',
				'prefix' => 'D',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian' => 'Darurat',
				'prefix' => 'F',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
		];
		$poli_antrians = [
			[
				'jenis_antrian_id' => 1,
				'poli_id' => 'umum',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 1,
				'poli_id' => 'sks',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 1,
				'poli_id' => 'luka',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 2,
				'poli_id' => 'gigi',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 3,
				'poli_id' => 'anc',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 3,
				'poli_id' => 'kb 1 bulan',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 3,
				'poli_id' => 'usg',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 3,
				'poli_id' => 'usgabdomen',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 3,
				'poli_id' => 'kb 3 bulan',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 4,
				'poli_id' => 'estetika',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 5,
				'poli_id' => 'darurat',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
		];
		PoliAntrian::insert($poli_antrians);
		JenisAntrian::insert($jenis_antrians);
		DB::statement("ALTER TABLE antrians ADD jenis_antrian_id varchar(255);");
		DB::statement("ALTER TABLE antrians ADD url varchar(255);");
		DB::statement("ALTER TABLE antrians DROP COLUMN antrian_terakhir;");
		DB::statement("ALTER TABLE antrian_polis DROP COLUMN antrian;");
		DB::statement("ALTER TABLE antrian_periksas DROP COLUMN antrian;");
		DB::statement("ALTER TABLE periksas DROP COLUMN antrian;");
		DB::statement("ALTER TABLE antrians ADD nomor int(11);");
		DB::statement("ALTER TABLE antrians ADD antriable_id varchar(30) null;");
		DB::statement("ALTER TABLE antrians ADD antriable_type varchar(30) null;");
		DB::statement("DELETE FROM antrians");
		DB::statement("DELETE FROM antrian_periksas");
		DB::statement("DELETE FROM antrian_polis");
		Artisan::call('test:console');
	}
}

