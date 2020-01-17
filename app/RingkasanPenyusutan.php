<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class RingkasanPenyusutan extends Model
{
	public function penyusutan(){
		return $this->hasMany('App\Penyusutan');
	}
    public function getKetjurnalAttribute(){
		return $this->keterangan;

    }
	
    protected $morphClass = 'App\RingkasanPenyusutan';
    public function jurnals(){
        return $this->morphMany('App\JurnalUmum', 'jurnalable');
    }
}
