<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Dispensing;
use App\Classes\Yoga;
use App\Pasien;
use App\Periksa;
use App\TransaksiPeriksa;
use Log;
use App\Http\Controllers\CustomController;

class testConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:console';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah untuk test console shell script';

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

		$transaksis = TransaksiPeriksa::where('jenis_tarif_id', 116)->get();
		$periksa_ids = [];
		$transaksi_periksa_updates = [];
		foreach ($transaksis as $trx) {
			$pemeriksaan_penunjang = $trx->periksa->pemeriksaan_penunjang;
			$hasil = Yoga::get_string_between( $pemeriksaan_penunjang, 'Gula Darah ', ',' );
			$gula = preg_replace("/[^0-9]/","",$hasil);
			if ($gula < 1000) {
				$transaksi_periksa_updates[] = [
					'collection' => $trx,
					'updates' => [
						'keterangan_pemeriksaan' => $gula . ' gr/dL'
					]
				];
			}
		}
		$c = new CustomController;
		return dd( $c->massUpdate($transaksi_periksa_updates) );
    }
}
