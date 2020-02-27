<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class KirimBerkas extends Model
{
	public function petugas_kirim(){
		return $this->hasMany('App\PetugasKirim');
	}

	public function piutang_asuransi(){
		return $this->hasMany('App\PiutangAsuransi');
	}

	public function getRekapTagihanAttribute(){
		$piutang_asuransis = $this->piutang_asuransi;
		$data              = [];
		foreach ($piutang_asuransis as $piutang) {
			$data[ $piutang->periksa->asuransi->nama ][] = $piutang;
		}
		$data2 = [];
		foreach ($data as $k => $dt) {
			$total_tagihan = 0;
			$jumlah_tagihan = count($dt);
			foreach ($dt as $d) {
				$total_tagihan += $d->piutang - $d->sudah_dibayar;
			}
			$data2[ $k ] = [
				'jumlah_tagihan' => $jumlah_tagihan,
				'total_tagihan' => $total_tagihan,
			];
		}
		return $data2;
	}
}
