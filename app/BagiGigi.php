<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BagiGigi extends Model
{
	protected $dates = ['mulai', 'akhir', 'tanggal_dibayar'];
	public function petugas(){
		return $this->belongsTo('App\Staf', 'petugas_id');
	}
	public function getPeriodeAttribute(){
		return $this->tanggal_mulai->format('d-m-Y') . ' s/d ' . $this->tanggal_akhir->format('d-m-Y');
	}
    public function pph21s(){
        return $this->morphOne('App\Pph21', 'pph21able');
    }
}
