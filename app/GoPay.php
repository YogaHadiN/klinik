<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoPay extends Model
{
	protected $dates = ['tanggal'];
	public function pengeluaran(){
		return $this->belongsTo('App\Pengeluaran');
	}
}
