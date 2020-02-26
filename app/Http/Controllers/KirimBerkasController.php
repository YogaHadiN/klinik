<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KirimBerkas;
use App\Classes\Yoga;
use Input;
use DB;

class KirimBerkasController extends Controller
{
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
	
	
}
