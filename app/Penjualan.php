<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model{
	
	protected $fillable = [];
	protected $guarded = [];

    public function merek(){
         return $this->belongsTo('App\Merek');
    }
    
    public function notaJual(){
         return $this->belongsTo('App\NotaJual');
    }
	
}
