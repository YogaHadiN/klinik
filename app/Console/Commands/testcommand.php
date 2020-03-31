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
		$rekening_ids = [43, 59, 74, 86, 38, 42, 53, 56, 67, 75, 77];
		$ids = '';
		foreach ($rekening_ids as $k => $id) {
			if ( $k == 0 ) {
				$ids .= $id;
			} else {
				$ids .=  ',' . $id;
			}
		}
		DB::statement("UPDATE rekenings set pembayaran_asuransi_id = 906 where id in( " .$ids ." )");

		$rekening_ids = [4829, 4828, 4827];
		DB::statement("DELETE FROM pengeluarans where id in (" .$ids ." )");
		DB::statement("DELETE FROM jurnal_umums where jurnalable_id in (" .$ids ." ) and jurnalable_type = 'App\\\Pengeluaran'");
   	}
}
