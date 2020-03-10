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
		DB::statement("update asuransis set coa_id='111087' where id='200216001'"); // etiqa
		DB::statement("update asuransis set coa_id='111066' where id='161123001'"); // aca
		DB::statement("update asuransis set coa_id='111075' where id='170722001'"); // fwd
		DB::statement("update asuransis set coa_id='111001' where id='1'"); // pan pacific
		DB::statement("update asuransis set coa_id='111009' where id='151020001'"); // hanwha
		DB::statement("update asuransis set coa_id='111015' where id='160207002'"); // axa admedika pyr
		DB::statement("update asuransis set coa_id='111016' where id='160207003'"); // tokio marine
		DB::statement("update asuransis set coa_id='111017' where id='160207004'"); // sunlife
		DB::statement("update asuransis set coa_id='111019' where id='160207006'"); // icon plus
		DB::statement("update asuransis set coa_id='111020' where id='160207007'"); // pertamina
		DB::statement("update asuransis set coa_id='111021' where id='160207008'"); // aia
		DB::statement("update asuransis set coa_id='111022' where id='160207009'"); // indosurya
		DB::statement("update asuransis set coa_id='111023' where id='160207010'"); // mega life
		DB::statement("update asuransis set coa_id='111024' where id='160207011'"); // regas
		DB::statement("update asuransis set coa_id='111028' where id='160207015'"); // infomedia
		DB::statement("update asuransis set coa_id='111029' where id='160207016'"); // patra
		DB::statement("update asuransis set coa_id='111030' where id='160207017'"); // hdi
		DB::statement("update asuransis set coa_id='111031' where id='160207018'"); // bumiputera
		DB::statement("update asuransis set coa_id='111032' where id='160207019'"); // pertamina internasional
		DB::statement("update asuransis set coa_id='111036' where id='160207023'"); // pelita air
		DB::statement("update asuransis set coa_id='111071' where id='170309001'"); // bni_life
		DB::statement("update asuransis set coa_id='111081' where id='181015001'"); // bca_life
		DB::statement("update asuransis set coa_id='111083' where id='181231001'"); // angkasapura2
		DB::statement("update asuransis set coa_id='111056' where id='34'"); // as umum mega
		DB::statement("update asuransis set coa_id='111060' where id='5'"); // reliance
		$statement = "update jurnal_umums as ju ";
		$statement .= "join periksas as px on px.id = ju.jurnalable_id ";
		$statement .= "join asuransis as asu on asu.id = px.asuransi_id ";
		$statement .= "set ju.coa_id=asu.coa_id ";
		$statement .= "where ju.coa_id is null ";
		$statement .= "and ju.jurnalable_type='App\\\Periksa'";
		DB::statement($statement);
	}
}
