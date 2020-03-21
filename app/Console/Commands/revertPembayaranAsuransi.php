<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PembayaranAsuransi;
use App\PiutangAsuransi;
use App\JurnalUmum;
use App\NotaJual;
use App\PiutangDibayar;
use DB;

class revertPembayaranAsuransi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:revertPembayaranAsuransi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'revertPembayaranAsuransi';

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
}
