<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PoliAntrian extends Model
{
	public function poli(){
		return $this->belongsTo('App\Poli');
	}
}
