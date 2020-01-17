<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alergi extends Model
{
	protected $table = 'alergies';
	public function generik(){
		return $this->belongsTo('App\Generik');
	}
}
