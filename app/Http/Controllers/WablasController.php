<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;
use DateTime;
use App\Sms;
use App\WhatsappRegistration;

class WablasController extends Controller
{
	public function webhook(){
		if(isset($_POST['message'])) {
			$message               = $_POST['message'];
			$no_telp               = $_POST['phone'];

			$whatsapp_registration = WhatsappRegistration::where('no_telp', $no_telp)
														->whereRaw("DATE_ADD( updated_at, interval 1 hour ) > '" . date('Y-m-d H:i:s') . "'")
														->first();
			Log::info('$this->clean($message)');
			Log::info($this->clean($message));
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
					$this->clean($message) == 'a' ||
					$this->clean($message) == 'b' ||
					$this->clean($message) == 'c' ||
					$this->clean($message) == 'd' ||
					$this->clean($message) == 'e'
				) {
					$whatsapp_registration->poli   = $this->clean($message);
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
					$this->clean($message) == 'a' ||
					$this->clean($message) == 'b' ||
					$this->clean($message) == 'c'
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
				Log::info('============================ sesak nafas ==========================================');
				if ( $this->clean($message)             == 'ya')  {
					$whatsapp_registration->sesak_nafas  = 1;
					$whatsapp_registration->save();
				} else if ( $this->clean($message)      == 'tidak')  {
					$whatsapp_registration->sesak_nafas  = 0;
					$whatsapp_registration->save();
				} else {
					$response .= 'Input Tidak tepat';
				}
			} else if ( 
				!is_null( $whatsapp_registration ) &&
				is_null( $whatsapp_registration->bepergian_ke_luar_negeri ) 
			) 
			{
				Log::info('============================ bepergian ke luar neger ==========================================');
				if ( $this->clean($message) == 'ya')  {
					$whatsapp_registration->bepergian_ke_luar_negeri  = 1;
					$whatsapp_registration->save();
				} else if ( $this->clean($message) == 'tidak')  {
					$whatsapp_registration->bepergian_ke_luar_negeri  = 0;
					$whatsapp_registration->save();
				} else {
					$response .= 'Input Tidak tepat';
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
				if (
				 !is_null( $whatsapp_registration->nama ) ||
				 !is_null( $whatsapp_registration->poli ) ||
				 !is_null( $whatsapp_registration->pembayaran ) ||
				 !is_null( $whatsapp_registration->tanggal_lahir )
				) {
					$response .=    "*Uraian Pengisian Anda*";
					$response .= PHP_EOL;
					$response .= PHP_EOL;
				}
				if ( !is_null( $whatsapp_registration->nama ) ) {
					$response .= 'Nama Pasien: ' . ucwords($whatsapp_registration->nama)  ;
					$response .= PHP_EOL;
				}
				if ( !is_null( $whatsapp_registration->poli ) ) {
					$response .= 'Poli : ';
					$response .= $this->formatPoli( $whatsapp_registration->poli );
					$response .= PHP_EOL;
				}
				if ( !is_null( $whatsapp_registration->pembayaran ) ) {
					$response .= 'Pembayaran : ';
					$response .= $this->formatPembayaran( $whatsapp_registration->pembayaran );
					$response .= PHP_EOL;
				}
				if ( !is_null( $whatsapp_registration->tanggal_lahir ) ) {
					$response .= 'Tanggal Lahir : '.  Carbon::CreateFromFormat('Y-m-d',$whatsapp_registration->tanggal_lahir)->format('d M Y');;
					$response .= PHP_EOL;
				}
				if (
				 !is_null( $whatsapp_registration->nama ) ||
				 !is_null( $whatsapp_registration->poli ) ||
				 !is_null( $whatsapp_registration->pembayaran ) ||
				 !is_null( $whatsapp_registration->tanggal_lahir )
				) {
					$response .= PHP_EOL;
					$response .=    "==================";
					$response .= PHP_EOL;
					$response .= PHP_EOL;
				}
			}

			$response .=  $this->botKirim($whatsapp_registration);
			
			Sms::send($no_telp, $response);
		}
	}

	private function validateDate($date, $format = 'Y-m-d')
	{
		$d = DateTime::createFromFormat($format, $date);
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format($format) === $date;
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function formatPoli($param)
	{
		if ( $this->clean($param) == 'a' ) {
			return ' Dokter Umum';
		} else if (  $this->clean($param) == 'b'  ){
			return ' Dokter Gigi';
		} else if (  $this->clean($param) == 'c'  ){
			return ' Suntik KB / Periksa Hamil';
		} else if (  $this->clean($param) == 'd'  ){
			return ' Dokter Estetik / Kecantikan';
		} else if (  $this->clean($param) == 'e'  ){
			return 'USG Kebidanan';
		}
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function formatPembayaran($param)
	{
		if ( $this->clean($param) == 'a' ) {
			return 'Biaya Pribadi';
		} else if (  $this->clean($param) == 'b'  ){
			return 'BPJS';
		} else if (  $this->clean($param) == 'c'  ){
			return 'Asuransi Lain';
		}
	}
}
