<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\MetodeBayar;

class MetodeBayar extends Model
{
	public static function list(){
		return array('' => '- Pilih Metode Bayar -') + MetodeBayar::pluck('metode_bayar', 'id')->all();
	}
}
