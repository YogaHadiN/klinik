<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Sms;

class WoowaController extends Controller
{
	public function webhook(){
		$json = file_get_contents('php://input');

		Log::info($json);

		$data = json_decode($json);
		$message = $data->message;
		$no_telp = $data->contact_name;

		if ( $message == 'daftar' ) {
			$message = 'Bisa dibantu tanggal lahirnya? Contoh 19 Juli 1993 kirim 19-07-1983';
			Sms::send($no_telp, $message);
			/* $whatsapp_registration            = new WhatsappRegistration; */
			/* $whatsapp_registration->no_telp   = $np_telp; */
			/* $whatsapp_registration->reg_level = 1; */
			/* $whatsapp_registration->save(); */
		}
		/* $message = 'Selamat Siang. Terima kasih telah menghubungi kami. Ada yang dapat kami bantu?'; */
		/* Sms::send($data->contact_name, $message); */
	}
}
