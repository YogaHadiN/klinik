<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Outbox;
use App\Pengeluaran;
use App\AntrianPoli;
use App\Pasien;
use App\Sms;
use App\KirimBerkas;
use App\Invoice;
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
		DB::statement("alter table kirim_berkas modify id varchar(255);");
		DB::statement("CREATE TABLE telpons ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, nomor VARCHAR(30) NOT NULL, telponable_type VARCHAR(30) NOT NULL, telponable_id VARCHAR(30) NOT NULL, created_at timestamp, updated_at timestamp);");
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
		DB::statement('ALTER table kirim_berkas add alamat text null');
		$statement = "CREATE TABLE invoices ";
		$statement .= "( ";
		$statement .= "id varchar(255) PRIMARY KEY, ";
		$statement .= "kirim_berkas_id VARCHAR(30) NOT NULL, ";
		$statement .= "created_at timestamp, ";
		$statement .= "updated_at timestamp";
		$statement .= ");";
		DB::statement($statement);

		$kirim_berkas = KirimBerkas::with('piutang_asuransi.periksa')->get();
		DB::statement('ALTER TABLE piutang_asuransis CHANGE `kirim_berkas_id` `invoice_id` varchar(255) null;');
		$invoices     = [];
		$timestamp    = date('Y-m-d H:i:s');
		foreach ($kirim_berkas as $kirim) {
			foreach ($kirim->piutang_asuransi as $piutang) {
				$invoices[ $piutang->kirim_berkas_id ][$piutang->periksa->asuransi_id] = [
					'id'              => $this->invoice_id($piutang),
					'kirim_berkas_id' => $piutang->kirim_berkas_id,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];
				$piutang->invoice_id = $this->invoice_id($piutang);
				$piutang->save();
			}
		}

		$datas = [];
		foreach ($invoices as $inv) {
			foreach ($inv as $in) {
				$datas[] = $in;
			}
		}
		Invoice::insert($datas);
	}
	public function invoice_id($piutang){
		/* INV/12/KJE/III/2019/1 */
		$kirim_berkas_id = $piutang->kirim_berkas_id;
		$ids = explode('/', $kirim_berkas_id);

		if (count($ids)>1) {
			$payor = $piutang->periksa->asuransi_id;
			$result = $ids[0] . '/'; //inv
			$result .= $ids[1] . '/'; //12
			$result .= $ids[2] . '/'; // kje
			$result .= 'PYR-' .$payor .'/';
			$result .= $ids[3] . '/'; //12
			$result .= $ids[4];
		} else {
			return $kirim_berkas_id . '/' . $piutang->periksa->asuransi_id;
		}
	}
}
