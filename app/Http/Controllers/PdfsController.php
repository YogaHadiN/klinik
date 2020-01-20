<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;

use App\Http\Requests;
use App\Http\Controllers\LaporanLabaRugisController;
use App\Http\Controllers\LaporanNeracasController;
use App\Http\Controllers\PajaksController;
use App\Http\Controllers\AsuransisController;
use App\Http\Controllers\PendapatansController;
use App\Classes\Yoga;
use App\Periksa;
use App\Pph21Dokter;
use App\BagiGigi;
use App\Modal;
use App\BayarDokter;
use App\Pengeluaran;
use App\NoSale;
use App\Asuransi;
use App\CheckoutKasir;
use App\BayarGaji;
use App\NotaJual;
use App\AntrianPoli;
use App\Pasien;
use App\Pendapatan;
use App\JurnalUmum;
use App\FakturBelanja;
use App\PembayaranAsuransi;
use App\Tarif;
use App\Merek;
use App\Rak;
use DB;
use PDF;

class PdfsController extends Controller
{

	/**
	 * Display a listing of the resource.
	 * GET /pdfs
	 *
	 * @return Response
	 */
	public function status($periksa_id)
	{
		return $this->status_private('a5', $periksa_id);
	}
	public function kuitansi($periksa_id)
	{
		$periksa = Periksa::find($periksa_id);

        $pdf = PDF::loadView('pdfs.kuitansi', compact('periksa'))->setPaper('a5')->setOrientation('landscape')->setWarnings(false);
        return $pdf->stream();
	}
	public function getKuitansiview()
	{
        return view('pdfs.kuitansi');
	}
	public function struk($periksa_id)
	{

		$periksa = Periksa::find($periksa_id);
        //return dd( $trxa );
		$pdf = PDF::loadView('pdfs.struk', compact(
			'periksa'
		//))->setPaper(array(0, 0, 210, 810),'Potrait');
		))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
	}
    public function jasa_dokter($bayar_dokter_id){
        $bayar = BayarDokter::find($bayar_dokter_id);
		$bulanPembayaran = $bayar->created_at->format('Y-m');
		$pembayaran_bulan_ini = BayarDokter::where('staf_id', $bayar->staf_id)
											->where('created_at', 'like', $bulanPembayaran . '%')
											->get();
		$total_pph_sudah_dibayar = 0;
		$errors = [];
		$perhitunganPphs = $bayar->perhitungan_pph;
		$perhitunganPphs = json_decode($perhitunganPphs, true);
		if (count( $perhitunganPphs ) > 0) {
			foreach (json_decode( $bayar->perhitungan_pph, true ) as $b) {
				$total_pph_sudah_dibayar += $b['pph'];
			}
		}
		$total_pembayaran_bulan_ini = 0;
		$total_pph_bulan_ini = 0;
		foreach ($pembayaran_bulan_ini as $bayar) {
			$total_pembayaran_bulan_ini += $bayar->bayar_dokter;
			$total_pph_bulan_ini += $bayar->pph21;
		}

		$pdf = PDF::loadView('pdfs.jasa_dokter', compact(
			'bayar',
			'pembayaran_bulan_ini',
			'total_pembayaran_bulan_ini',
			'total_pph_sudah_dibayar',
			'total_pph_bulan_ini'
		))
				->setOption('page-width', 72)
				->setOption('page-height', 297)
				->setOption('margin-top', 0)
				->setOption('margin-bottom', 0)
				->setOption('margin-right', 0)
				->setOption('margin-left', 0);
        return $pdf->stream();
    }
    
    public function pembelian($faktur_belanja_id){
        $fakturbelanja = FakturBelanja::find($faktur_belanja_id);
		//return dd( $fakturbelanja );
        $total = 0;
        if ($fakturbelanja->belanja_id == 1) {
            foreach ($fakturbelanja->pembelian as $pemb) {
                $total += $pemb->harga_beli * $pemb->jumlah;
            }
		} else if ($fakturbelanja->belanja_id == 4) {
			//return dd( $fakturbelanja->belanjaPeralatan );
            foreach ($fakturbelanja->belanjaPeralatan as $pemb) {
                $total += $pemb->harga_satuan * $pemb->jumlah;
            }
        } else {
            foreach ($fakturbelanja->pengeluaran as $pemb) {
                $total += $pemb->harga_satuan * $pemb->jumlah;
            }
        }
		$pdf = PDF::loadView('pdfs.pembelian', compact(
			'fakturbelanja', 
			'total'
		))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
    }
    public function penjualan($nota_jual_id){
        $nota_jual = NotaJual::find($nota_jual_id);

		$pdf = PDF::loadView('pdfs.penjualan', compact('nota_jual'))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();


    }
    public function pembayaran_asuransi($pembayaran_asuransi_id){
        $pembayaran = PembayaranAsuransi::find($pembayaran_asuransi_id);
		$pdf = PDF::loadView('pdfs.pembayaran_asuransi', compact('pembayaran'))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
    }
    public function bayar_gaji_karyawan($bayar_gaji_id){

        $bayar = BayarGaji::find($bayar_gaji_id);
		$gajis = BayarGaji::where('staf_id', $bayar->staf_id)
							->where('mulai', 'like', $bayar->mulai->format('Y-m') . '%')
							->get();
		$total_pembayaran_bulan_ini = 0;
		$total_pph_bulan_ini        = 0;
		foreach ($gajis as $gaji) {
			$total_pembayaran_bulan_ini += $gaji->gaji_pokok + $gaji->bonus;
			$total_pph_bulan_ini        += $gaji->pph21;
		}

		$pdf = PDF::loadView('pdfs.bayar_gaji_karyawan', compact(
			'bayar',
			'gajis',
			'total_pembayaran_bulan_ini',
			'total_pph_bulan_ini'
		))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();

    }
    public function notaz($checkout_kasir_id){
        $notaz = CheckoutKasir::find($checkout_kasir_id);
		$buka_kasir = CheckoutKasir::find($checkout_kasir_id - 1)->created_at;
        $detils = json_decode( $notaz->detil_pengeluarans, true );
        $detils_tangan = json_decode( $notaz->detil_pengeluaran_tangan, true );
        $modals = json_decode( $notaz->detil_modals, true );
        $pengeluarans = JurnalUmum::whereIn('id', $detils)->get();
        $pengeluarans_tangan = JurnalUmum::whereIn('id', $detils_tangan)->get();
        $modals = JurnalUmum::whereIn('id', $modals)->get();
		$total_modal = 0;
		foreach ($modals as $mdl) {
			$total_modal += $mdl->nilai;
		}

		$total_pengeluaran = 0;
		foreach ($pengeluarans as $mdl) {
			$total_pengeluaran += $mdl->nilai;
		}

		$total_pengeluaran_tangan = 0;
		foreach ($pengeluarans_tangan as $mdl) {
			$total_pengeluaran_tangan += $mdl->nilai;
		}
        $total_pemasukan = 0;
        foreach ($notaz->checkoutDetail as $cd) {
            $total_pemasukan += $cd->nilai;
        }
		//return $total_nilai;
		$pdf = PDF::loadView('pdfs.notaz', compact(
			'notaz', 
			'pengeluarans',
			'pengeluarans_tangan',
			'modals', 
			'buka_kasir', 
			'total_modal', 
			'total_pengeluaran',
			'total_pengeluaran_tangan',
			'total_pemasukan'
		))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
    }
    public function rc($modal_id){
         $modal = Modal::find($modal_id);

		 $pdf = pdf::loadview('pdfs.rc', compact('modal'))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();

    }
    public function ns($no_sale_id){
        $nosale = NoSale::find($no_sale_id);
        
		$pdf = pdf::loadview('pdfs.ns', compact('nosale'))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
    }

	public function pengeluaran($id){
		
        $pengeluaran = Pengeluaran::find($id);
		$pdf = pdf::loadview('pdfs.pengeluaran', compact('pengeluaran'))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
	}
	public function pendapatan($id){
		
        $pendapatan = Pendapatan::find($id);
		$pdf = pdf::loadview('pdfs.pendapatan', compact('pendapatan'))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
	}
	
    public function merek(){
        $mereks = Merek::all();
		$pdf = pdf::loadview('pdfs.merek', compact('mereks'))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
    }
	public function dispensing($rak_id, $mulai, $akhir){

		// return 'mulai = ' . $mulai . ' akhir = ' . $akhir . ' rak_id = ' . $rak_id . ' ';
		//$dispensings = DB::select("SELECT id, tanggal, rak_id, sum(keluar) as keluar, sum(masuk) as masuk, dispensable_id FROM dispensings where tanggal <= '{$akhir}' AND tanggal >= '{$mulai}' AND rak_id like '{$rak_id}' group by tanggal");
		$query = "SELECT id, ";
		$query .= "tanggal, ";
		$query .= "rak_id, ";
		$query .= "sum(keluar) as keluar, ";
		$query .= "sum(masuk) as masuk, ";
		$query .= "dispensable_id, ";
		$query .= "dispensable_type ";
		$query .= "FROM dispensings ";
		$query .= "where tanggal <= '{$akhir}' ";
		$query .= "AND tanggal >= '{$mulai}' ";
		$query .= "AND rak_id like '{$rak_id}' ";
		$query .= "group by tanggal";

		$dispensings = DB::select($query);
		// $dispensings = Dispensing::where('tanggal', '>=', $mulai)->where('tanggal', '<=', $akhir)->where('rak_id', 'like', $rak_id)->groupBy('rak_id')->get();
		$rak = Rak::find($rak_id);
		$raks = Rak::all();



		$pdf = pdf::loadview('pdfs.dispensing', compact(
			'dispensings', 
			'rak',  
			'mulai',  
			'akhir',  
			'raks'
		))->setPaper('a5')->setOrientation('potrait')->setWarnings(false);
        return $pdf->stream();


	}
	public function status_a4($periksa_id){
		return $this->status_private('a4', $periksa_id);
	}
	private function status_private($a, $periksa_id){
		header ('Content-type: text/html; charset=utf-8');
		$periksa    = Periksa::find($periksa_id);

		//cek apakah pasien ini sudah pernah periksa GDS sebelumnya
		//
		//
		//
		$tarifObatFlat = Tarif::where('asuransi_id', $periksa->asuransi_id)->where('jenis_tarif_id', '9')->first()->biaya;		


		$bayarGDS   = false;
		$transaksi_before = json_decode($periksa->transaksi, true);
		// return 'oke';
		if ($periksa->asuransi_id == 32) {
			foreach ($transaksi_before as $key => $value) {
				if (($value['jenis_tarif_id'] == '116')) {
					$bayarGDS = Yoga::cekGDSBulanIni($periksa->pasien, $periksa)['bayar'];
				}
			}
			$cetak_usg = '1';
		} else {
			$cetak_usg = '2';
		}

		// return $periksa->register_anc->register_hamil->riwobs;
		$puyerAdd = false;
		foreach ($periksa->terapii as $key => $v) {
			if ($v->merek_id < 0) {
				$puyerAdd = true;
				break;
			}
		}

		$transaksis = $periksa->transaksi;
		$biaya = 0;
		$transaksis = json_decode($transaksis, true);
		// return $t
		foreach ($transaksis as $transaksi) {
			$biaya += $transaksi['biaya'];
			if ($transaksi['jenis_tarif_id'] == '9') {
				$biayaObat = $transaksi['biaya'];
			}
		}
		// return $periksa->asuransi->tipe_asuransi;
		// return $biayaObat;
		// return $tarifObatFlat;

		// return dd($transaksis);

		// return $periksa->registerAnc->presentasi_id;

        $pdf = PDF::loadView('pdfs.status', compact('periksa', 'cetak_usg', 'puyerAdd', 'bayarGDS', 'biaya', 'biayaObat', 'tarifObatFlat'))->setPaper($a)->setOrientation('landscape')->setWarnings(false);
        // return view('pdfs.status', compact('periksa', 'cetak_usg', 'puyerAdd', 'bayarGDS'));
        return $pdf->stream();

	}
	public function formUsg($id, $asuransi_id){
		$pasien = Pasien::find($id);
		$asuransi = Asuransi::find($asuransi_id);
		$pdf = PDF::loadView('pdfs.form_usg', compact(
			'pasien',
			'asuransi'
		))->setPaper('a5')->setOrientation('landscape')->setWarnings(false);
        // return view('pdfs.status', compact('periksa', 'cetak_usg', 'puyerAdd', 'bayarGDS'));
        return $pdf->stream();
	}
	
	public function laporanLabaRugi($tanggal_awal, $tanggal_akhir){
		$lap   = new LaporanLabaRugisController;
		$query = $lap->tempLaporanLabaRugiRangeByDate($tanggal_awal, $tanggal_akhir);
		$pdf   = PDF::loadView(
					'pdfs.laporan_laba_rugi', 
					$query)
				->setPaper('a4');
        return $pdf->stream();
	}
	public function laporanLabaRugiPerTahun($tahun){
		return 'perTahun';
	}
	public function this(){
		$data =  [
			 'foo' => 'bar'
		];
		$pdf = PDF::loadView('pdfs.jurnal_umum', $data)
			->setPaper('a5')
			->setOrientation('landscape')
			->setOption('margin-bottom', 0);
		return $pdf->stream();
	}


	public function laporanNeraca($tahun){
		$th = new LaporanNeracasController;
		$temp = $th->temp($tahun);
		
		$akunAktivaLancar      = $temp['akunAktivaLancar'];
		$total_harta           = $temp['total_harta'];
		$akunHutang            = $temp['akunHutang'];
		$akunModal             = $temp['akunModal'];
		$laba_tahun_berjalan   = $temp['laba_tahun_berjalan'];
		$akunAktivaTidakLancar = $temp['akunAktivaTidakLancar'];

		$pdf = PDF::loadView('pdfs.laporan_neraca', compact(
			'akunAktivaLancar',
			'total_harta',
			'akunHutang',
			'akunModal',
			'laba_tahun_berjalan',
			'akunAktivaTidakLancar'
		))
			->setPaper('a4')
			->setOrientation('landscape')
			->setOption('margin-bottom', 10);
		return $pdf->stream();
	}
	public function jurnalUmum($bulan, $tahun){
		$ju = JurnalUmum::with('coa', 'jurnalable')->where('created_at', 'like', $tahun . '-' . $bulan . '%')
			->get();
		$jurnals = [];
		foreach ($ju as $j) {
			$jurnals[$j->jurnalable_id.$j->jurnalable_type][] = $j;
		}

		$pdf = PDF::loadView('pdfs.jurnal_umum', compact(
			'jurnals'
		))
			->setPaper('a4')
			->setOption('margin-bottom', 10);
		return $pdf->stream();
	}
	
	public function bukuBesar($bulan, $tahun, $coa_id){
		$jurnalumums = JurnalUmum::where('coa_id', $coa_id)
		->where('created_at', 'like', $tahun . '-' . $bulan . '%')
		->get();

		$pdf = PDF::loadView('pdfs.buku_besar', compact(
			'jurnalumums'
		))
			->setPaper('a4')
			->setOption('margin-bottom', 0);
		return $pdf->stream();
	}

	public function bagiHasilGigi($id)
	{
		$bayar = BagiGigi::find($id);

		$pembayaran_bulan_ini = BagiGigi::where('tanggal_mulai', 'like', $bayar->tanggal_mulai->format('Y-m') . '%' )->get();
		$total_pembayaran_bulan_ini = 0;
		$total_pph_bulan_ini = 0;
		foreach ($pembayaran_bulan_ini as $b) {
			$total_pembayaran_bulan_ini += $b->nilai;
			$total_pph_bulan_ini += $b->pph21;
		}

		$pdf = PDF::loadView('pdfs.bagi_hasil_gigi', compact(
			'bayar',
			'pembayaran_bulan_ini',
			'total_pph_bulan_ini',
			'total_pembayaran_bulan_ini'
		))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
	}
	public function pph21dokter($id){
		$pph = Pph21Dokter::find($id);
		return dd( $pph->staf->nama );
	}
	public function amortisasi($tahun){
		$pajak           = new PajaksController;
		$peralatans      = $pajak->queryAmortisasi( 'peralatan', 'belanja_peralatans', 'App\\\BelanjaPeralatan', 'fb.tanggal', $tahun);
		$jumlah_penyusutan = 0;
		$zuzuts = [];

		$array = [];
		foreach ($peralatans as $p) {
			$jumlah_penyusutan += $p->total_penyusutan - $p->susut_fiskal_tahun_lalu;
			$zuzuts[]= $p->total_penyusutan - $p->susut_fiskal_tahun_lalu;
			$array[$p->masa_pakai][] = $p;
		}
		if (!isset( $array[4] )) {
			$array[4] = [];
		}
		if (!isset( $array[8] )) {
			$array[8] = [];
		}
		if (!isset( $array[10] )) {
			$array[10] = [];
		}
		if (!isset( $array[20] )) {
			$array[20] = [];
		}
		$peralatans = $array;

		$bahan_bangunans = $pajak->queryAmortisasi( 'keterangan', 'bahan_bangunans', 'App\\\BahanBangunan', 'bp.tanggal_renovasi_selesai', $tahun);
		$array = [];
		foreach ($bahan_bangunans as $bb) {
			$jumlah_penyusutan +=  $bb->total_penyusutan - $bb->susut_fiskal_tahun_lalu;
			$zuzuts[]= $bb->total_penyusutan - $bb->susut_fiskal_tahun_lalu;
			$array[$bb->permanen][] = $bb;
		}
		if (!isset( $array[0] )) {
			$array[0] = [];
		}
		if (!isset( $array[1] )) {
			$array[1] = [];
		}
		$bahan_bangunans = $array;

		$pdf = PDF::loadView('pdfs.amortisasi', compact(
			'peralatans',
			'tahun',
			'jumlah_penyusutan',
			'bahan_bangunans'
		))
		->setPaper('legal')
		->setOrientation('landscape')
		->setOption('margin-bottom', 10);
        return $pdf->stream();
	}
	public function peredaranBruto(){
		$pb = new PajaksController;
		$peredaranBruto = $pb->queryPeredaranBruto();

		$total = 0;
		foreach ($peredaranBruto as $pb) {
			$total += $pb->total;
		}
		$pdf = PDF::loadView('pdfs.peredaranBruto', compact(
			'peredaranBruto',
			'total'
		))
		->setPaper('a4')
		->setOrientation('landscape')
		->setOption('margin-bottom', 10);
        return $pdf->stream();

	}
	public function kuatansiPerBulan($bulan, $tahun){

		$periksas = Periksa::where('tanggal', 'like', $tahun . '-' . $bulan . '%')->get();

		return view('pdfs.kuitansiPerBulan', compact(
			'periksas'
		));



		
	}
	public function strukPerBulan($bulan, $tahun)
	{

		$periksas          = Periksa::where('tanggal', 'like', $tahun . '-' . $bulan . '%')
									->where('tunai' , '>', '0')
									->take(20)
									->get();
        //return dd( $trxa );
		$pdf = PDF::loadView('pdfs.multiStruk', compact(
			'periksas'
		//))->setPaper(array(0, 0, 210, 810),'Potrait');
		))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
	}
	
	public function strukPerTanggal($tahun, $bulan, $tanggal)
	{

		$tanggal = $tahun . '-' . $bulan . '-' . $tanggal;
		$periksas          = Periksa::where('tanggal', $tanggal)
									->where('tunai' , '>', '0')
									->get();
		$nota_juals          = NotaJual::where('tanggal', $tanggal)
									->get();
		$pendapatans          = Pendapatan::where('created_at','like', $tanggal . '%')
									->get();
		$pdf = PDF::loadView('pdfs.multiStruk', compact(
			'periksas',
			'pendapatans',
			'nota_juals'
		//))->setPaper(array(0, 0, 210, 810),'Potrait');
		))
		->setOption('page-width', 72)
		->setOption('page-height', 297)
		->setOption('margin-top', 0)
		->setOption('margin-bottom', 0)
		->setOption('margin-right', 0)
		->setOption('margin-left', 0);
        return $pdf->stream();
	}
	public function piutangAsuransiBelumDibayar($asuransi_id, $mulai, $akhir){

		$asuransi             = Asuransi::find( $asuransi_id );
		$pendapatanController = new PendapatansController;
        $belum_dibayars       = $pendapatanController->belumDibayar( $mulai, $akhir, $asuransi_id );

		$total_piutang       = 0;
		$total_sudah_dibayar = 0;
		$total_sisa_piutang  = 0;

		foreach ($belum_dibayars as $belum) {
			$total_piutang       += $belum->piutang;
			$total_sudah_dibayar += $belum->total_pembayaran;
			$total_sisa_piutang  += $belum->piutang - $belum->total_pembayaran;
		}

		$pdf = PDF::loadView('pdfs.piutangAsuransiBelumDibayar', compact(
			'asuransi',
			'mulai',
			'total_piutang',
			'total_sudah_dibayar',
			'total_sisa_piutang',
			'akhir',
			'belum_dibayars'
		))->setPaper('a4')->setOrientation('portrait')->setWarnings(false);

		return $pdf->stream();
	}
	public function piutangAsuransiSudahDibayar($asuransi_id, $mulai, $akhir){
		$asuransi             = Asuransi::find( $asuransi_id );
		$pendapatanController = new PendapatansController;
        $sudah_dibayars       = $pendapatanController->sudahDibayar( $mulai, $akhir, $asuransi_id );

		$total_tunai          = 0;
		$total_piutang        = 0;
		$total_sudah_dibayar  = 0;
		$total_sisa_piutang   = 0;

		foreach ($sudah_dibayars as $sudah) {
			$total_tunai         += $sudah->tunai;
			$total_piutang       += $sudah->piutang;
			$total_sudah_dibayar += $sudah->sudah_dibayar;
			$total_sisa_piutang  += $sudah->piutang - $sudah->sudah_dibayar;
		}

		$pdf = PDF::loadView('pdfs.piutangAsuransiSudahDibayar', compact(
			'asuransi',
			'mulai',
			'pembayaran_asuransi',
			'total_piutang',
			'total_tunai',
			'total_sudah_dibayar',
			'total_sisa_piutang',
			'akhir',
			'sudah_dibayars',
			'total_pembayaran'
		))->setPaper('a4')->setOrientation('portrait')->setWarnings(false);
		return $pdf->stream();
	}
	public function piutangAsuransi($asuransi_id, $mulai, $akhir){

		$asuransiController = new AsuransisController;

		$piutangs = $asuransiController->querySemuaPiutangPerBulan($asuransi_id, $mulai, $akhir  );

		$asuransi = Asuransi::find( $asuransi_id );

		$total_tunai         = 0;
		$total_piutang       = 0;
		$total_sudah_dibayar = 0;

		foreach ($piutangs as $piutang) {
			$total_tunai         += $piutang->tunai;
			$total_piutang       += $piutang->piutang;
			$total_sudah_dibayar += $piutang->sudah_dibayar;
		}

		$pdf = PDF::loadView('pdfs.piutangAsuransi', compact(
			'mulai',
			'asuransi',
			'akhir',
			'piutangs',
			'total_piutang',
			'total_sudah_dibayar',
			'total_tunai'
		))->setPaper('a4')->setOrientation('portrait')->setWarnings(false);
		return $pdf->stream();
	}
}