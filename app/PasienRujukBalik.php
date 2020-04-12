<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasienRujukBalik extends Model
{
	public function pasien(){
		return $this->belongsTo('App\Pasien');
	}
}
