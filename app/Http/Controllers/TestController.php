<?php
namespace App\Http\Controllers;


use App\Http\Requests;
use Input;
use DB;
use Moota;
use App\Asuransi;
use App\Telpon;
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
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Vultr\VultrClient;
use Vultr\Adapter\GuzzleHttpAdapter;
use App\Imports\PembayaranImport;
use Maatwebsite\Excel\Facades\Excel;


class TestController extends Controller
{

	public function index(){

		DB::statement('ALTER TABLE pasiens ADD kepala_keluarga_id VARCHAR( 255 );');
		DB::statement('ALTER TABLE pasiens ADD sudah_kontak_bulan_ini tinyint( 1 ) default 0;');

		$periksa_bulan_ini = Periksa::with('pasien')->where('tanggal', 'like', date('Y-m') .'%')->get();

		foreach ($periksa_bulan_ini as $p) {
			$pasien = $p->pasien;
			$pasien->sudah_kontak_bulan_ini = 1;
			$pasien->save();
		}

	}
	public function post(){
		if (Input::hasFile('rekening')) {
			$file =Input::file('rekening'); //GET FILE
			$excel_pembayaran = Excel::toArray(new PembayaranImport, $file)[0];
			$data = [];
			$timestamp = date('Y-m-d H:i:s');
			foreach ($excel_pembayaran as $k => $e) {
				$data[] = [
					'id' => $k +1,
					'akun_bank_id' => 'wnazGyxGWGA',
					'tanggal'      => $e['tanggal'],
					'deskripsi'    => $e['deskripsi'],
					'nilai'        => $e['nilai'],
					'saldo_akhir'  => 0,
					'debet'        => 0,
					'created_at' => $timestamp,
					'updated_at' => $timestamp
				];
			}
			Rekening::insert($data);
		}  
	}
	public function test(){
		$asuransis = Asuransi::all();
		$data = [];
		$timestamp = date('Y-m-d H:i:s');
		foreach ($asuransis as $asu) {
			if (!empty($asu->no_telp)) {
				$data[] = [
					'nomor'           => $asu->no_telp,
					'telponable_id'   => $asu->id,
					'telponable_type' => 'App\\Asuransi',
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];
			}
		}
		Telpon::insert($data);
		DB::statement('ALTER table asuransis drop column no_telp');
		DB::statement('ALTER TABLE asuransis ADD id2 bigint;');
		$asuransis = Asuransi::all();
		foreach ($asuransis as $k => $asu) {
			$asu->id2 = $k +1;
			$asu->save();
			DB::statement("update antrian_polis set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update periksas set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update pics set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update pembayaran_asuransis set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update pasiens set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update sops set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			DB::statement("update tarifs set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			db::statement("update antrian_periksas set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			db::statement("update discount_asuransis set asuransi_id='{$asu->id2}' where asuransi_id='{$asu->id}';");
			db::statement("update emails set emailable_id='{$asu->id2}' where emailable_id='{$asu->id}' and emailable_type='App\\\Asuransi';");
			db::statement("update telpons set telponable_id='{$asu->id2}' where telponable_id='{$asu->id}' and telponable_type='App\\\Asuransi';");
		}
		DB::statement('ALTER TABLE asuransis DROP PRIMARY KEY;');
		DB::statement('ALTER TABLE asuransis DROP PRIMARY KEY;');
		DB::statement('ALTER TABLE asuransis DROP id;');
		DB::statement('ALTER TABLE asuransis RENAME COLUMN "id2" TO "id" bigint not null auto_increment primary key;');
		
	}
}
