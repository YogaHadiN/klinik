<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Outbox;
use App\Pengeluaran;
use App\AntrianPoli;
use App\Pasien;
use App\Sms;
use App\Terapi;
use App\AntrianPeriksa;
use App\FakturBelanja;
use App\JurnalUmum;
use App\Periksa;
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
		Log::info('oh yeaaaah');

		/* $periksas = Terapi::with('periksa')->where('created_at', '>', '2017-01-01')->get(); */
		/* $errors = []; */
		/* foreach ($periksas as $periksa) { */
		/* 	try { */
		/* 		$periksa->merek->merek; */
		/* 	} catch (\Exception $e) { */
		/* 		$errors[] = [ */
		/* 			'merek_id' => $periksa->merek_id, */
		/* 			'periksa_id' => $periksa->periksa->id, */
		/* 			'poli' => $periksa->periksa->poli */
		/* 		]; */
		/* 		/1* $errors[] = $periksa->periksa; *1/ */
		/* 	} */
		/* } */
		/* dd($errors); */
	}
}
