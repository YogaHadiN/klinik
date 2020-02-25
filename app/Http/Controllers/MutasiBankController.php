<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper;
use Log;

class MutasiBankController extends Controller
{
    //
	//

	public function info(){
		// Get Account information
		$result = Helper::GetAccount();
		$data1 = json_decode($result);

		$date_from = '2020-02-01';
		$date_to = '2020-02-29';
		$acc_id = 1270;

		/// Get Account Statement
		$result = Helper::GetAccountStatement($acc_id,$date_from,$date_to);
		$data2 = json_decode($result);
		dd( compact(
				'data1', 'data2', 'date_to', 'date_from'
		));
	}
	public function mootaCallback(){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://app.moota.co/api/v1/balance');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'Authorization: Bearer ' . env('MOOTA_TOKEN')
		]);
		$response = curl_exec($curl);

		dd($response);
	}
	
	
}
