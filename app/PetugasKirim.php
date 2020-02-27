<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PetugasKirim extends Model
{
	public function staf(){
		return $this->belongsTo('App\Staf');
	}
	public function role_pengiriman(){
		return $this->belongsTo('App\RolePengiriman');
	}
}
