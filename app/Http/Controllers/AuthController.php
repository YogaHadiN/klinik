<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Input; 
use Auth;
use App\User;
use App\Classes\Yoga;



class AuthController extends Controller {


	public function index()
	{
		return view('login');
	}

	public function login()
	{
		$creds = array(
			'email'    => Input::get('email'),
			'password' => Input::get('password')
		);

		if( Auth::attempt($creds) ){
			$id = Auth::id();
			$nama = User::find($id)->username;

			return redirect('laporans')->withPesan(Yoga::suksesFlash('Selamat Datang <strong>' . $nama . '</strong>'));
		}else {
			return redirect('login')
			->withInput()
			->withPesan('Kombinasi email dan password Tidak Benar');
		}

	}

	public function logout(){

		Auth::logout();

		return redirect('/');

	}

}
