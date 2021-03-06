<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pph21 extends Model
{
	protected $guarded = ['id'];
	public function jurnalable(){
		return $this->morphto();
	}
	public function getTotalBrutoAttribute(){

		$gaji_bruto_bulan_ini = json_decode($this->ikhtisar_gaji_bruto, true);
		$total_bayar_bulan_ini = 0;
		foreach ( $gaji_bruto_bulan_ini as $g) {
			$total_bayar_bulan_ini += $g['gaji_bruto'];
		}
		return $total_bayar_bulan_ini;
	}

	public function getTotalPph21Attribute(){

		$gaji_bruto_bulan_ini = json_decode($this->ikhtisar_gaji_bruto, true);
		$total_bayar_bulan_ini = 0;
		foreach ( $gaji_bruto_bulan_ini as $g) {
			$total_bayar_bulan_ini += $g['pph21'];
		}
		return $total_bayar_bulan_ini;
	}

	public function getTotalBayarAttribute(){

		$gaji_bruto_bulan_ini = json_decode($this->ikhtisar_gaji_bruto, true);
		$total_bayar_bulan_ini = 0;
		foreach ( $gaji_bruto_bulan_ini as $g) {
			$total_bayar_bulan_ini += $g['gaji_bruto'] - $g['pph21'];
		}
		return $total_bayar_bulan_ini;
	}

}
