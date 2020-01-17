<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class BelanjapluckController extends Controller
{
	public function index(){
		return view('belanjalist.index');
	}
	
}
