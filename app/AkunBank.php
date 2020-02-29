<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AkunBank extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;  // You most probably want this too
}
