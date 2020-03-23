<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Poli;

class Poli extends Model
{
    public $incrementing = false; 
	public static function list(){
		return [ null => 'pilih' ] + Poli::pluck('poli', 'id')->all();
	}
	
}
