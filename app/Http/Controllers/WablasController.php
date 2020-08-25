<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WablasController extends Controller
{
	public function webhook(){
		dd( 'iyes' );
		Log::info( ' ================================== WABLAS ===========================' );
		Log::info($_POST['message']);
		if(isset($_POST['message'])) {
			Log::info( ' ================================== WABLAS ===========================' );
			Log::info($_POST['message']);
		}
	}
}
