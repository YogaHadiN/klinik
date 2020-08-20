<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class WoowaController extends Controller
{
	public function webhook(){
		Log::info('-=-=-=-=-=');
		$json = file_get_contents('php://input');
		Log::info($json);
		Log::info('-=-=-=-=-=');
		$data = json_decode($json);
	}
}
