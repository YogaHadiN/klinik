<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dispensing extends Model{

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $guarded = [];


	public function merek(){
		return $this->belongsTo('App\Merek');
	}
	public function rak(){
		return $this->belongsTo('App\Rak');
	}
	public function terapi(){
		return $this->belongsTo('App\Terapi');
	}

	public function pembelian(){
		return $this->belongsTo('App\Pembelian');
	}

	public function dispensable(){
		return $this->morphTo();
	}

}
