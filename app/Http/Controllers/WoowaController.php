<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Sms;
use App\WhatsappRegistration;

class WoowaController extends Controller
{
	public $tanya_tanggal_lahir = 'Bisa dibantu tanggal lahirnya? Contoh 19 Juli 1993 kirim 19-07-1983';

	public $tanya_nama_pasien   = 'Bisa dibantu nama pasien?';

	public 	$tanya_poli         = 'Bisa dibantu berobat ke dokter apa? balas 1 untuk dokter umum, balas 2 untuk dokter gigi, balas 3 untuk suntik kb/periksa hamil. Balas 4 untuk dokter estetika / kecantikan';

	public $tanya_pembayaran    = 'Bisa dibantu pembayaran menggunakan apa? balas A untuk biaya pribadi, balas B untuk bpjs, balas C untuk asuransi';

	public function webhook(){
		$json                  = file_get_contents('php://input');
		Log::info($json);
		$data                  = json_decode($json);
		$message               = $data->message;
		$no_telp               = $data->contact_name;
		$whatsapp_registration = WhatsappRegistration::where('no_telp', $no_telp)
													->where('updated_at', '>', strtotime('-1 hour'))
													->first();
		Log::info($this->clean($message));
		if ( $this->clean($message) == 'daftar' ) {
			if ( is_null( $whatsapp_registration ) ) {
				$whatsapp_registration            = new WhatsappRegistration;
				$whatsapp_registration->no_telp   = $no_telp;
				$whatsapp_registration->save();
			}
		} else if ( 
				is_numeric((int) $this->clean($message)) &&
				(int) $this->clean($message) > 0 &&
				(int) $this->clean($message) < 5 &&
				!is_null( $whatsapp_registration ) 
		) 
		{
				$whatsapp_registration->poli   = (int) $this->clean($message);
				$whatsapp_registration->save();
		} else if ( 
				(
					$this->clean($message) == 'a' ||
					$this->clean($message) == 'b' ||
					$this->clean($message) == 'c' ||
					$this->clean($message) == 'd'
				) &&
				!is_null( $whatsapp_registration ) 
		) 
		{
			$whatsapp_registration->pembayaran  = $this->clean($message);
			$whatsapp_registration->save();
		} else if ( 
				!is_null( $whatsapp_registration ) 
		) 
		{
			Log::info('masuk nama');
			$whatsapp_registration->nama  = $this->clean($message);
			$whatsapp_registration->save();
			Log::info(json_encode($whatsapp_registration));
		}


		Sms::send($no_telp, $this->botKirim($whatsapp_registration));

	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function clean($param)
	{
		return strtolower( trim($param) );
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function botKirim($whatsapp_registration)
	{
		if ( is_null( $whatsapp_registration->poli ) ) {
			return $this->tanya_poli;
		}
		if ( is_null( $whatsapp_registration->pembayaran ) ) {
			return  $this->tanya_pembayaran;
		}
		if ( is_null( $whatsapp_registration->nama ) ) {
			return $this->tanya_nama_pasien;
		}
		if ( is_null( $whatsapp_registration->tanggal_lahir ) ) {
			return $this->tanya_tanggal_lahir;
		}
		return "Terima kasih, telah mendaftarkan berikut ini adalah ulasan pendaftaran anda. Nama = {$whatsapp_registration->nama}, tanggal lahir = {$whatsapp_registration->tanggal_lahir}, pembayaran = {$whatsapp_registration->pembayaran}, poli = {$whatsapp_registration->poli}";
	}
}
