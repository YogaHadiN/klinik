<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approve extends Model{

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

}