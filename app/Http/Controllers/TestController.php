<?php



namespace App\Http\Controllers;

use Input;

use App\Http\Requests;

use DB;
use Moota;
use App\Asuransi;
use App\CheckoutKasir;
use App\BayarGaji;
use App\Pasien;
use App\User;
use App\Staf;
use App\Rak;
use App\JurnalUmum;
use App\TransaksiPeriksa;
use App\Terapi;
use App\Dispensing;
use App\Rujukan;
use App\SuratSakit;
use App\RegisterAnc;
use App\Usg;
use App\GambarPeriksa;
use App\Periksa;
use App\Merek;
use App\BukanPeserta;
use App\Formula;
use App\Komposisi;
use App\Classes\Yoga;
use App\AkunBank;
use App\Rekening;
use App\Http\Handler;
use App\Console\Commands\sendMeLaravelLog;
use App\Imports\PembayaranImport;
use Maatwebsite\Excel\Facades\Excel;


class TestController extends Controller
{

	public function index(){
		//define semua bank yang ada

		$banks = Moota::banks();
		foreach ($banks['data'] as $bank) {
			$bank_id = $bank->bank_id;
			$newBank = AkunBank::findOrNew($bank_id);
			if ( !$newBank->id ) {
				$newBank->id             = $bank_id;
				$newBank->nomor_rekening = $bank->account_number;
				$newBank->akun           = $bank->bank_type;
				$newBank->save();
			}
			$mutasis = Moota::mutation( $newBank->id )->month()->toArray();
			$insertMutasi = [];
			foreach ($mutasis['data'] as $mutasi) {
				if ( $mutasi->type == 'CR' ) {
					$debet = 0;
				} else {
					$debet = 1;
				}
				$newRekening = Rekening::findOrNew($mutasi->mutation_id);
				if ( !$newRekening->id ) {
					$insertMutasi[] = [
						'id'           => $mutasi->mutation_id,
						'akun_bank_id' => $newBank->id,
						'tanggal'      => $mutasi->created_at,
						'deskripsi'    => $mutasi->description,
						'nilai'        => $mutasi->amount,
						'saldo_akhir'  => $mutasi->balance,
						'debet'        => $debet
					];
				}
			}
			Rekening::insert($insertMutasi);
		}
	}
}
