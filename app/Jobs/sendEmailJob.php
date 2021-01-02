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
    public function handle()
    {
        /* Mail::to('yoga_email@yahoo.com')->send(new SendEmailMailable()); */
		/* Sms::send('081381912803', $pesan); */
		if ( $this->foo->no_telp != '08999993744' ) {
			Sms::send($this->foo->no_telp, $pesan);
			DataDuplikat::create([
				'no_telp' => $this->foo->no_telp,
				'pasien_id' => $this->foo->id
			]);
		}
		Log::info('terkirim ke ' . $this->foo->no_telp);
    }
}
