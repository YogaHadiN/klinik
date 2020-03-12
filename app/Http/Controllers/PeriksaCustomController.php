<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\TransaksiPeriksa;
use App\PiutangAsuransi;
use App\Pasien;
use App\Classes\Yoga;
use App\Periksa;
use App\Sms;
use App\JurnalUmum;
use Input;
use DB;

class PeriksaCustomController extends Controller
{

	  public function __construct()
    {
		$this->middleware('super', ['only' => [
			'editTransaksiPeriksa',
			'updateTransaksiPeriksa'
		]]);
    }
	public function editJurnal($id){
		$periksa = Periksa::find($id);
		return view('jurnal_umums.editJurnal', compact('periksa'));
	}
	
	public function editTransaksiPeriksa($id){
		$periksa          = Periksa::with('jurnals', 'transaksii')->where('id', $id)->first();
		return view('periksas.editTransaksiPeriksa', compact(
			'periksa'
		));
	}
	public function updateTransaksiPeriksa(){


		$transaksis = json_decode( Input::get('transaksis'), true );
		$periksa    = json_decode( Input::get('periksa'), true );
		$jurnals    = json_decode( Input::get('jurnals'), true );
		$temp       = json_decode( Input::get('temp'), true );

		$timestamp = date('Y-m-d H:i:s');
		$jurnal = [];

		DB::beginTransaction();
		try {
			foreach ($transaksis as $t) {
				$trans        = TransaksiPeriksa::find($t['id']);
				$trans->biaya = $t['biaya'];
				$trans->save();
			}

			$prx                 = Periksa::find($periksa['id']);
			$prx->tunai          = $periksa['tunai'];
			$prx->nomor_asuransi = Input::get('nomor_asuransi');
			$prx->piutang        = $periksa['piutang'];
			$confirm             = $prx->save();

			if ( $prx->asuransi_id == $prx->pasien->asuransi_id ) {
				$pasien                 = Pasien::find( $prx->pasien_id );
				$pasien->nomor_asuransi = Input::get('nomor_asuransi');
				$pasien->save();
			}

			if ( $prx->piutang > 0 ) {
				try {
					$pp = PiutangAsuransi::where('periksa_id', $prx->id)->firstOrFail();
					$pp->tunai      = $periksa['tunai'];
					$pp->piutang    = $periksa['piutang'];
					$pp->save();
				} catch (\Exception $e) {
					$pp             = new PiutangAsuransi;
					$pp->tunai      = $periksa['tunai'];
					$pp->piutang    = $periksa['piutang'];
					$pp->periksa_id = $periksa['id'];
					$pp->save();
				}
			} else {
				PiutangAsuransi::where('periksa_id', $prx->id)->delete();
			}

			JurnalUmum::where('jurnalable_type', 'App\Periksa')->where('jurnalable_id', $periksa['id'])->delete();
			foreach ($jurnals as $j) {
				$jurnal[] = [
					'jurnalable_id'   => $periksa['id'],
					'jurnalable_type' => 'App\Periksa',
					'debit'           => $j['debit'],
					'coa_id'          => $j['coa_id'],
					'nilai'           => $j['nilai'],
					'created_at'      => $j['created_at'],
					'updated_at'      => $timestamp
				];
			}
			foreach ($temp as $t) {
				$jurnal[] = [
					'jurnalable_id'    => $periksa['id'],
					'jurnalable_type' => 'App\Periksa',
					'debit'            => $t['debit'],
					'coa_id'           => $t['coa_id'],
					'nilai'            => $t['nilai'],
					'created_at'       => $timestamp,
					'updated_at'       => $timestamp
				];
			}
			JurnalUmum::insert($jurnal);
			Sms::send('081381912803', 'Telah dilakukan update transaksi dengan id periksa ' . $prx->id . ' , nama pasien ' . $prx->pasien->nama);
			$pesan = Yoga::suksesFlash('Berhasil mengedit Transaksi Periksa');
			DB::commit();
			return redirect()->back()->withPesan($pesan);
		} catch (\Exception $e) {
			DB::rollback();
			throw $e;
		}
	}
}
