<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsappRegistration extends Model
{
	public function getNamaPembayaranAttribute(){
		if ( $this->pembayaran == 'a' ) {
			return 'Biaya Pribadi';
		} else if (  $this->pembayaran == 'b'  ){
			return 'BPJS';
		} else if (  $this->pembayaran == 'c'  ){
			return $this->nama_asuransi ;
		}
	}
	
}
