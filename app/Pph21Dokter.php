<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pph21Dokter extends Model
{
	public function staf(){
		return $this->belongsTo('App\Staf');
	}
	public function getPenghasilanLainAttribute(){
		if ( $this->ada_penghasilan_lain == '1' ) {
			return 'Ada';
		} else {
			return 'Tidak Ada';
		}
	}
}
