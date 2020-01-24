<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Session;
use App\Classes\Yoga;
use App\Formula;
use DB;

class Rak extends Model{


	public static function boot(){
		parent::boot();
		self::deleting(function($rak){
			$merek_ids = [];
			$mereks = $rak->merek;
			foreach ($mereks as $merek) {
				$merek_ids[] = $merek->id;
			}

			$fx = new Formula;
			if ($fx->cekMerekSudahDigunakan($merek_ids)) {
				$query  = "SELECT merek ";
				$query .= "FROM mereks as mr ";
				$query .= "LEFT JOIN raks as rk on rk.id = mr.rak_id ";
				$query .= "WHERE rk.id = '" . $rak->id . "' ";
				$query .= "AND mr.id in ";
				$query .= "(Select merek_id from terapis)";
				$mereks = DB::select($query);
				$pesan = 'Tidak bisa menghapus karena ';
				$pesan .= '<ul>';
				foreach ($mereks as $merek) {
					$pesan .= '<li>' . $merek->merek . '</li>';
				}
				$pesan .= '</ul>';
				$pesan .= 'sudah pernah digunakan, rubah Rak ' . $rak->id . ' bila perlu!';
				Session::flash('pesan', Yoga::gagalFlash($pesan));
				return false;
			}
		});
		self::deleted(function($rak){
			$pesan = 'Rak ' .$rak->id . ' <strong>BERHASIL</strong> dihapus ';
			$mereks = $rak->merek;
			if (Merek::where('rak_id', $rak->id)->delete()) {
				$pesan .= '<br />';
				$pesan .= 'Merek yang menanunginya seperti : ';
				$pesan .= '<ul>';
				foreach ($mereks as $merek) {
					$pesan .= '<li>' . $merek->merek .  '</li>';
				}
				$pesan .= '</ul>';
				$pesan .= 'juga <strong>BERHASIL</strong> dihapus';
			}
			Session::flash('pesan', Yoga::suksesFlash($pesan));
			return true;
		});
	}
	// Add your validation rules here
	public $incrementing = false; 

	// Don't forget to fill this array
	protected $guarded = [];

	public function formula(){
		return $this->belongsTo('Formula');
	}

	public function merek(){
		return $this->hasMany('App\Merek');
	}

	public function getMereksAttribute(){

		$mereks = '';

		foreach ($this->merek as $merek) {
			$mereks .= $merek->merek . '<br>';
		}

		return $mereks;
	}
	public function getKomposisisAttribute(){

		$komposisis = '';

		foreach ($this->formula->komposisi as $komposisi) {
			$komposisis .= $komposisi->generik->generik . ' ' . $komposisi->bobot . '<br>';
		}

		return $komposisis;
	}
	public function getFornasnyaAttribute(){
		$fornas = $this->fornas;

		if ($fornas == '1') {
			return 'fornas';
		} else if($fornas == '2'){
			return 'non fornas';
		}

	}

}
