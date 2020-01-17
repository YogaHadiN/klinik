<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BahanBangunan extends Model
{
	protected $guarded = [];
	protected $dates = ['tanggal_renovasi_selesai'];
	public function fakturBelanja(){
		return $this->belongsTo('App\FakturBelanja');
	}
    protected $morphClass = 'App\BahanBangunan';
    public function susuts(){
        return $this->morphMany('App\Penyusutan', 'susutable');
    }
	public function getSudahSusutAttribute(){
		$susuts = $this->susuts;
		$nilai = 0;
		foreach ($susuts as $s) {
			$nilai += $s->nilai;
		}
		return $nilai;
	}
}
