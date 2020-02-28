<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Classes\Yoga;
use App\AntrianPoli;
use App\PengantarPasien;
use App\Pasien;
use Input;

class AntrianPolisAjaxController extends Controller
{
    //
	public function getProlanis(){
		$pasien_id = Input::get('pasien_id');
		$pasien    = Pasien::find($pasien_id);
		/* $pasien    = Pasien::with('periksa')->where('id', $pasien_id)->first(); // ini kenapa 530MB memory nya? padahal data periksa cuma ada 26 */
		return Yoga::golonganProlanis($pasien);
	}
}
