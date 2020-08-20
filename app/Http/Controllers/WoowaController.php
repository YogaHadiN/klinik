<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class WoowaController extends Controller
{
	public function webhook(){
		Log::info('test webhook');
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		Log::info($data);
	}
}
