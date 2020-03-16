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
		DB::statement("update rekenings ")	850	26
		DB::statement("update rekenings ")	849	6
		DB::statement("update rekenings ")	845	8
		DB::statement("update rekenings ")	842	0LWdpoqEYWe
		DB::statement("update rekenings ")	851	Arz6gYaA8jK
		DB::statement("update rekenings ")	854	36
		DB::statement("update rekenings ")	848	Arz6gdpXwjK
		DB::statement("update rekenings ")	847	Exj7r4wwaz5
		DB::statement("update rekenings ")	846	3ykV2mbNZkN
		DB::statement("update rekenings ")	844	3
		DB::statement("update rekenings ")	843	2
		DB::statement("update rekenings ")	841	31
		DB::statement("update rekenings ")	840	32
		DB::statement("update rekenings ")	839	21
		DB::statement("update rekenings ")	816	66
		DB::statement("update rekenings ")	817	44
		DB::statement("update rekenings ")	815	28
		DB::statement("update rekenings ")	814	61
		DB::statement("update rekenings ")	813	60
		DB::statement("update rekenings ")	809	41
		DB::statement("update rekenings ")	812	52
		DB::statement("update rekenings ")	811	52
		DB::statement("update rekenings ")	810	52
		DB::statement("update rekenings ")	819	50
		DB::statement("update rekenings ")	820	54
		DB::statement("update rekenings ")	820	62
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
