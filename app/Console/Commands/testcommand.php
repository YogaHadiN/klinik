<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Outbox;
use App\Pengeluaran;
use App\Woowa;
use App\Sms;
use App\AntrianPoli;
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
		DB::statement("update rekenings set pembayaran_asuransi_id ='850' where id = '26';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='849' where id = '6';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='845' where id = '8';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='842' where id = '0LWdpoqEYWe';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='851' where id = 'Arz6gYaA8jK';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='854' where id = '36';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='848' where id = 'Arz6gdpXwjK';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='847' where id = 'Exj7r4wwaz5';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='846' where id = '3ykV2mbNZkN';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='844' where id = '3';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='843' where id = '2';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='841' where id = '31';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='840' where id = '32';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='839' where id = '21';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='816' where id = '66';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='817' where id = '44';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='815' where id = '28';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='814' where id = '61';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='813' where id = '60';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='809' where id = '41';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='812' where id = '52';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='811' where id = '52';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='810' where id = '52';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='819' where id = '50';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='820' where id = '54';");
		DB::statement("update rekenings set pembayaran_asuransi_id ='820' where id = '62';");
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
