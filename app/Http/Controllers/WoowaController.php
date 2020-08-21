<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class WoowaController extends Controller
{
	public function webhook(){
		$json = file_get_contents('php://input');
		Log::info('this is sparta');
		Log::info($json);
		$data = json_decode($json);
		Log::info('contact name');
		Log::info($data['contact_name']);
		Log::info('message');
		Log::info($data['message']);
	}
}
