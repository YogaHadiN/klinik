<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CekObat extends Model
{
	protected $dates = ['created_at'];
	public function rak(){
		return $this->belongsTo('App\Rak');
	}
	public function staf(){
		return $this->belongsTo('App\Staf');
	}
}
