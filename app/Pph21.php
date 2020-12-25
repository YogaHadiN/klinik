<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pph21 extends Model
{
	protected $guarded = ['id'];
	public function jurnalable(){
		return $this->morphto();
	}

}
