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
		DB::statement("CREATE TABLE jenis_antrians ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, jenis_antrian VARCHAR(30) NOT NULL, prefix VARCHAR(30) NOT NULL, created_at timestamp, updated_at timestamp);");
		DB::statement("CREATE TABLE poli_antrians ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, jenis_antrian_id VARCHAR(30) NOT NULL, poli_id VARCHAR(30) NOT NULL, created_at timestamp, updated_at timestamp);");
		DB::statement("CREATE TABLE panggilans ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, antrian_id VARCHAR(30) NOT NULL, created_at timestamp, updated_at timestamp);");
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
				'jenis_antrian' => 'Poli USG',
				'prefix' => 'E',
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
				'poli_id' => 'luka',
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
				'poli_id' => 'usg',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 5,
				'poli_id' => 'usgabdomen',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
			[
				'jenis_antrian_id' => 6,
				'poli_id' => 'darurat',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			],
		];
		PoliAntrian::insert($poli_antrians);
		JenisAntrian::insert($jenis_antrians);
		$panggilan       = new Panggilan;
		$panggilan->antrian_id   = '90000';
		$panggilan->save();
		DB::statement("ALTER TABLE antrians ADD jenis_antrian_id varchar(255);");
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
	}


	/* private function tarifCorrection(){ */
	/* 	$asuransis = Asuransi::all(); */

	/* 	$jenis_tarif_ids = []; */
	/* 	$data            = []; */
	/* 	foreach ($asuransis as $asu) { */
	/* 		$jenis_tarif_ids = ['147', '148', '149', '150']; */
	/* 		foreach ($asu->tarif as $tr) { */
	/* 			$jenis_tarif_ids[] = $tr->jenis_tarif_id; */
	/* 		} */

	/* 		$non_jenis_tarif = JenisTarif::whereNotIn('id', $jenis_tarif_ids)->get(); */
	/* 		$jenis_tarifs = []; */
	/* 		foreach ($non_jenis_tarif as $jt) { */
	/* 			$jenis_tarifs[] = $jt->id; */
	/* 		} */
	/* 		if (count($jenis_tarifs)) { */
	/* 			$data[] = [ */
	/* 				'asuransi_id'   => $asu->id, */
	/* 				'jenis_tarif_ids'  => $jenis_tarifs */
	/* 			]; */
	/* 		} */
	/* 	} */
	/* 	/1* dd($data); *1/ */
	/* 	$result = []; */
	/* 	$timestamp = date('Y-m-d H:i:s'); */
	/* 	foreach ($data as $d) { */
	/* 		$tarifs = Tarif::where('asuransi_id', '0') */
	/* 					->whereIn('jenis_tarif_id', $d['jenis_tarif_ids']) */
	/* 					->get(); */
	/* 		foreach ($tarifs as $t) { */
	/* 			$result[] = [ */
	/* 				"jenis_tarif_id"        => $t->jenis_tarif_id, */
	/* 				"biaya"                 => $t->biaya, */
	/* 				"asuransi_id"           => $d['asuransi_id'], */
	/* 				"jasa_dokter"           => $t->jasa_dokter, */
	/* 				"tipe_tindakan_id"      => $t->tipe_tindakan_id, */
	/* 				"bhp_items"             => $t->bhp_items, */
	/* 				"jasa_dokter_tanpa_sip" => $t->jasa_dokter_tanpa_sip, */
	/* 				'created_at'            => $timestamp, */
	/* 				'updated_at'            => $timestamp */
	/* 			]; */
	/* 		} */
	/* 	} */
	/* 	Tarif::insert($result); */
	/* } */
}
