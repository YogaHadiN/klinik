<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KirimBerkas;
use App\PetugasKirim;
use App\PiutangAsuransi;
use App\Classes\Yoga;
use Input;
use DB;

class KirimBerkasController extends Controller
{
	public function index(){
		$kirim_berkas = KirimBerkas::with('petugas_kirim.staf', 'piutang_asuransi.periksa.asuransi')->get();

		return view('kirim_berkas.index', compact(
			'kirim_berkas'
		));
	}
	
	public function create(){
		return view('kirim_berkas.create');
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
		$kirim_berkas = KirimBerkas::find( $id );
		return view('kirim_berkas.edit', compact('kirim_berkas'));
	}
}
