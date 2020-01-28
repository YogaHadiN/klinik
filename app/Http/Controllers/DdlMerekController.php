<?php

namespace App\Http\Controllers;

use Input;

use App\Http\Requests;

use App\Asuransi;
use App\Rak;
use App\Yoga;
use App\Dose;
use App\BeratBadan;
use DB;



class DdlMerekController extends Controller
{

	
	public function alloption(){

		$asuransi_id = Input::get('asuransi_id');

		$query = 'SELECT f.tidak_dipuyer as tidak_dipuyer, ';
		$query .= 'm.id as merek_id, ';
		$query .= 'f.sediaan as sediaan, ';
		$query .= 'r.id as rak_id, ';
		$query .= 'f.id as formula_id, ';
		$query .= 'm.merek, ';
		$query .= 'r.harga_beli, ';
		$query .= 'f.aturan_minum_id as aturan_minum_id, ';
		$query .= 'f.peringatan as peringatan, ';
		$query .= 'r.fornas as fornas, ';
		$query .= 'g.generik as generik, ';
		$query .= 'k.bobot as bobot, ';
		$query .= 'r.harga_jual as harga_jual ';
		$query .= 'FROM mereks as m ';
		$query .= 'JOIN raks as r on r.id = m.rak_id ';
		$query .= 'JOIN formulas as f on f.id = r.formula_id ';
		$query .= 'JOIN komposisis as k on f.id = k.formula_id ';
		$query .= 'JOIN generiks as g on g.id = k.generik_id ';
		$query .= 'ORDER BY m.id ASC';
		/* dd($query); */
		$data =  DB::select($query);
		/* dd($data); */
		$asuransi    = Asuransi::find($asuransi_id);
		$asuransi_id = Input::get('asuransi_id');
		$non_fornas = [];

		$mereks = [];

		$i = 0;
		foreach ($data as $dt) {
			$mereks[$dt->merek_id]['merek_id']        = $dt->merek_id;
			$mereks[$dt->merek_id]['sediaan']         = $dt->sediaan;
			$mereks[$dt->merek_id]['rak_id']          = $dt->rak_id;
			$mereks[$dt->merek_id]['formula_id']      = $dt->formula_id;
			$mereks[$dt->merek_id]['merek']           = $dt->merek;
			$mereks[$dt->merek_id]['harga_beli']      = $dt->harga_beli;
			$mereks[$dt->merek_id]['aturan_minum_id'] = $dt->aturan_minum_id;
			$mereks[$dt->merek_id]['peringatan']      = $dt->peringatan;
			$mereks[$dt->merek_id]['fornas']          = $dt->fornas;
			$mereks[$dt->merek_id]['harga_jual']      = $dt->harga_jual;
			$mereks[$dt->merek_id]['ID_TERAPI']       = strval($i);
			$mereks[$dt->merek_id]['komposisi'][]     = $dt->generik . ' ' . $dt->bobot;
			$i++;	
		}
		$datas = [];
		foreach ($mereks as $mr) {
			$datas[] = $mr;
		}
		/* dd($datas); */
/* 		foreach ($data as $dt) { */
/* 			$temp = '<br />'; */
/* 			$komposisis = DB::select("SELECT generik, bobot FROM komposisis as k INNER JOIN generiks as g on g.id = k.generik_id WHERE k.formula_id = '" . $dt->formula_id . "'"); */
/* 			foreach ($komposisis as $komposisi) { */
/* 				$temp .= $komposisi->generik . ' ' . $komposisi->bobot . '<br />'; */
/* 			} */
/* 			if ($asuransi->tipe_asuransi == '4' || $asuransi->tipe_asuransi == '5') { //tipe asuransi 4 = bpjs tipe asuransi 5 = flat */
/* 				$rak            = Rak::where('formula_id', $dt->formula_id)->orderBy('harga_beli', 'asc')->first(); */
/* 				$dt->harga_jual = $rak->harga_jual; */
/* 				$dt->fornas     = $rak->fornas; */
/* 			} */
/* 			$dt->ID_TERAPI = strval($i); */
/* 			$dt->komposisi = $temp; */
/* 			$i++; */	

/* 			$number = '1'; */
/* 			if ($dt->fornas == $number) { */
/* 				$non_fornas[] = $dt->merek; */
/* 			} */
/* 		} */
/* 		dd($non_fornas); */
		return json_encode($datas);
	}
	public function alloption2(){

		$data =  DB::select('SELECT f.tidak_dipuyer as tidak_dipuyer, r.harga_jual as harga_jual, m.id as merek_id, f.sediaan as sediaan, r.id as rak_id, f.id as formula_id, m.merek, r.harga_beli, f.aturan_minum_id, f.peringatan as peringatan, r.fornas as fornas FROM mereks as m JOIN raks as r on r.id = m.rak_id join formulas as f on f.id = r.formula_id ORDER BY m.id ASC');
		$i = 0;
		$bb = Input::get('bb');

		$berat_badan_id = Yoga::beratBadanId($bb);
		$asuransi_id = Input::get('asuransi_id');
		$asuransi = Asuransi::find($asuransi_id);

		foreach ($data as $dt) {
			$temp = '<br />';
			$komposisis = DB::select("SELECT generik, bobot FROM komposisis as k INNER JOIN generiks as g on g.id = k.generik_id WHERE k.formula_id = '" . $dt->formula_id . "' ORDER BY k.formula_id ASC");
			foreach ($komposisis as $komposisi) {
				$temp .= $komposisi->generik . ' ' . $komposisi->bobot . '<br />';
			}
			if ($asuransi->tipe_asuransi == '4' || $asuransi->tipe_asuransi == '5') {
				$rak = Rak::where('formula_id', $dt->formula_id)->orderBy('harga_beli', 'asc')->first();
				$dt->harga_jual = $rak->harga_jual;
				$dt->fornas = $rak->fornas;
			}
			$doses = Dose::where('formula_id', $dt->formula_id)->where('berat_badan_id', $berat_badan_id)->first(['jumlah', 'jumlah_bpjs', 'jumlah_puyer_add', 'signa_id']);	

			$dt->ID_TERAPI = strval($i);
			$dt->komposisi = $temp;
			$dt->doses = $doses;

			// return json_encode($dt);
			$i++;	
		}

		$temp = [

			'berat_badan' => BeratBadan::find($berat_badan_id)->berat_badan,
			'temp' => $data

		];
		return json_encode($temp);
	}

	public function optionpuyer(){
		$data =  DB::select("SELECT f.tidak_dipuyer as tidak_dipuyer, r.harga_jual as harga_jual, f.aturan_minum_id as aturan_minum_id, m.id as merek_id, f.sediaan as sediaan, r.id as rak_id, f.id as formula_id, m.merek, r.harga_beli, f.peringatan as peringatan, r.fornas as fornas FROM mereks as m JOIN raks as r on r.id = m.rak_id join formulas as f on f.id = r.formula_id WHERE f.sediaan = 'capsul' or f.sediaan = 'tablet' ORDER BY m.id ASC");
		$i = 0;
		$asuransi_id = Input::get('asuransi_id');
		$asuransi = Asuransi::find($asuransi_id);

		foreach ($data as $dt) {
			$temp = '<br />';
			$komposisis = DB::select("SELECT generik, bobot FROM komposisis as k INNER JOIN generiks as g on g.id = k.generik_id WHERE k.formula_id = '" . $dt->formula_id . "'");
			foreach ($komposisis as $komposisi) {
				$temp .= $komposisi->generik . ' ' . $komposisi->bobot . '<br />';
			}
			if ($asuransi->tipe_asuransi == '4' || $asuransi->tipe_asuransi == '5') {
				$rak = Rak::where('formula_id', $dt->formula_id)->orderBy('harga_beli', 'asc')->first();
				$dt->harga_jual = $rak->harga_jual;
				$dt->fornas = $rak->fornas;
			}
			$dt->ID_TERAPI = strval($i);
			$dt->komposisi = $temp;
			$i++;		
		}
		return json_encode($data);
	}
	public function optionsyrup(){
		$data =  DB::select("SELECT f.tidak_dipuyer as tidak_dipuyer, r.harga_jual as harga_jual, m.id as merek_id, f.aturan_minum_id as aturan_minum_id, f.sediaan as sediaan, r.id as rak_id, f.id as formula_id, m.merek, r.harga_beli, f.peringatan as peringatan, r.fornas as fornas FROM mereks as m JOIN raks as r on r.id = m.rak_id join formulas as f on f.id = r.formula_id WHERE f.sediaan like '%syrup%' ORDER BY m.id ASC");
		$i = 0;
		$asuransi_id = Input::get('asuransi_id');
		$asuransi = Asuransi::find($asuransi_id);
		foreach ($data as $dt) {
			$temp = '<br />';
			$komposisis = DB::select("SELECT generik, bobot FROM komposisis as k INNER JOIN generiks as g on g.id = k.generik_id WHERE k.formula_id = '" . $dt->formula_id . "'");
			foreach ($komposisis as $komposisi) {
				$temp .= $komposisi->generik . ' ' . $komposisi->bobot . '<br />';
			}
			if ($asuransi->tipe_asuransi == '4' || $asuransi->tipe_asuransi == '5') {
				$rak = Rak::where('formula_id', $dt->formula_id)->orderBy('harga_beli', 'asc')->first();
				$dt->harga_jual = $rak->harga_jual;
				$dt->fornas = $rak->fornas;
			}
			$dt->ID_TERAPI = strval($i);
			$dt->komposisi = $temp;
			$i++;	
		}
		// return Komposisi::where('formula_id', $data[0]->formula_id)->get();
		return json_encode($data);
	}

}
