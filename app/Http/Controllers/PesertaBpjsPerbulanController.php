<?php

namespace App\Http\Controllers;

use App\PesertaBpjsPerbulan;
use App\Rules\ExceRule;
use Input;
use Auth;
use App\Classes\Yoga;
use DB;
use Illuminate\Http\Request;

class PesertaBpjsPerbulanController extends Controller
{
    public function index(){
        $peserta_bpjs_perbulans = PesertaBpjsPerbulan::latest()->get();
        return view('peserta_bpjs_perbulans.index', compact(
            'peserta_bpjs_perbulans'
        ));
    }
    public function create(){
        return view('peserta_bpjs_perbulans.create');
    }
    public function edit($id){
        $peserta_bpjs_perbulan = PesertaBpjsPerbulan::find($id);
        return view('peserta_bpjs_perbulans.edit', compact('peserta_bpjs_perbulan'));
    }
    public function store(Request $request){
        /* dd(Input::all()); */ 
        if ($this->valid( Input::all() )) {
            return $this->valid( Input::all() );
        }
        $peserta_bpjs_perbulan = new PesertaBpjsPerbulan;
        $peserta_bpjs_perbulan = $this->processData($peserta_bpjs_perbulan);

        $pesan = Yoga::suksesFlash('PesertaBpjsPerbulan baru berhasil dibuat');
        return redirect('peserta_bpjs_perbulans')->withPesan($pesan);
    }
    public function update($id, Request $request){
        if ($this->valid( Input::all() )) {
            return $this->valid( Input::all() );
        }
        $peserta_bpjs_perbulan = PesertaBpjsPerbulan::find($id);
        $peserta_bpjs_perbulan = $this->processData($peserta_bpjs_perbulan);

        $pesan = Yoga::suksesFlash('PesertaBpjsPerbulan berhasil diupdate');
        return redirect('peserta_bpjs_perbulans')->withPesan($pesan);
    }
    public function destroy($id){
        PesertaBpjsPerbulan::destroy($id);
        $pesan = Yoga::suksesFlash('PesertaBpjsPerbulan berhasil dihapus');
        return redirect('peserta_bpjs_perbulans')->withPesan($pesan);
    }

    public function processData($peserta_bpjs_perbulan){
        $peserta_bpjs_perbulan->nama_file = $this->fileUpload('nama_file');
        $peserta_bpjs_perbulan->save();

        return $peserta_bpjs_perbulan;
    }
    public function import(){
        return 'Not Yet Handled';
        $file      = Input::file('file');
        $file_name = $file->getClientOriginalName();
        $file->move('files', $file_name);
        $results   = Excel::load('files/' . $file_name, function($reader){
            $reader->all();
        })->get();
        $peserta_bpjs_perbulans     = [];
        $timestamp = date('Y-m-d H:i:s');
        foreach ($results as $result) {
            $peserta_bpjs_perbulans[] = [

                // Do insert here

                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }
        PesertaBpjsPerbulan::insert($peserta_bpjs_perbulans);
        $pesan = Yoga::suksesFlash('Import Data Berhasil');
        return redirect()->back()->withPesan($pesan);
    }
    private function valid( $data ){
        $messages = [
            'required' => ':attribute Harus Diisi',
        ];
        $rules = [
            'nama_file'           =>[
                'required',
                new ExceRule( Input::file('nama_file') )
            ]
        ];
        $validator = \Validator::make($data, $rules, $messages);
        
        if ($validator->fails())
        {
            return \Redirect::back()->withErrors($validator)->withInput();
        }
    }
    /**
     * undocumented function
     *
     * @return void
     */
    private function fileUpload($fieldName)
    {
		if(Input::hasFile($fieldName)) {

			$upload_cover = Input::file($fieldName);
			//mengambil extension
			$extension = $upload_cover->getClientOriginalExtension();

			//membuat nama file random + extension
			$filename =	 'file_'. time() . '_' . Auth::id() . '.' . $extension;

			//menyimpan bpjs_image ke folder public/img
			$destination_path = public_path() . DIRECTORY_SEPARATOR . 'peserta_bpjs';

			// Mengambil file yang di upload
			$upload_cover->move($destination_path , $filename);
			
			//mengisi field bpjs_image di book dengan filename yang baru dibuat
			return $filename;
			
		}
    }
    
}
