<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeteranganPenyusutan extends Model
{
	public function golonganPeralatan(){
		return $this->belongsTo('App\GolonganPeralatan');
	}
}
