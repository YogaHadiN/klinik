<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Outbox;
use App\Pengeluaran;
use App\Woowa;
use App\PengantarPasien;
use App\Role;
use App\Panggilan;
use App\Rekening;
use App\Sms;
use App\StatusBpjs;
use App\AntrianPoli;
use App\KunjunganSakit;
use App\PembayaranAsuransi;
use App\CatatanAsuransi;
use App\AbaikanTransaksi;
use App\PiutangDibayar;
use App\NotaJual;
use App\PoliAntrian;
use App\KirimBerkas;
use App\PasienRujukBalik;
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
    protected $description = 'This is spartaaaa';

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
		DB::statement('ALTER TABLE pasiens ADD sudah_kontak_bulan_ini tinyint(1) not null default 0;');

		$periksaBulanIni         = Periksa::with('pasien')->where('created_at', 'like', date('Y-m') . '%')
									->where('asuransi_id', '32')
									->get();
		$pengantarPasienBulanIni = PengantarPasien::with('pengantar')->where('created_at', 'like', date('Y-m') . '%')
											->where('pcare_submit', '1')
											->get();
		$KunjunganSakitBulanIni  = KunjunganSakit::with('periksa.pasien')->where('created_at', 'like', date('Y-m') . '%')
											->where('pcare_submit', '1')
											->get();

		foreach ($periksaBulanIni as $p) {
			$pasien                          = $p->pasien;
			$pasien->sudah_kontak_bulan_ini = 1;
			$pasien->save();
		}

		foreach ($KunjunganSakitBulanIni as $p) {
			$pasien                          = $p->pasien;
			$pasien->sudah_kontak_bulan_ini = 1;
			$pasien->save();
		}

		foreach ($pengantarPasienBulanIni as $p) {
			$pasien                          = $p->pengantar;
			$pasien->sudah_kontak_bulan_ini = 1;
			$pasien->save();
		}

		DB::statement('ALTER TABLE pasiens ADD kepala_keluarga_id VARCHAR( 255 );');

		$query = "CREATE TABLE status_bpjs (";
		$query .= "id int, ";
		$query .= "status_bpjs varchar(255),";
		$query .= "created_at timestamp,";
		$query .= "updated_at timestamp,";
		$query .= "primary key(`id`)";
		$query .= ");";

		DB::statement($query);

		$query = "CREATE TABLE home_visits (";
		$query .= "id INT AUTO_INCREMENT PRIMARY KEY,";
		$query .= "pasien_id varchar(255) not null,";
		$query .= "sistolik varchar(255),";
		$query .= "diastolik varchar(255),";
		$query .= "berat_badan varchar(255),";
		$query .= "image varchar(255),";
		$query .= "created_at timestamp,";
		$query .= "updated_at timestamp";
		$query .= ");";

		DB::statement($query);

		$timestamp = date('Y-m-d H:i:s');
		$data = [
			[  
				'id'          => '0',
				'status_bpjs' => 'berlaku',
				'created_at'  => $timestamp,
				'updated_at'  => $timestamp
			],
			[  
				'id'          => '1',
				'status_bpjs' => 'tidak berlaku',
				'created_at'  => $timestamp,
				'updated_at'  => $timestamp
		   	]
		];
		StatusBpjs::insert($data);
   	}
}
