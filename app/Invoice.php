<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	public function piutang_asuransi(){
		return $this->hasMany('App\PiutangAsuransi');
	}
}
