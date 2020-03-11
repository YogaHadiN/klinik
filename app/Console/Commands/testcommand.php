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
		DB::statement("update jurnal_umums set coa_id='111066' where id='1138326'"); // etiqa
		DB::statement("update jurnal_umums set coa_id='111066' where id='1138332'"); // etiqa
		DB::statement("update jurnal_umums set coa_id='111066' where id='1138835'"); // etiqa
		DB::statement("update jurnal_umums set coa_id='111066' where id='1138837'"); // etiqa
	}
}
