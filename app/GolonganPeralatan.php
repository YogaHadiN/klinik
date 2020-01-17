<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GolonganPeralatan extends Model
{
	public function KeteranganPenyusutan(){
		return $this->hasMany('App\KeteranganPenyusutan');
	}
}
