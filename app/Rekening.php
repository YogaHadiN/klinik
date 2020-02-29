<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;  // You most probably want this too
	protected $dates = [
		'tanggal'
	];
}
