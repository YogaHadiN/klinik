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

		$ids = [
			'26',
			'6',
			'8',
			'0LWdpoqEYWe',
			'Arz6gYaA8jK',
			'36',
			'Arz6gdpXwjK',
			'Exj7r4wwaz5',
			'3ykV2mbNZkN',
			'3',
			'2',
			'31',
			'32',
			'21',
			'66',
			'44',
			'28',
			'61',
			'60',
			'41',
			'52',
			'52',
			'52',
			'50',
			'54',
			'62',
			'7',
			'5',
			'11',
			'12',
			'2qjy21O2wWG',
			'Z6zK1wgD0WJ',
			'40',
			'q7WPxPZZXWA',
			'eMkb75Lx1zY',
			'VGjZdXMVJkr',
			'G1kap7LMbWg',
			'1',
			'3EWgGRpAEWP',
			'ylzrPrOyxWx',
			'28',
			'G4kYwgPoGjp',
			'25',
			'2qjy2RKVdWG',
			'Z6zK1wdeYWJ',
			'agzqeKNd9z4',
			'agzqeRQAdz4',
			'4',
			'24',
			'23',
			'9',
			'agzqeKNd9z4',
			'2qjy2RKVdWG',
			'2qjy2RKVdWG',
			'45',
			'39',
			'89',
			'90'
		];

		dd( Rekening::whereIn('id', $ids)->whereNull('pembayaran_asuransi_id')->get(['id', 'pembayaran_asuransi_id']) );

		DB::statement("update rekenings set pembayaran_asuransi_id='850' where id='26';");
		DB::statement("update rekenings set pembayaran_asuransi_id='849' where id='6';");
		DB::statement("update rekenings set pembayaran_asuransi_id='845' where id='8';");
		DB::statement("update rekenings set pembayaran_asuransi_id='842' where id='0LWdpoqEYWe';");
		DB::statement("update rekenings set pembayaran_asuransi_id='851' where id='Arz6gYaA8jK';");
		DB::statement("update rekenings set pembayaran_asuransi_id='854' where id='36';");
		DB::statement("update rekenings set pembayaran_asuransi_id='848' where id='Arz6gdpXwjK';");
		DB::statement("update rekenings set pembayaran_asuransi_id='847' where id='Exj7r4wwaz5';");
		DB::statement("update rekenings set pembayaran_asuransi_id='846' where id='3ykV2mbNZkN';");
		DB::statement("update rekenings set pembayaran_asuransi_id='844' where id='3';");
		DB::statement("update rekenings set pembayaran_asuransi_id='843' where id='2';");
		DB::statement("update rekenings set pembayaran_asuransi_id='841' where id='31';");
		DB::statement("update rekenings set pembayaran_asuransi_id='840' where id='32';");
		DB::statement("update rekenings set pembayaran_asuransi_id='839' where id='21';");
		DB::statement("update rekenings set pembayaran_asuransi_id='816' where id='66';");
		DB::statement("update rekenings set pembayaran_asuransi_id='817' where id='44';");
		DB::statement("update rekenings set pembayaran_asuransi_id='815' where id='28';");
		DB::statement("update rekenings set pembayaran_asuransi_id='814' where id='61';");
		DB::statement("update rekenings set pembayaran_asuransi_id='813' where id='60';");
		DB::statement("update rekenings set pembayaran_asuransi_id='809' where id='41';");
		DB::statement("update rekenings set pembayaran_asuransi_id='812' where id='52';");
		DB::statement("update rekenings set pembayaran_asuransi_id='811' where id='52';");
		DB::statement("update rekenings set pembayaran_asuransi_id='810' where id='52';");
		DB::statement("update rekenings set pembayaran_asuransi_id='819' where id='50';");
		DB::statement("update rekenings set pembayaran_asuransi_id='820' where id='54';");
		DB::statement("update rekenings set pembayaran_asuransi_id='820' where id='62';");
		DB::statement("update rekenings set pembayaran_asuransi_id='862' where id='7';");
		DB::statement("update rekenings set pembayaran_asuransi_id='863' where id='5';");
		DB::statement("update rekenings set pembayaran_asuransi_id='864' where id='11';");
		DB::statement("update rekenings set pembayaran_asuransi_id='865' where id='12';");
		DB::statement("update rekenings set pembayaran_asuransi_id='866' where id='2qjy21O2wWG';");
		DB::statement("update rekenings set pembayaran_asuransi_id='867' where id='Z6zK1wgD0WJ';");
		DB::statement("update rekenings set pembayaran_asuransi_id='868' where id='40';");
		DB::statement("update rekenings set pembayaran_asuransi_id='869' where id='q7WPxPZZXWA';");
		DB::statement("update rekenings set pembayaran_asuransi_id='870' where id='eMkb75Lx1zY';");
		DB::statement("update rekenings set pembayaran_asuransi_id='871' where id='VGjZdXMVJkr';");
		DB::statement("update rekenings set pembayaran_asuransi_id='872' where id='G1kap7LMbWg';");
		DB::statement("update rekenings set pembayaran_asuransi_id='855' where id='1';");
		DB::statement("update rekenings set pembayaran_asuransi_id='856' where id='3EWgGRpAEWP';");
		DB::statement("update rekenings set pembayaran_asuransi_id='857' where id='ylzrPrOyxWx';");
		DB::statement("update rekenings set pembayaran_asuransi_id='858' where id='28';");
		DB::statement("update rekenings set pembayaran_asuransi_id='859' where id='G4kYwgPoGjp';");
		DB::statement("update rekenings set pembayaran_asuransi_id='860' where id='25';");
		DB::statement("update rekenings set pembayaran_asuransi_id='861' where id='2qjy2RKVdWG';");
		DB::statement("update rekenings set pembayaran_asuransi_id='876' where id='Z6zK1wdeYWJ';");
		DB::statement("update rekenings set pembayaran_asuransi_id='877' where id='agzqeKNd9z4';");
		DB::statement("update rekenings set pembayaran_asuransi_id='878' where id='agzqeRQAdz4';");
		DB::statement("update rekenings set pembayaran_asuransi_id='879' where id='4';");
		DB::statement("update rekenings set pembayaran_asuransi_id='881' where id='24';");
		DB::statement("update rekenings set pembayaran_asuransi_id='882' where id='23';");
		DB::statement("update rekenings set pembayaran_asuransi_id='883' where id='9';");
		DB::statement("update rekenings set pembayaran_asuransi_id='884' where id='agzqeKNd9z4';");
		DB::statement("update rekenings set pembayaran_asuransi_id='885' where id='2qjy2RKVdWG';");
		DB::statement("update rekenings set pembayaran_asuransi_id='886' where id='2qjy2RKVdWG';");
		DB::statement("update rekenings set pembayaran_asuransi_id='895' where id='45';");
		DB::statement("update rekenings set pembayaran_asuransi_id='896' where id='39';");
		DB::statement("update rekenings set pembayaran_asuransi_id='897' where id='89';");
		DB::statement("update rekenings set pembayaran_asuransi_id='898' where id='90';");
	}
}

