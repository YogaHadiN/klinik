<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;
use Image;
use App\Pengeluaran;
use App\JurnalUmum;
use App\Http\Controllers\PengeluaransController;
use App\Belanja;
use App\KirimBerkas;
use App\Staf;
use App\RolePengiriman;
use App\PetugasKirim;
use App\PiutangAsuransi;
use App\Classes\Yoga;
use Input;
use DB;

class KirimBerkasController extends Controller
{
	public function __construct()
	 {
		 $this->middleware('protect', ['only' => [
			 'destroy',
			 'inputNota',
			 'inputNotaPost',
			 'update'
		 ]]);
	 }
	public function index(){
		$kirim_berkas = KirimBerkas::with('petugas_kirim.staf', 'piutang_asuransi.periksa.asuransi')->get();

		return view('kirim_berkas.index', compact(
			'kirim_berkas'
		));
	}
	
	public function create(){
		$staf_list = Staf::pluck('nama', 'id')->all();
		$role_pengiriman_list = RolePengiriman::list();
		return view('kirim_berkas.create', compact('staf_list', 'role_pengiriman_list'));
	}
	public function cariPiutang(){
		$date_to     = Yoga::datePrep(Input::get('date_to'));
		$date_from     = Yoga::datePrep(Input::get('date_from'));
		$asuransi_id = Input::get('asuransi_id');
		$query  = "SELECT ";
		$query .= "pa.id as piutang_id, ";
		$query .= "pa.piutang as piutang, ";
		$query .= "pa.sudah_dibayar as sudah_dibayar, ";
		$query .= "px.id as periksa_id, ";
		$query .= "ps.nama as nama_pasien, ";
		$query .= "asu.nama as nama_asuransi ";
		$query .= "FROM piutang_asuransis as pa ";
		$query .= "JOIN periksas as px on px.id = pa.periksa_id ";
		$query .= "JOIN pasiens as ps on ps.id = px.pasien_id ";
		$query .= "JOIN asuransis as asu on asu.id = px.asuransi_id ";
		$query .= "WHERE px.tanggal between '$date_from' and '$date_to' ";
		$query .= "AND px.asuransi_id = '$asuransi_id';";
		$data = DB::select($query);

		return $data;
	}
	public function store(){
		DB::beginTransaction();
		try {
			$kirim_berkas          = new KirimBerkas;
			$kirim_berkas->tanggal = Yoga::datePrep(Input::get('tanggal'));
			$kirim_berkas->save();

			$staf_ids            = Input::get('staf_id');
			$role_pengiriman_ids = Input::get('role_pengiriman_id');

			foreach ($staf_ids as $k => $staf_id) {
				$petugas_kirim                     = new PetugasKirim;
				$petugas_kirim->staf_id            = $staf_id;
				$petugas_kirim->role_pengiriman_id = $role_pengiriman_ids[$k];
				$petugas_kirim->kirim_berkas_id    = $kirim_berkas->id;
				$petugas_kirim->save();
			}

			$piutang_tercatat = Input::get('piutang_tercatat');
			$piutang_tercatat = json_decode($piutang_tercatat, true);

			$piutang_ids = [];

			foreach ($piutang_tercatat as $piutang) {
				$piutang_ids[] = $piutang['piutang_id'];
			}

			PiutangAsuransi::whereIn('id', $piutang_ids)->update([
				'kirim_berkas_id' => $kirim_berkas->id
			]);
			
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			throw $e;
		}
		$pesan = Yoga::suksesFlash('Form Kirim Berkas Berhasil Dibuat');
		return redirect('kirim_berkas')->withPesan($pesan);
	}
	public function edit($id){
		$kirim_berkas = KirimBerkas::with('piutang_asuransi.periksa.asuransi', 'piutang_asuransi.periksa.pasien')->where('id', $id )->first();
		$staf_list = Staf::pluck('nama', 'id')->all();
		$role_pengiriman_list = RolePengiriman::list();
		return view('kirim_berkas.edit', compact('staf_list', 'role_pengiriman_list','kirim_berkas'));
	}
	public function update($id){
		DB::beginTransaction();
		try {
			$kirim_berkas          = KirimBerkas::find($id);
			$kirim_berkas->tanggal = Yoga::datePrep(Input::get('tanggal'));
			$kirim_berkas->save();

			$staf_ids            = Input::get('staf_id');
			$role_pengiriman_ids = Input::get('role_pengiriman_id');

			PetugasKirim::where('kirim_berkas_id', $id)->delete();

			foreach ($staf_ids as $k => $staf_id) {
				$petugas_kirim                     = new PetugasKirim;
				$petugas_kirim->staf_id            = $staf_id;
				$petugas_kirim->role_pengiriman_id = $role_pengiriman_ids[$k];
				$petugas_kirim->kirim_berkas_id    = $kirim_berkas->id;
				$petugas_kirim->save();
			}

			PiutangAsuransi::where('kirim_berkas_id', $id)->update([
				'kirim_berkas_id' => null
			]);

			$piutang_tercatat = Input::get('piutang_tercatat');
			$piutang_tercatat = json_decode($piutang_tercatat, true);

			$piutang_ids = [];
			foreach ($piutang_tercatat as $piutang) {
				$piutang_ids[] = $piutang['piutang_id'];
			}
			PiutangAsuransi::whereIn('id', $piutang_ids)->update([
				'kirim_berkas_id' => $kirim_berkas->id
			]);
			
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			throw $e;
		}
		$pesan = Yoga::suksesFlash('Form Kirim Berkas Berhasil Diupdate');
		return redirect('kirim_berkas')->withPesan($pesan);
	}
	public function inputNota($id){
		$kirim_berkas = KirimBerkas::find( $id );
		$suppliers    = Supplier::all();
		$stafs        = Yoga::stafList();
		$sumber_uang  = Yoga::sumberuang();
		$belanjaList  = [ null => '- Jenis Belanja -']  + Belanja::pluck('belanja', 'id')->all();

		return view('suppliers.belanja_bukan_obat', compact(
			'suppliers', 
			'kirim_berkas', 
			'stafs', 
			'belanjaList', 
			'sumber_uang'
		));
	}
	public function inputNotaPost($id){
		$messages          = array(
			'required'    => ':attribute harus diisi terlebih dahulu',
		);
		$rules             = [
			'staf_id'      => 'required',
			'supplier_id'  => 'required',
			'nilai'        => 'required',
			'faktur_image' => 'required',
			'foto_berkas_dan_bukti' => 'required',
			'tanggal'      => 'required|date_format:d-m-Y',
			'sumber_uang'  => 'required',
			'keterangan'   => 'required'
		];
		$validator         = \Validator::make($data = Input::all(), $rules, $messages);
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator->messages())->withInput();
		}
		$staf_id           = Input::get('staf_id');
		$supplier_id       = Input::get('supplier_id');
		$nilai             = Yoga::clean( Input::get('nilai') );
		$tanggal           = Input::get('tanggal');
		$keterangan        = Input::get('keterangan');

		$peng                 = new Pengeluaran;
		$peng->staf_id        = $staf_id;
		$peng->supplier_id    = $supplier_id;
		$peng->nilai          = $nilai;
		$peng->tanggal        = Yoga::datePrep( $tanggal );
		$peng->sumber_uang_id = Input::get('sumber_uang');
		$peng->keterangan     = $keterangan;
		$peng->save();
		$peng->faktur_image   = $this->imageUpload('faktur', 'faktur_image', $peng->id,'img/belanja/lain');
		$confirm              = $peng->save();


		$kirim_berkas                        = KirimBerkas::find($id);
		$kirim_berkas->foto_berkas_dan_bukti = $this->imageUpload('faktur', 'foto_berkas_dan_bukti', $id, 'img/foto_berkas_dan_bukti');
		$kirim_berkas->pengeluaran_id        = $peng->id;
		$kirim_berkas->save();



		if ($confirm) {
			$jurnals = [];
			$timestamp = $peng->created_at;
			$jurnals[] = [
				'jurnalable_id'   => $peng->id,
				'jurnalable_type' => 'App\Pengeluaran',
				'debit'           => 1,
				'coa_id'           => null,
				'nilai'           => $peng->nilai,
				'created_at'      => $timestamp,
				'updated_at'      => $timestamp
			];

			$jurnals[] = [
				'jurnalable_id'   => $peng->id,
				'jurnalable_type' => 'App\Pengeluaran',
				'coa_id'          => Input::get('sumber_uang'),
				'debit'           => 0,
				'nilai'           => $peng->nilai,
				'created_at'      => $timestamp,
				'updated_at'      => $timestamp
			];
			JurnalUmum::insert($jurnals);
		}
		$nama_supplier = Supplier::find($supplier_id)->nama;
		return redirect('kirim_berkas')->withPesan(Yoga::suksesFlash('Transaksi Uang Keluar kepada ' . $nama_supplier . ' senilai <span class=uang>' . $nilai .'</span> berhasil dilakukan'))->withPrint($peng->id);

	}

	private function imageUpload($pre, $fieldName, $id, $path){
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
			$filename =	 $pre . $id . '.' . $extension;
			//
			//menyimpan bpjs_image ke folder public/img
			$destination_path = public_path() . DIRECTORY_SEPARATOR .$path;

			// Mengambil file yang di upload
			$upload_cover->save($destination_path . '/' . $filename);
			
			//mengisi field bpjs_image di book dengan filename yang baru dibuat
			return $filename;
			
		} else {
			return null;
		}
	}
	public function destroy($id){
		PiutangAsuransi::where('kirim_berkas_id', $id)->update([
			'kirim_berkas_id' => null
		]);
		PetugasKirim::where('kirim_berkas_id', $id)->delete();
		KirimBerkas::destroy($id);
		$pesan = Yoga::suksesFlash('Berkas Berhasil dihapus');
		return redirect(
			'kirim_berkas'
		)->withPesan($pesan);
	}
}
