<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


use App\Classes\Yoga;
use App\Asuransi;
use DB;


class Asuransi extends Model{

    public $incrementing = false; 
	// Add your validation rules here
	public static $rules = [
		'nama' => 'required',
		'email' => 'email'
	];

	public function pic(){
		return $this->hasMany('App\Pic');
	}
    public function emails(){
		return $this->morphMany('App\Email', 'emailable');
		/* if(is_null($this->morphMany('App\Email', 'emailable'))){ */
		/* 	return []; */
		/* } else { */
		/* 	return $this->morphMany('App\Email', 'emailable'); */
		/* } */
    }

	// Don't forget to fill this array
	protected $guarded = [];

	public function periksa(){

		return $this->hasMany('App\Periksa');
	}
	public function tarif(){

		return $this->hasMany('App\Tarif');
	}
	public function getUmumstringAttribute(){

		$umums = $this->umum;
		$string = '';

		try {
			$umums = json_decode($umums, true);
			foreach ($umums as $str) {
				$string .= $str . '&#013;';
			}
	    } catch (\Exception $e) {
	        $string = $umums;
	    }

		return Yoga::emptyIfNull($string);
	}
	public function getGigistringAttribute(){

		$umums = $this->gigi;
		$string = '';

		try {

			$umums = json_decode($umums, true);
			foreach ($umums as $str) {
				$string .= $str . '&#013;';
			}

	    } catch (\Exception $e) {

	        $string = $umums;

	    }

		return Yoga::emptyIfNull($string);
	}
	public function getRujukanstringAttribute(){

		$umums = $this->rujukan;
		$string = '';

		try {
			$umums = json_decode($umums, true);
			foreach ($umums as $str) {
				$string .= $str . '&#013;';
			}
	    } catch (\Exception $e) {
	        $string = $umums;
	    }

		return Yoga::emptyIfNull($string);
	}
	public function getPenagihanstringAttribute(){

		$umums = $this->penagihan;
		$string = '';

		try {
			$umums = json_decode($umums, true);
			foreach ($umums as $str) {
				$string .= $str . '&#013;';
			}
	    } catch (\Exception $e) {
	        $string = $umums;
	    }

		return Yoga::emptyIfNull($string);
	}
	public function getBelumAttribute(){
		$query = "SELECT count(px.id) as jumlah from periksas as px join pasiens as p on px.pasien_id = p.id join asuransis as asu on asu.id = px.asuransi_id where px.piutang > 0 and px.piutang > px.piutang_dibayar and px.asuransi_id = '{$this->id}';";
		return DB::select($query)[0]->jumlah;
	}
	public static function list(){
		return  Asuransi::pluck('nama', 'id')->all();
	}
	public function tipe_asuransi(){
		return $this->belongsTo('App\TipeAsuransi', 'tipe_asuransi');
	}
	
    public function telpons(){
		return $this->morphMany('App\Telpon', 'telponable');
    }

    public function berkas(){
        return $this->morphMany('App\Berkas', 'berkasable');
    }
}
