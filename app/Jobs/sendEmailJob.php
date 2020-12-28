<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Mail;
use App\Mail\SendEmailMailable;
use App\Sms;

class sendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    protected $foo;
    public function __construct($foo)
    {
        $this->foo = $foo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /* Mail::to('yoga_email@yahoo.com')->send(new SendEmailMailable()); */
		$pesan = "*Klinik Jati Elok*";
		$pesan .= PHP_EOL;
		$pesan .= "*Komp. Bumi Jati Elok Blok A I No. 4-5*";
		$pesan .= PHP_EOL;
		$pesan .= "*Jl. Raya Legok - Parung Panjang km. 3*";
		$pesan .= PHP_EOL;
		$pesan .= "Melayani";
		$pesan .= PHP_EOL;
		$pesan .= "Rapid Test Antibody & RapiD Test Antigen (Swab Test Antigen)";
		$pesan .= PHP_EOL;
		$pesan .= PHP_EOL;
		$pesan .= "*Paket 1 | Rapid Test Antibody*";
		$pesan .= PHP_EOL;
		$pesan .= "(Rp.150.000)";
		$pesan .= PHP_EOL;
		$pesan .= "hasil keluar 15-30 menit";
		$pesan .= PHP_EOL;
		$pesan .= "darah kapiler";
		$pesan .= PHP_EOL;
		$pesan .= PHP_EOL;
		$pesan .= "*Paket 2 | Rapid Test Antigen (Swab Antigen)*";
		$pesan .= PHP_EOL;
		$pesan .= "(Rp.250.000)";
		$pesan .= PHP_EOL;
		$pesan .= "hasil keluar 30 menit- 1 jam";
		$pesan .= PHP_EOL;
		$pesan .= "metode swab belakang hidung / tenggorokan";
		$pesan .= PHP_EOL;
		$pesan .= "*Sebagai syarat perjalanan udara/laut/darat";
		$pesan .= PHP_EOL;
		$pesan .= "*dengan perjanjian";
		$pesan .= PHP_EOL;
		$pesan .= PHP_EOL;
		$pesan .= "Informasi hubungi 021 5977 529";
		$pesan .= PHP_EOL;
		$pesan .= "Atau whatsapp ke nomor 082278065959";
		$pesan .= PHP_EOL;
		$pesan .= "Atau klik https://wa.wizard.id/df2299";
		/* Sms::send('081381912803', $pesan); */
		if ( $this->foo->no_telp != '08999993744' ) {
			Sms::send($this->foo->no_telp, $pesan);
		}
		Log::info('terkirim ke ' . $this->foo->no_telp);
    }
}
