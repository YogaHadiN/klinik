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
	public function webhook(){
		$json                  = file_get_contents('php://input');
		Log::info($json);
		$data                  = json_decode($json);
		$message               = $data->message;
		$no_telp               = $data->contact_name;
		$whatsapp_registration = WhatsappRegistration::where('no_telp', $no_telp)
													->whereRaw("DATE_ADD( updated_at, interval 1 hour ) > '" . date('Y-m-d H:i:s') . "'")
													->first();
		$query = WhatsappRegistration::where('no_telp', $no_telp)
													->whereRaw("DATE_ADD( updated_at, interval 1 hour ) > '" . date('Y-m-d H:i:s') . "'")
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
		if (
			!is_null( $whatsapp_registration ) 
		) {
			$response = '*Ulasan jawaban Anda :*';
			$response .= PHP_EOL;
			if ( !is_null( $whatsapp_registration->nama ) ) {
				$response .= 'Nama : ' . $whatsapp_registration->nama  ;
				$response .= PHP_EOL;
			}
			if ( !is_null( $whatsapp_registration->poli ) ) {
				$response .= 'Poli : ';
				if ( $this->clean($whatsapp_registration->poli) == 'a' ) {
					$response .= ' Dokter Umum';
				} else if (  $this->clean($whatsapp_registration->poli) == 'b'  ){
					$response .= ' Dokter Gigi';
				} else if (  $this->clean($whatsapp_registration->poli) == 'c'  ){
					$response .= ' Suntik KB / Periksa Hamil';
				} else if (  $this->clean($whatsapp_registration->poli) == 'd'  ){
					$response .= ' Dokter Estetik / Kecantikan';
				}
				$response .= PHP_EOL;
			}
			if ( !is_null( $whatsapp_registration->pembayaran ) ) {
				$response .= 'Pembayaran : ';
				if ( $this->clean($whatsapp_registration->pembayaran) == 'a' ) {
					$response .= 'Biaya Pribadi';
				} else if (  $this->clean($whatsapp_registration->pembayaran) == 'b'  ){
					$response .= 'BPJS';
				} else if (  $this->clean($whatsapp_registration->pembayaran) == 'c'  ){
					$response .= 'Asuransi Lain';
				}
				$response .= PHP_EOL;
			}
			if ( !is_null( $whatsapp_registration->tanggal_lahir ) ) {
				$response .= 'Pembayaran : '.  $whatsapp_registration->tanggal_lahir;
				$response .= PHP_EOL;
			}
			$response .= PHP_EOL;
		}

		$response .=  $this->botKirim($whatsapp_registration);
		
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
			$text = 'Terima kasih telah mendaftar sebagai pasien di Klinik Jati Elok.' .. 'Dengan senang hati kami akan siap membantu Anda.' . PHP_EOL . PHP_EOL . 'Bisa dibantu berobat ke dokter apa?' . PHP_EOL . 'Balas *A* untuk dokter umum, ' . PHP_EOL . 'Balas *B* untuk dokter gigi, ' . PHP_EOL . 'Balas *C* untuk suntik kb/periksa hamil.' . PHP_EOL . 'Balas *D* untuk dokter estetika / kecantikan';
			$text .= PHP_EOL;
			$text .= PHP_EOL;
			$text .= 'Balas *A* untuk pembayaran dengan biaya pribadi, '  
			$text .= PHP_EOL;
			$text .= 'Balas *B* pembayaran dengan BPJS, '
			$text .= PHP_EOL;
			$text .= 'Balas *C* pembayaran dengan asuransi';
			return $text;

		}
		if ( is_null( $whatsapp_registration->pembayaran ) ) {
			$text = 'Bisa dibantu pembayaran menggunakan apa? ';
			$text .= PHP_EOL;
			$text .= PHP_EOL;
			$text .= 'Balas *A* untuk pembayaran dengan biaya pribadi, '  
			$text .= PHP_EOL;
			$text .= 'Balas *B* pembayaran dengan BPJS, '
			$text .= PHP_EOL;
			$text .= 'Balas *C* pembayaran dengan asuransi';
			return $text;
		}
		if ( is_null( $whatsapp_registration->nama ) ) {
			return  'Bisa dibantu Nama Lengkap pasien?';
		}
		if ( is_null( $whatsapp_registration->tanggal_lahir ) ) {
			return  'Bisa dibantu tanggal pasien? ' . PHP_EOL . PHP_EOL . 'Contoh *19 Juli 2003* balas dengan *19-07-2003*';
		}
		if ( is_null( $whatsapp_registration->demam ) ) {
			return 'Apakah pasien memiliki keluhan demam. Balas *ya/tidak*?';
		}
		if ( is_null( $whatsapp_registration->batuk_pilek ) ) {
			return 'Apakah pasien memiliki keluhan batuk pilek. Balas *ya/tidak*?';
		}
		if ( is_null( $whatsapp_registration->nyeri_menelan ) ) {
			return 'Apakah pasien memiliki keluhan nyeri menelan. Balas *ya/tidak*?';
		}
		if ( is_null( $whatsapp_registration->bepergian_ke_luar_negeri ) ) {
			return 'Apakah pasien sempat bepergian ke luar negeri dalam 14 hari terakhir? Balas *ya/tidak*';
		}
		if ( is_null( $whatsapp_registration->kontak_covid ) ) {

			$text = 'Apakah anda memiliki riwayat kontak dengan seseorang yang terkonfirmasi/ positif COVID 19 ?' 
			$text .= PHP_EOL;
			$text .= PHP_EOL;
			$text .= '*Kontak Berarti :*';
			$text .= PHP_EOL;
			$text .= '- Tinggal serumah';
			$text .= PHP_EOL;
			$text .= '- Kontak tatap muka, misalnya : bercakap-cakap selama beberapa menit';
			$text .= PHP_EOL;
			$text .= '- Terkena Batuk pasien terkontaminasi';
			$text .= PHP_EOL;
			$text .= '- Berada dalam radius 2 meter selama lebih dari 15 menit dengan kasus terkonfirmasi';
			$text .= PHP_EOL;
			$text .= '- Kontak dengan cairan tubuh kasus terkonfirmasi';
			$text .= PHP_EOL;
			$text .= PHP_EOL;
			$text .= 'Balas *ya/tidak*';

			return $text;
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
