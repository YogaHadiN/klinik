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
		DB::statement("update rekenings set pembayaran_asuransi_id='807' where id = '80';");
		DB::statement("update rekenings set pembayaran_asuransi_id='815' where id = '64';");
		DB::statement("update rekenings set pembayaran_asuransi_id='780' where id = '88';");
		DB::statement("update rekenings set pembayaran_asuransi_id='781' where id = '76';");
		DB::statement("update rekenings set pembayaran_asuransi_id='793' where id = '58';");
		DB::statement("update rekenings set pembayaran_asuransi_id='792' where id = '63';");
		DB::statement("update rekenings set pembayaran_asuransi_id='791' where id = '85';");
		DB::statement("update rekenings set pembayaran_asuransi_id='808' where id = '73';");
		DB::statement("update rekenings set pembayaran_asuransi_id='785' where id = '82';");
		DB::statement("update rekenings set pembayaran_asuransi_id='785' where id = '83';");
		DB::statement("update rekenings set pembayaran_asuransi_id='787' where id = '57';");
		DB::statement("update rekenings set pembayaran_asuransi_id='789' where id = '78';");
		DB::statement("update rekenings set pembayaran_asuransi_id='788' where id = '78';");
		DB::statement("update rekenings set pembayaran_asuransi_id='787' where id = '57';");
		DB::statement("update rekenings set pembayaran_asuransi_id='781' where id = '76';");
		DB::statement("update rekenings set pembayaran_asuransi_id='782' where id = '68';");
		DB::statement("update rekenings set pembayaran_asuransi_id='780' where id = '88';");
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
