<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public $incrementing = false; 
	public function piutang_asuransi(){
		return $this->hasMany('App\PiutangAsuransi');
	}

	public function getDetailAttribute(){
		$piutang_asuransis = $this->piutang_asuransi;
		$jumlah_tagihan    = $piutang_asuransis->count();
		$total_tagihan     = 0;

		$nama_asuransi     = $piutang_asuransis->first()->periksa->asuransi->nama;

		foreach ($piutang_asuransis as $pa) {
			$total_tagihan += $pa->piutang - $pa->sudah_dibayar;
		}

		return compact(
			'nama_asuransi',
			'jumlah_tagihan',
			'total_tagihan'
		);
	}
	public function getTanggalAkhirAttribute(){
		$piutang_asuransis = $this->piutang_asuransi;
		$dates = [];
		foreach ($piutang_asuransis as $pa) {
			$dates[] = strtotime($pa->periksa->tanggal);
		}
		rsort($dates);
		return date("Y-m-d", $dates[0]);
	}
	public function getTanggalMulaiAttribute(){
		$piutang_asuransis = $this->piutang_asuransi;
		$dates = [];
		foreach ($piutang_asuransis as $pa) {
			$dates[] = strtotime($pa->periksa->tanggal);
		}
		sort($dates);
		return date("Y-m-d", $dates[0]);
	}

	public function getDetailInvoiceAttribute(){
		$piutang_asuransis = $this->piutang_asuransi;
		$jumlah_tagihan    = $piutang_asuransis->count();
		$total_tagihan     = 0;

		$nama_asuransi     = $piutang_asuransis->first()->periksa->asuransi->nama;

		foreach ($piutang_asuransis as $pa) {
			$total_tagihan += $pa->piutang - $pa->sudah_dibayar;
		}

		return compact(
			'nama_asuransi',
			'jumlah_tagihan',
			'total_tagihan'
		);
	}
}
