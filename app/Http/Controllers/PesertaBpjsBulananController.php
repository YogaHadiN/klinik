<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PesertaBpjsBulanan;
use Input;
use App\Yoga;
use DB;

class PesertaBpjsBulananController extends Controller
{
	public function index(){
		$peserta_bpjs_bulanans = PesertaBpjsBulanan::all();
		return view('peserta_bpjs_bulanans.index', compact(
			'peserta_bpjs_bulanans'
		));
	}
	public function create(){
		return view('peserta_bpjs_bulanans.create');
	}
	public function edit($id){
		$peseta_bpjs_bulanan = PesertaBpjsBulanan::find($id);
		return view('peserta_bpjs_bulanans.edit', compact('peseta_bpjs_bulanan'));
	}
	public function store(Request $request){
		if ($this->valid( Input::all() )) {
			return $this->valid( Input::all() );
		}
		$peseta_bpjs_bulanan = new PesertaBpjsBulanan;
		$peseta_bpjs_bulanan = $this->processData($peseta_bpjs_bulanan);

		$pesan = Yoga::suksesFlash('PesertaBpjsBulanan baru berhasil dibuat');
		return redirect('peserta_bpjs_bulanans')->withPesan($pesan);
	}
	public function update($id, Request $request){
		if ($this->valid( Input::all() )) {
			return $this->valid( Input::all() );
		}
		$peseta_bpjs_bulanan = PesertaBpjsBulanan::find($id);
		$peseta_bpjs_bulanan = $this->processData($peseta_bpjs_bulanan);

		$pesan = Yoga::suksesFlash('PesertaBpjsBulanan berhasil diupdate');
		return redirect('peserta_bpjs_bulanans')->withPesan($pesan);
	}
	public function destroy($id){
		PesertaBpjsBulanan::destroy($id);
		$pesan = Yoga::suksesFlash('PesertaBpjsBulanan berhasil dihapus');
		return redirect('peserta_bpjs_bulanans')->withPesan($pesan);
	}

	public function processData($peseta_bpjs_bulanan){
		dd( 'processData belum diatur' );
		$peseta_bpjs_bulanan = $this->peseta_bpjs_bulanan;
		$peseta_bpjs_bulanan->save();

		return $peseta_bpjs_bulanan;
	}
	public function import(){
		return 'Not Yet Handled';
		$file      = Input::file('file');
		$file_name = $file->getClientOriginalName();
		$file->move('files', $file_name);
		$results   = Excel::load('files/' . $file_name, function($reader){
			$reader->all();
		})->get();
		$peserta_bpjs_bulanans     = [];
		$timestamp = date('Y-m-d H:i:s');
		foreach ($results as $result) {
			$peserta_bpjs_bulanans[] = [

				// Do insert here

				'created_at' => $timestamp,
				'updated_at' => $timestamp
			];
		}
		PesertaBpjsBulanan::insert($peserta_bpjs_bulanans);
		$pesan = Yoga::suksesFlash('Import Data Berhasil');
		return redirect()->back()->withPesan($pesan);
	}
	private function valid( $data ){
		dd( 'validasi belum diatur' );
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
	private function fileUpload(){
		if(Input::hasFile($fieldName)) {

			$upload_cover = Input::file($fieldName);
			//mengambil extension
			$extension = $upload_cover->getClientOriginalExtension();

			$upload_cover = Image::make($upload_cover);
			$upload_cover->resize(1000, null, function ($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			});
			//membuat nama file random + extension
			$filename =	 $pre . $staf->id . '.' . $extension;

			//menyimpan bpjs_image ke folder public/img
			$destination_path = public_path() . DIRECTORY_SEPARATOR . 'img/staf';

			// Mengambil file yang di upload
			$upload_cover->save($destination_path . '/' . $filename);
			
			//mengisi field bpjs_image di book dengan filename yang baru dibuat
			return 'img/staf/'. $filename;
			
		} else {
			return $staf->$fieldName;
		}
	}
}

