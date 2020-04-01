<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
	public function berkasable(){
		return $this->morphto();
	}
}
