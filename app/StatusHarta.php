<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\StatusHarta;

class StatusHarta extends Model
{
	public static function list(){
		return[ null => 'pilih' ] + StatusHarta::pluck('status_harta', 'id')->all();
	}
}
