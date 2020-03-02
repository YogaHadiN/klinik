<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Surat;
use Input;
use App\Yoga;
use DB;
class SuratController extends Controller
{
	public function index(){
		$surats = Surat::all();
		return view('surats.index', compact(
			'surats'
		));
	}
	public function create(){
		return view('surats.create');
	}
	public function edit($id){
		$surat = Surat::find($id);
		return view('surats.edit', compact('surat'));
	}
	public function store(Request $request){
		if ($this->valid( Input::all() )) {
			return $this->valid( Input::all() );
		}
		$surat       = new Surat;
		// Edit disini untuk simpan data
		$surat->save();
		$pesan = Yoga::suksesFlash('Surat baru berhasil dibuat');
		return redirect('surats')->withPesan($pesan);
	}
	public function update($id, Request $request){
		if ($this->valid( Input::all() )) {
			return $this->valid( Input::all() );
		}
		$surat     = Surat::find($id);
		// Edit disini untuk simpan data
		$surat->save();
		$pesan = Yoga::suksesFlash('Surat berhasil diupdate');
		return redirect('surats')->withPesan($pesan);
	}
	public function destroy($id){
		Surat::destroy($id);
		$pesan = Yoga::suksesFlash('Surat berhasil dihapus');
		return redirect('surats')->withPesan($pesan);
	}
	public function import(){
		return 'Not Yet Handled';
		$file      = Input::file('file');
		$file_name = $file->getClientOriginalName();
		$file->move('files', $file_name);
		$results   = Excel::load('files/' . $file_name, function($reader){
			$reader->all();
		})->get();
		$surats     = [];
		$timestamp = date('Y-m-d H:i:s');
		foreach ($results as $result) {
			$surats[] = [
	
				// Do insert here
	
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			];
		}
		Surat::insert($surats);
		$pesan = Yoga::suksesFlash('Import Data Berhasil');
		return redirect()->back()->withPesan($pesan);
	}
	private function valid( $data ){
		$messages = [
			'required' => ':attribute Harus Diisi',
		];
		$rules = [
			'data'           => 'required',
		];
		$validator = \Validator::make($data, $rules, $messages);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
	}
}
