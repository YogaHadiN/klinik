<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckoutDetail extends Model
{
    public function coa(){
         return $this->belongsTo('App\Coa');
    }
    
    //
}
