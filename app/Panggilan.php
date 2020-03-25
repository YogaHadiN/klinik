<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Panggilan extends Model
{
	public function antrian(){
		return $this->belongsTo('App\Antrian');
	}
}
