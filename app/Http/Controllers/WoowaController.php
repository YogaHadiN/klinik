<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Sms;
use App\WhatsappRegistration;

class WoowaController extends Controller
{
	public function webhook(){
		$json = file_get_contents('php://input');

		Log::info($json);

		$data = json_decode($json);
		$message             = $data->message;
		$no_telp             = $data->contact_name;
		$tanya_tanggal_lahir = 'Bisa dibantu tanggal lahirnya? Contoh 19 Juli 1993 kirim 19-07-1983';
		$tanya_nama_pasien   = 'Bisa dibantu nama pasien?';
		$tanya_poli          = 'Bisa dibantu berobat ke dokter apa? balas 1 untuk dokter umum, balas 2 untuk dokter gigi, balas 3 untuk suntik kb/periksa hamil. Balas 4 untuk dokter estetika / kecantikan';
		$tanya_pembayaran    = 'Bisa dibantu pembayaran menggunakan apa? balas 1 untuk biaya pribadi, balas 2 untuk bpjs, balas 3 untuk asuransi';
		if ( $this->clean($message) == 'daftar' ) {

			try {
				$whatsapp_registration            = WhatsappRegistration::where('no_telp', $no_telp)
																		->where('updated_at', '>', strtotime('-1 hour'))
																		->first();
				if ( is_null( $whatsapp_registration->tanggal_lahir ) ) {
					$message = $tanya_tanggal_lahir;
				}
				if ( is_null( $whatsapp_registration->nama ) ) {
					$message = $tanya_nama_pasien;
				}
				if ( is_null( $whatsapp_registration->pembayaran ) ) {
					$message = $tanya_pembayaran;
				}
				if ( is_null( $whatsapp_registration->poli ) ) {
					$message = $tanya_poli;
				}

			} catch (\Exception $e) {
				$whatsapp_registration            = new WhatsappRegistration;
			}
			$whatsapp_registration->no_telp   = $no_telp;
			$whatsapp_registration->save();

			Sms::send($no_telp, $message);
		}
		/* $message = 'Selamat Siang. Terima kasih telah menghubungi kami. Ada yang dapat kami bantu?'; */
		/* Sms::send($data->contact_name, $message); */
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
	
}
