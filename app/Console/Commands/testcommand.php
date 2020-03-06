<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Outbox;
use App\Pengeluaran;
use App\AntrianPoli;
use App\Pasien;
use App\Sms;
use App\Terapi;
use App\AntrianPeriksa;
use App\FakturBelanja;
use App\JurnalUmum;
use App\Periksa;
use App\Asuransi;
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
		$asuransis = Asuransi::all();
		$data = [];
		$timestamp = date('Y-m-d H:i:s');
		foreach ($asuransis as $asu) {
			if (!empty($asu->no_telp)) {
				$data[] = [
					'nomor'           => $asu->no_telp,
					'telponable_id'   => $asu->id,
					'telponable_type' => 'App\\Asuransi',
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];
			}
		}
		Telpon::insert($data);
		DB::statement('ALTER table asuransis drop column no_telp');
		DB::statement('ALTER TABLE asuransis ADD id2 bigint;');
		$asuransis = Asuransi::all();
		foreach ($asuransis as $k => $asu) {
			$asu->id2 = $k;
			$asu->save();
			DB::statement("update antrian_polis set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update periksas set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update pics set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update pembayaran_asuransis set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update pasiens set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update sops set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update tarifs set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			db::statement("update antrian_periksas set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			db::statement("update discount_asuransis set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			db::statement("update emails set emailable_id='{$asu->id2}' where emailable_id='{$asu->id}' and emailable_type='App\\\Asuransi';");
			db::statement("update telpons set telponable_id='{$asu->id2}' where telponable_id='{$asu->id}' and telponable_type='App\\\Asuransi';");
		}
		DB::statement('ALTER TABLE asuransis MODIFY id INT NOT NULL;');
		DB::statement('ALTER TABLE asuransis DROP PRIMARY KEY;');
		DB::statement('ALTER TABLE asuransis DROP id;');
		DB::statement('ALTER TABLE asuransis CHANGE `id2` `id` bigint not null primary key;');
	}
}
