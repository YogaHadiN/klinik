<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BelanjaPeralatan extends Model
{
	protected $guarded = ['id'];
	public function staf(){
		return $this->belongsTo('App\Staf');
	}
	public function fakturBelanja(){
		return $this->belongsTo('App\FakturBelanja');
	}
    protected $morphClass = 'App\BelanjaPeralatan';
	
    public function susuts(){
        return $this->morphMany('App\Penyusutan', 'susutable');
    }
    public function getPenyusutanTotalAttribute(){
		$penyusutans = Penyusutan::where('susutable_id', $this->id)
								->where('susutable_type', 'App\BelanjaPeralatan')
								->get();
		$nilai = 0;
		foreach ($penyusutans as $p) {
			$nilai += $p->nilai;
		}
		return $nilai;
    }
    public function getSudahSusutAttribute(){
		$penyusutans = $this->susuts;
		$nilai = 0;
		foreach ($penyusutans as $p) {
			$nilai += $p->nilai;
		}
		return $nilai;
    }
	
}
