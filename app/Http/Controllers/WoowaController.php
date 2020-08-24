<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;
use DateTime;
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
													->whereRaw("DATE_ADD( updated_at, interval 1 hour ) < '" . date('Y-m-d H:i:s') . "'")
													->first();
		$query = WhatsappRegistration::where('no_telp', $no_telp)
													->whereRaw("DATE_ADD( updated_at, interval 1 hour ) < '" . date('Y-m-d H:i:s') . "'")
													->toSql();
		Log::info('whatsapp_registration awal');
		Log::info( json_encode($whatsapp_registration) );
		Log::info('$this->clean($message)');
		Log::info($this->clean($message));
		Log::info('query');
		Log::info($query);
		$response = '';
		if ( $this->clean($message) == 'daftar' ) {
			if ( is_null( $whatsapp_registration ) ) {
				$whatsapp_registration            = new WhatsappRegistration;
				$whatsapp_registration->no_telp   = $no_telp;
				$whatsapp_registration->save();
			}
		} else if ( 
				!is_null( $whatsapp_registration ) &&
				is_null( $whatsapp_registration->poli ) 
		) {
			if (
				(int)$this->clean($message) > 0 &&
				(int)$this->clean($message) < 5
			) {
				$whatsapp_registration->poli   = (int) $this->clean($message);
				$whatsapp_registration->save();
			} else {
				$response = 'Input yang anda masukkan salah';
			}
		} else if ( 
				!is_null( $whatsapp_registration ) &&
				is_null( $whatsapp_registration->pembayaran ) 
		) 
		{
			if (
				(int)$this->clean($message) > 0 &&
				(int)$this->clean($message) < 5
			) {
				$whatsapp_registration->pembayaran  = $this->clean($message);
				$whatsapp_registration->save();
			} else {
				$response = 'Input yang anda masukkan salah';
			}
		} else if ( 
			!is_null( $whatsapp_registration ) &&
			is_null( $whatsapp_registration->nama ) 
		) {
			Log::info('masuk nama');
			$whatsapp_registration->nama  = $this->clean($message);
			$whatsapp_registration->save();
			Log::info(json_encode($whatsapp_registration));
		} else if ( 
			!is_null( $whatsapp_registration ) &&
			is_null( $whatsapp_registration->tanggal_lahir ) 
		) 
		{
			if ( $this->validateDate($this->clean($message), $format = 'd-m-Y') ) {
				$whatsapp_registration->tanggal_lahir  = Carbon::CreateFromFormat('d-m-Y',$this->clean($message))->format('Y-m-d');
				$whatsapp_registration->save();
			} else {
				$response = 'Input yang anda masukkan salah';
			}
		} else if ( 
			!is_null( $whatsapp_registration ) &&
			is_null( $whatsapp_registration->demam ) 
		) 
		{
			if ( $this->clean($message) == 'ya')  {
				$whatsapp_registration->demam  = 1;
				$whatsapp_registration->save();
			} else if ( $this->clean($message) == 'tidak') {
				$whatsapp_registration->demam  = 0;
				$whatsapp_registration->save();
			} else {
				$response = 'Input Tidak tepat';
			}
		} else if ( 
			!is_null( $whatsapp_registration ) &&
			is_null( $whatsapp_registration->batuk_pilek ) 
		) 
		{
			if ( $this->clean($message) == 'ya')  {
				$whatsapp_registration->batuk_pilek  = 1;
				$whatsapp_registration->save();
			} else if ( $this->clean($message) == 'tidak')  {
				$whatsapp_registration->batuk_pilek  = 0;
				$whatsapp_registration->save();
			} else {
				$response = 'Input Tidak tepat';
			}
		} else if ( 
			!is_null( $whatsapp_registration ) &&
			is_null( $whatsapp_registration->nyeri_menelan ) 
		) 
		{
			if ( $this->clean($message) == 'ya')  {
				$whatsapp_registration->nyeri_menelan  = 1;
				$whatsapp_registration->save();
			} else if ( $this->clean($message) == 'tidak')  {
				$whatsapp_registration->nyeri_menelan  = 0;
				$whatsapp_registration->save();
			} else {
				$response = 'Input Tidak tepat';
			}
		} else if ( 
			!is_null( $whatsapp_registration ) &&
			is_null( $whatsapp_registration->sesak_nafas ) 
		) 
		{
			if ( $this->clean($message) == 'ya')  {
				$whatsapp_registration->sesak_nafas  = 1;
				$whatsapp_registration->save();
			} else if ( $this->clean($message) == 'tidak')  {
				$whatsapp_registration->sesak_nafas  = 0;
				$whatsapp_registration->save();
			} else {
				$response = 'Input Tidak tepat';
			}
		} else if ( 
			!is_null( $whatsapp_registration ) &&
			is_null( $whatsapp_registration->bepergian_ke_luar_negeri ) 
		) 
		{
			if ( $this->clean($message) == 'ya')  {
				$whatsapp_registration->bepergian_ke_luar_negeri  = 1;
				$whatsapp_registration->save();
			} else if ( $this->clean($message) == 'tidak')  {
				$whatsapp_registration->bepergian_ke_luar_negeri  = 0;
				$whatsapp_registration->save();
			} else {
				$response = 'Input Tidak tepat';
			}
		} else if ( 
			!is_null( $whatsapp_registration ) &&
			is_null( $whatsapp_registration->kontak_covid ) 
		) 
		{
			if ( $this->clean($message) == 'ya')  {
				$whatsapp_registration->kontak_covid  = 1;
				$whatsapp_registration->save();
			} else if ( $this->clean($message) == 'tidak')  {
				$whatsapp_registration->kontak_covid  = 0;
				$whatsapp_registration->save();
			} else {
				$response = 'Input Tidak tepat';
			}
		}

		Log::info('whatsapp_registration');
		Log::info( json_encode($whatsapp_registration) );

		$response = $response . ' ' . $this->botKirim($whatsapp_registration);
		Sms::send($no_telp, $response);
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
			return  'Bisa dibantu berobat ke dokter apa? balas 1 untuk dokter umum, balas 2 untuk dokter gigi, balas 3 untuk suntik kb/periksa hamil.Balas 4 untuk dokter estetika / kecantikan';
		}
		if ( is_null( $whatsapp_registration->pembayaran ) ) {
			return   'Bisa dibantu pembayaran menggunakan apa? balas A untuk biaya pribadi, balas B untuk bpjs, balas C untuk asuransi';
		}
		if ( is_null( $whatsapp_registration->nama ) ) {
			return  'Bisa dibantu nama pasien?';
		}
		if ( is_null( $whatsapp_registration->tanggal_lahir ) ) {
			return  'Bisa dibantu tanggal lahirnya? Contoh 19 Juli 1993 kirim 19-07-1983';
		}
		if ( is_null( $whatsapp_registration->demam ) ) {
			return 'Apakah anda memiliki keluhan demam ?';
		}
		if ( is_null( $whatsapp_registration->batuk_pilek ) ) {
			return 'Apakah anda memiliki keluhan batuk pilek ?';
		}
		if ( is_null( $whatsapp_registration->nyeri_menelan ) ) {
			return 'Apakah anda memiliki keluhan nyeri menelan ?';
		}
		if ( is_null( $whatsapp_registration->bepergian_ke_luar_negeri ) ) {
			return 'Apakah anda sempat bepergian ke luar negeri dalam 14 hari terakhir?';
		}
		if ( is_null( $whatsapp_registration->kontak_covid ) ) {
			return 'Apakah anda sempat sempat kontak dengan penderita covid?';
		}
		return "Terima kasih, telah mendaftarkan berikut ini adalah ulasan pendaftaran anda. Nama = {$whatsapp_registration->nama}, tanggal lahir = {$whatsapp_registration->tanggal_lahir}, pembayaran = {$whatsapp_registration->pembayaran}, poli = {$whatsapp_registration->poli}";
	}
	private function validateDate($date, $format = 'Y-m-d')
	{
		$d = DateTime::createFromFormat($format, $date);
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format($format) === $date;
	}
}
