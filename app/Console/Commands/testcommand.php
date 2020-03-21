<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Outbox;
use App\Pengeluaran;
use App\Woowa;
use App\Sms;
use App\AntrianPoli;
use App\PembayaranAsuransi;
use App\CatatanAsuransi;
use App\PiutangDibayar;
use App\NotaJual;
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
		$piutang_asuransi = PembayaranAsuransi::where('id', [878,877,861])->get();

		$nota_jual_ids = [];
		foreach ($piutang_asuransi as $pa) {
			$nota_jual_ids[] = $pa->nota_jual_id;
		}
		JurnalUmum::where('jurnalable_type', 'App\\NotaJual')->whereIn('jurnalable_id', $nota_jual_ids )->delete();
		NotaJual::destroy($nota_jual_ids);
		PembayaranAsuransi::destroy([878,877,861]);
		/* CatatanAsuransi::whereIn('pembayaran_asuransi_id', [878,877,861])->delete(); */
		PiutangDibayar::whereIn('pembayaran_asuransi_id', [878,877,861])->delete();

		$query = "UPDATE piutang_asuransis as pa ";
		$query .= "JOIN periksas as px on px.id = pa.periksa_id ";
		$query .= "SET sudah_dibayar = 0 ";
		$query .= "WHERE px.tanggal like '2019-12%' ";
		$query .= "AND px.asuransi_id  = '21';";
		DB::statement($query);
		DB::statement("UPDATE invoices set pembayaran_asuransi_id = null where pembayaran_asuransi_id in (878,877,861)");
		DB::statement("UPDATE rekenings set pembayaran_asuransi_id = null where pembayaran_asuransi_id in (878,877,861)");
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
