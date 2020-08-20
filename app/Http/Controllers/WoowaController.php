<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class WoowaController extends Controller
{
	public function webhookc(){
		Log::info("pek cun");
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		file_put_contents("listen.txt", print_r($data,1));
	}
}
