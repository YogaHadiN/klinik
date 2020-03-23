<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
	public function jenis_antrian(){
		return $this->belongsTo('App\JenisAntrian');
	}

	public function antriable(){
		return $this->morphto()->withDefault();
	}

	public function getNomorAntrianAttribute(){
		return $this->jenis_antrian->prefix . $this->nomor;
	}
}
