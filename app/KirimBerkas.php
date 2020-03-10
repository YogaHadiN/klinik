<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class KirimBerkas extends Model
{
	public $incrementing = false;
	protected $dates = [
		'tanggal'
	];
	public function petugas_kirim(){
		return $this->hasMany('App\PetugasKirim');
	}

	public function pengeluaran(){
		return $this->belongsTo('App\Pengeluaran');
	}
	public function invoice(){
		return $this->hasMany('App\Invoice');
	}

	public function getRekapTagihanAttribute(){
		$invoices = $this->invoice;
		$data              = [];
		foreach ($invoices as $invoice) {
			foreach ($invoice->piutang_asuransi as $piutang) {
				$data[ $piutang->periksa->asuransi->nama ][] = $piutang;
			}
		}
		/* return $data; */
		$data2 = [];
		foreach ($data as $k => $dt) {
			$total_tagihan = 0;
			$jumlah_tagihan = count($dt);
			foreach ($dt as $d) {
				$total_tagihan += $d->piutang - $d->sudah_dibayar;
			}
			$data2[ $k ] = [
				'nomor_invoice' => $d->invoice_id,
				'jumlah_tagihan' => $jumlah_tagihan,
				'total_tagihan' => $total_tagihan
			];
		}
		return $data2;
	}
	public function getPiutangTercatatAttribute(){
		$invoices = $this->invoice;
		$data     = [];
		foreach ($invoices as $invoice) {
			foreach ($invoice->piutang_asuransi as $piutang) {
				$data[] = [
					'piutang_id'    => $piutang->id,
					'piutang'       => $piutang->piutang,
					'sudah_dibayar' => $piutang->sudah_dibayar,
					'periksa_id'    => $piutang->periksa_id,
					'nama_pasien'   => $piutang->periksa->pasien->nama,
					'nama_asuransi' => $piutang->periksa->asuransi->nama
				];
			}
		}
		return json_encode($data);
	}
	public function getIdViewAttribute(){
		return str_replace('/', '!', $this->id);
	}
}
