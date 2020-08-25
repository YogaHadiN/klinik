<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WablasController extends Controller
{
	public function webhook(){
		Log::info( ' ================================== INFO ===========================' );
		Log::info($_POST['message']);
		Log::info( ' ================================== INFO ===========================' );
		if(isset($_POST['message'])) {
			Log::info( ' ================================== WABLAS ===========================' );
			Log::info($_POST['message']);
			Log::info( ' ================================== WABLAS ===========================' );
		}
	}
}
