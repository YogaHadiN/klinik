<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Sms;

class WoowaController extends Controller
{
	public function webhook(){
		$json = file_get_contents('php://input');

		Log::info('this is sparta');
		Log::info($json);

		$data = json_decode($json);

		Log::info('contact name');
		Log::info($data->contact_name);

		Log::info('message');
		Log::info($data->message);

		$message = 'Selamat Siang. Terima kasih telah menghubungi kami. Ada yang dapat kami bantu?';

		Sms::send($data->contact_name, $message);
	}
}
