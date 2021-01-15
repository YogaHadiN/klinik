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
        $peserta_bpjs_bulanan = PesertaBpjsBulanan::find($id);
        return view('peserta_bpjs_bulanans.edit', compact('peserta_bpjs_bulanan'));
    }
    public function store(Request $request){
        dd(Input::all()); 
        if ($this->valid( Input::all() )) {
            return $this->valid( Input::all() );
        }
        $peserta_bpjs_bulanan       = new PesertaBpjsBulanan;
        // Edit disini untuk simpan data
        $peserta_bpjs_bulanan->save();
        $pesan = Yoga::suksesFlash('PesertaBpjsBulanan baru berhasil dibuat');
        return redirect('peserta_bpjs_bulanans')->withPesan($pesan);
    }
    public function update($id, Request $request){
        if ($this->valid( Input::all() )) {
            return $this->valid( Input::all() );
        }
        $peserta_bpjs_bulanan     = PesertaBpjsBulanan::find($id);
        // Edit disini untuk simpan data
        $peserta_bpjs_bulanan->save();
        $pesan = Yoga::suksesFlash('PesertaBpjsBulanan berhasil diupdate');
        return redirect('peserta_bpjs_bulanans')->withPesan($pesan);
    }
    public function destroy($id){
        PesertaBpjsBulanan::destroy($id);
        $pesan = Yoga::suksesFlash('PesertaBpjsBulanan berhasil dihapus');
        return redirect('peserta_bpjs_bulanans')->withPesan($pesan);
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
