<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JenisAntrian extends Model
{
	public function poli_antrian(){
		return $this->hasMany('App\PoliAntrian');
	}
}
