<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InputHarta extends Model
{
	protected $dates= ['tanggal_beli'];
	public function statusHarta(){
		return $this->belongsTo('App\StatusHarta');
	}
    public function susuts(){
        return $this->morphMany('App\Penyusutan', 'susutable');
    }
}
