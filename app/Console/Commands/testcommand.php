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

		DB::statement("update piutang_asuransis as pa join periksas as px on px.id = pa.periksa_id set sudah_dibayar=0 where px.asuransi_id='160207002' and (px.tanggal like '2020-02%') or px.tanggal like '2019-12%' or px.tanggal like '2019-08%';");
		DB::statement("UPDATE invoices set pembayaran_asuransi_id = null where pembayaran_asuransi_id in (808, 862, 866)");
		DB::statement("UPDATE rekenings set pembayaran_asuransi_id = null where pembayaran_asuransi_id in (808, 862, 866)");
		DB::statement("delete pd from piutang_dibayars as pd join periksas as px on px.id = pd.periksa_id where px.asuransi_id = 21 and px.tanggal like '2019-12%';");
		DB::statement("delete pd from piutang_dibayars as pd join periksas as px on px.id = pd.periksa_id where px.asuransi_id = 160207002 and (px.tanggal like '2020-02%' or px.tanggal like '2019-12%' or px.tanggal like '2019-08%');");
	}

    /* public function handle() */
    /* { */
		/* $piutang_asuransi = PembayaranAsuransi::where('id', [878,877,861])->get(); */

		/* $nota_jual_ids = []; */
		/* foreach ($piutang_asuransi as $pa) { */
			/* $nota_jual_ids[] = $pa->nota_jual_id; */
		/* } */
		/* JurnalUmum::where('jurnalable_type', 'App\\NotaJual')->whereIn('jurnalable_id', $nota_jual_ids )->delete(); */
		/* NotaJual::destroy($nota_jual_ids); */
		/* PembayaranAsuransi::destroy([878,877,861]); */
		/* /1* CatatanAsuransi::whereIn('pembayaran_asuransi_id', [878,877,861])->delete(); *1/ */
		/* PiutangDibayar::whereIn('pembayaran_asuransi_id', [878,877,861])->delete(); */

		/* $query = "UPDATE piutang_asuransis as pa "; */
		/* $query .= "JOIN periksas as px on px.id = pa.periksa_id "; */
		/* $query .= "SET sudah_dibayar = 0 "; */
		/* $query .= "WHERE px.tanggal like '2019-12%' "; */
		/* $query .= "AND px.asuransi_id  = '21';"; */
		/* DB::statement($query); */
		/* DB::statement("UPDATE invoices set pembayaran_asuransi_id = null where pembayaran_asuransi_id in (878,877,861)"); */
		/* DB::statement("UPDATE rekenings set pembayaran_asuransi_id = null where pembayaran_asuransi_id in (878,877,861)"); */
	/* } */
}
