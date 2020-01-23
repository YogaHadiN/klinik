<?php
namespace App\Http\Controllers;

use Input;

use App\Http\Requests;
use Log;
use App\FakturBelanja;
use App\Http\Controllers\JurnalUmumsController;
use App\PenjualanAset;
use App\Pph21Dokter;
use App\Http\Controllers\PasiensAjaxController;
use App\BayarHutangHarta;
use App\Penyusutan;
use App\Config;
use App\Tarif;
use App\InputHarta;
use DateTime;
use App\GolonganPeralatan;
use App\GoPay;
use App\JenisPengeluaran;
use App\Classes\Yoga;
use App\BukanObat;
use App\Http\Controllers\PengeluaransController;
use App\BagiGigi;
use App\CheckoutDetail;
use App\Pembelian;
use App\Pasien;
use App\BelanjaPeralatan;
use App\Pendapatan;
use App\Diagnosa;
use App\PembayaranAsuransi;
use App\Modal;
use App\Supplier;
use App\BayarGaji;
use App\User;
use App\Coa;
use App\GajiGigi;
use App\Penjualan;
use App\TransaksiPeriksa;
use App\CheckoutKasir;
use App\BayarDokter;
use App\Periksa;
use App\JurnalUmum;
use App\Ac;
use App\Staf;
use Image;
use DB;
use Hash;
use App\Pengeluaran;

class PengeluaransController extends Controller
{



	/**
	 * Display a listing of the resource.
	 * GET /pengeluarans
	 *
	 * @return Response
	 */
	public function __construct()
	 {
	     $this->middleware('super', ['only' => ['bayar_gaji_karyawan', 'nota_z']]);
	     $this->middleware('notready', ['only' => ['nota_z']]);
	 }
	public function index($id)
	{
		$fakturbelanja = FakturBelanja::find($id);

		$jenis_pengeluarans = Yoga::jenisPengeluaranList();
		$bukanObat = BukanObat::all(['nama']);

		$temp = [];

		foreach ($bukanObat as $nama) {
			$temp[] = $nama->nama;
		}
		$bukanObat = json_encode($temp);
		return view('pengeluarans.index', compact('fakturbelanja', 'jenis_pengeluarans', 'bukanObat'));
	}

	public function store() {
		$messages          = array(
			'required'    => ':attribute harus diisi terlebih dahulu',
		);
		$rules             = [
			'staf_id'      => 'required',
			'supplier_id'  => 'required',
			'nilai'        => 'required',
			'faktur_image' => 'required',
			'tanggal'      => 'required|date_format:d-m-Y',
			'sumber_uang'  => 'required',
			'keterangan'   => 'required'
		];
		$validator         = \Validator::make($data = Input::all(), $rules, $messages);
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator->messages())->withInput();
		}
		$staf_id           = Input::get('staf_id');
		$supplier_id       = Input::get('supplier_id');
		$nilai             = Yoga::clean( Input::get('nilai') );
		$tanggal           = Input::get('tanggal');
		$keterangan        = Input::get('keterangan');

		$peng                 = new Pengeluaran;
		$peng->staf_id        = $staf_id;
		$peng->supplier_id    = $supplier_id;
		$peng->nilai          = $nilai;
		$peng->tanggal        = Yoga::datePrep( $tanggal );
		$peng->sumber_uang_id = Input::get('sumber_uang');
		$peng->keterangan     = $keterangan;
		$peng->save();
		$peng->faktur_image   = $this->imageUpload('faktur', 'faktur_image', $peng->id);
		$confirm              = $peng->save();

		if ($confirm) {
			$jurnals = [];
			$timestamp = $peng->created_at;
			$jurnals[] = [
				'jurnalable_id'   => $peng->id,
				'jurnalable_type' => 'App\Pengeluaran',
				'debit'           => 1,
				'coa_id'           => null,
				'nilai'           => $peng->nilai,
				'created_at'      => $timestamp,
				'updated_at'      => $timestamp
			];

			$jurnals[] = [
				'jurnalable_id'   => $peng->id,
				'jurnalable_type' => 'App\Pengeluaran',
				'coa_id'          => Input::get('sumber_uang'),
				'debit'           => 0,
				'nilai'           => $peng->nilai,
				'created_at'      => $timestamp,
				'updated_at'      => $timestamp
			];
			JurnalUmum::insert($jurnals);
		}
		$nama_supplier = Supplier::find($supplier_id)->nama;
		if ($confirm) {
			return redirect('suppliers/belanja_bukan_obat')->withPesan(Yoga::suksesFlash('Transaksi Uang Keluar kepada ' . $nama_supplier . ' senilai <span class=uang>' . $nilai .'</span> berhasil dilakukan'))->withPrint($peng->id);

		}else {
			return redirect('suppliers/belanja_bukan_obat')->withPesan(Yoga::gagalFlash('Transaksi Uang Keluar kepada ' . $nama_supplier . ' senilai <span class=uang>' . $nilai .'</span> gagal dilakukan'));
		}
	}

	public function lists() {
		$mulai = Yoga::datePrep(Input::get('mulai'));
		$akhir = Yoga::datePrep(Input::get('akhir'));

		$notas = FakturBelanja::where('tanggal', '>=', $mulai)->where('tanggal', '<=', $akhir)->get();
		return view('pengeluarans.list', compact('notas', 'mulai', 'akhir'));
	}


	public function show($id){
		$pengeluaran = Pengeluaran::find($id);
		return view('pengeluarans.show', compact('pengeluaran'));

	}

	public function ketkeluar(){
		$keterangan = Input::get('keterangan');

		$bukanObat = BukanObat::where('nama', $keterangan)->get();
		if ($bukanObat->count() > 0) {
			$result = [
				'confirm' => '1',
				'cont'    => [
					'jumlah'      => $bukanObat[0]->harga_beli,
					'jml_peng_id' => $bukanObat[0]->jenis_pengeluaran_id
				]
			];
		} else {
			$result = [
				'confirm' => '0'
			];
		}
		return json_encode($result);
    }

    public function bayardokterdetail(){
        $tanggal_mulai = Input::get('tanggal_mulai');
        $tanggal_akhir = Input::get('tanggal_akhir');
        $query = "select p.tanggal as tanggal, st.nama as nama_staf, ps.id as pasien_id, ps.nama as nama, asu.nama as nama_asuransi, tunai, piutang, nilai  from jurnal_umums as ju join periksas as p on p.id=ju.jurnalable_id join stafs as st on st.id= p.staf_id join pasiens as ps on ps.id=p.pasien_id join asuransis as asu on asu.id=p.asuransi_id where jurnalable_type='App\\\Periksa' and p.staf_id='{$id}' and ju.coa_id=200001 where date(p.tanggal) between '{$tanggal_mulai}' and '{$tanggal_akhir}';";
        $hutangs = DB::select($query);
        $total = 0;
        foreach ($hutangs as $hutang) {
            $total += $hutang->nilai;
        }
        return view('bayardokterdetail', compact('hutangs', 'total'));
    }

    public function bayardokter($id){
        $staf = Staf::find($id);
        return view('bayardokter', compact('staf'));
    }

    public function dokterbayar(){
		//return Input::all();
		$rules           = [
			'staf_id'   => 'required',
			'mulai'     => 'required',
			'jam_mulai' => 'required',
			'akhir'     => 'required',
			'jam_akhir' => 'required',
		];

		$validator = \Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
         
        $id        = Input::get('staf_id');
        $staf      = Staf::find($id);
		$mulai     = Input::get('mulai');
		$jam_mulai = Input::get('jam_mulai');
		$akhir     = Input::get('akhir');
		$jam_akhir = Input::get('jam_akhir');

		$mulai = Yoga::datePrep($mulai);
		$mulai = $mulai . ' ' . $jam_mulai;
		$akhir = Yoga::datePrep($akhir);
		$akhir = $akhir . ' ' . $jam_akhir;
         
		$hutangs = Periksa::with('transaksii.jenisTarif', 'pasien', 'asuransi', 'jurnals')
					->whereRaw("created_at between '" . $mulai . "' and '" . $akhir . "'")
					->where('staf_id', $id)
					->get();
        $total   = $this->total($id, $mulai, $akhir);

		if ( $staf->ada_penghasilan_lain == '1' ) {
			$ada_penghasilan_lain = '1';
		} else {
			$ada_penghasilan_lain = null;
		}

		return view('dokterbayar', compact(
			'hutangs', 
			'total',
			'ada_penghasilan_lain',
		   	'staf',
		   	'mulai',
		   	'akhir',
			'id'
		));
    }

    
    public function dokterdibayar(){

		$rules           = [
			'staf_id'              => 'required',
			'hutang'               => 'required',
			'dibayar'              => 'required',
			'petugas_id'           => 'required',
			'sumber_uang_id'       => 'required',
			'ada_penghasilan_lain' => 'required'
		];
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}

        $staf_id                       = Input::get('staf_id');
        $petugas_id                    = Input::get('petugas_id');
        $mulai                         = Input::get('mulai');
        $akhir                         = Input::get('akhir');
        $total                         = $this->total($staf_id, $mulai, $akhir);
        $hutang                        = Input::get('hutang');
        $jasa_dokter                   = Input::get('jasa_dokter');
        $dibayar                       = Yoga::clean( Input::get('dibayar') );
        $sumber_uang_id                = Input::get('sumber_uang_id');

		$bayar_dokter_id = (int)BayarDokter::orderBy('id', 'desc')->first()->id + 1;
        $staf                          = Staf::find($staf_id);

		$bayar_dokters = [];

		$jurnals = [];
        if ($dibayar > 0) {
			$timestamp = date('Y-m-d H:i:s');

			$bayar_dokters[] = [

                'id'                   => $bayar_dokter_id,
                'staf_id'              => $staf_id,
                'petugas_id'           => $petugas_id,
                'bayar_dokter'         => $dibayar,
                'hutang'               => $hutang,
                'mulai'                => $mulai ,
                'akhir'                => $akhir ,
                'sumber_uang_id'       => $sumber_uang_id ,
				'ada_penghasilan_lain' => Input::get('ada_penghasilan_lain'),
				'created_at'           => $timestamp,
				'updated_at'           => $timestamp

			];
				
			if ($dibayar == $hutang) {

				$jurnals[]             = [
					'jurnalable_id'   => $bayar_dokter_id,
					'jurnalable_type' => 'App\BayarDokter',
					'coa_id'          => 200001, // Hutang Kepada Dokte,
					'debit'           => 1,
					'nilai'           => $bayar->bayar_dokter,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];

				$jurnals[]             = [
					'jurnalable_id'   => $bayar_dokter_id,
					'jurnalable_type' => 'App\BayarDokter',
					'coa_id'          => $sumber_uang_id,
					'debit'           => 0,
					'nilai'           => $bayar->bayar_dokter,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];
			} else if($dibayar  > $hutang){
				//Create Jurnal Umum untuk hutang saja
				$jurnals[]             = [
					'jurnalable_id'   => $bayar_dokter_id,
					'jurnalable_type' => 'App\BayarDokter',
					'coa_id'          => 200001, // Hutang Kepada Dokte,
					'debit'           => 1,
					'nilai'           => $hutang,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];

				$jurnals[]             = [
					'jurnalable_id'   => $bayar_dokter_id,
					'jurnalable_type' => 'App\BayarDokter',
					'coa_id'          => $sumber_uang_id,
					'debit'           => 0,
					'nilai'           => $hutang,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];
				//Jurnal Umum untuk sisa dengan b. operasional jasa dokter
				$jurnals[]             = [
					'jurnalable_id'   => $bayar_dokter_id,
					'jurnalable_type' => 'App\BayarDokter',
					'coa_id'          => 50201, // B. Produksi Jasa Dokte,
					'debit'           => 1,
					'nilai'           => $dibayar - $hutang,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];
				$jurnals[]             = [
					'jurnalable_id'   => $bayar_dokter_id,
					'jurnalable_type' => 'App\BayarDokter',
					'coa_id'          => $sumber_uang_id,
					'debit'           => 0,
					'nilai'           => $dibayar - $hutang,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];

			} else if($dibayar  < $hutang){


				//Jurnal Umum untuk sisa dengan b. operasional jasa dokter
				$jurnals[]             = [
					'jurnalable_id'   => $bayar_dokter_id,
					'jurnalable_type' => 'App\BayarDokter',
					'coa_id'          => 200001, // Hutang kepada dokte,
					'debit'           => 1,
					'nilai'           => $hutang- $dibayar,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];

				$jurnals[]             = [
					'jurnalable_id'   => $bayar_dokter_id,
					'jurnalable_type' => 'App\BayarDokter',
					'coa_id'          => 50201, // B. Produksi Jasa Dokte,
					'debit'           => 0,
					'nilai'           => $hutang- $dibayar,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];

				//Create Jurnal Umum untuk hutang saja
				$jurnals[]             = [
					'jurnalable_id'   => $bayar_dokter_id,
					'jurnalable_type' => 'App\BayarDokter',
					'coa_id'          => 200001, // Hutang kepada dokte,
					'debit'           => 1,
					'nilai'           => $dibayar,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];

				//end
				$jurnals[]             = [
					'jurnalable_id'   => $bayar_dokter_id,
					'jurnalable_type' => 'App\BayarDokter',
					'coa_id'          => $sumber_uang_id,
					'debit'           => 0,
					'nilai'           => $dibayar,
					'created_at'      => $timestamp,
					'updated_at'      => $timestamp
				];

			}

			//
			//pph21 batal dipotong
			//

			// tahun dihitung adalah tahun dimana dokter mulai bekerja

			DB::beginTransaction();
			try {
				BayarDokter::insert($bayar_dokters);
				JurnalUmum::insert($jurnals);
				DB::commit();
			} catch (\Exception $e) {
				DB::rollback();
				throw $e;
			}
			$pesan = Yoga::suksesFlash('Gaji ' . $staf->nama . ' sebesar ' . Yoga::buatrp( $dibayar ) . '. Berhasil diinput' );
			return redirect('pengeluarans/bayardoker')->withPesan($pesan)->withPrint($bayar_dokter_id);
        } else {
            $pesan = Yoga::gagalFlash('Gaji ' . $staf->nama . ' sebesar Rp. ' . Yoga::buatrp( $dibayar ) . ',- . Gagal diinput' );
            return redirect('pengeluarans/bayardoker')->withPesan($pesan);
        }

    }
    public function bayar(){
        $bayar_dokters = BayarDokter::with('staf', 'petugas')->latest()->paginate(30);
        return view('formbayardokter', compact('bayar_dokters')
    }
    
    public function nota_z(){
        $checkout = CheckoutKasir::latest()->first();
		//return dd( $checkout );

        $tanggal = $checkout->created_at;
		//return $tanggal;

        $jurnal_umum_id = $checkout->jurnal_umum_id;

        $tindakans = [];

		$modals = Modal::where('created_at', '>', $tanggal)->get();

		$totalModal = 0;
		foreach ($modals as $modal) {
			$totalModal += $modal->modal;
		}

		$pengeluarans = JurnalUmum::with('jurnalable')
									->where('coa_id', 110000)
                                    ->where('debit', '0')
                                    ->where('id', '>=', $checkout->jurnal_umum_id)
                                    ->where('created_at', '>=', $checkout->created_at)
                                    ->where('jurnalable_type', 'not like', 'App\\\CheckoutKasir')
                                    ->get();

        $totalPengeluarans = 0;
        foreach ($pengeluarans as $peng) {
            $totalPengeluarans += $peng->nilai;
        }
        $jurnalumums = JurnalUmum::with('coa')->where('id', '>=', $checkout->jurnal_umum_id)->get();;

        $asuransis = Periksa::where('created_at', '>=', $tanggal)->groupBy('asuransi_id')->get();

		$uang_masuks = JurnalUmum::with('jurnalable')->where('id', '>=', $checkout->jurnal_umum_id)
									->where('coa_id', 110000)
									->where('jurnalable_type', '!=', 'App\Modal')
									->where('jurnalable_type', '!=', 'App\CheckoutKasir')
                                    ->where('created_at', '>=', $checkout->created_at)
									->where('debit', '1')
									->get();

        $modal_awals = JurnalUmum::where('id', '>=', $checkout->jurnal_umum_id)
                                    ->where('coa_id', 110000)
                                    ->where('jurnalable_type', 'App\Modal')
                                    ->where('created_at', '>=', $checkout->created_at)
                                    ->where('debit', '1')
                                    ->get();

        $uang_keluar = JurnalUmum::where('id', '>=', $checkout->jurnal_umum_id)
                                    ->where('coa_id', 110000)
                                    ->where('jurnalable_type', '!=', 'App\Modal')
                                    ->where('created_at', '>=', $checkout->created_at)
                                    ->where('jurnalable_type', '!=', 'App\CheckoutKasir')
                                    ->where('debit', '0')
                                    ->get();
        $modal_awal = 0;
        foreach ($modal_awals as $md) {
            $modal_awal += $md->nilai;
        }

        $total_uang_masuk = 0;
        foreach ($uang_masuks as $penjualan) {
            
			$total_uang_masuk += $penjualan->nilai;
        }
        $total_uang_keluar = 0;
        foreach ($uang_keluar as $penjualan) {
            $total_uang_keluar += $penjualan->nilai;
        }
        $checkouts = CheckoutKasir::latest()->paginate(20);
        $uang_di_kasir = $modal_awal + $total_uang_masuk - $total_uang_keluar;

		$pembayaran_asuransis = PembayaranAsuransi::where('created_at', '>', $tanggal)->get();
		$total_pembayaran_asuransi = 0;
		foreach ($pembayaran_asuransis as $p) {
			$total_pembayaran_asuransi += $p->pembayaran;
		}

		$periksas = DB::select("SELECT *, p.id as periksa_id, ps.nama as nama_pasien, asu.nama as nama_asuransi, p.id as periksa_id, p.poli as poli FROM periksas as p LEFT OUTER JOIN pasiens as ps on ps.id = p.pasien_id LEFT OUTER JOIN asuransis as asu on asu.id = p.asuransi_id where p.created_at > '{$tanggal}' AND p.lewat_kasir = '1'");
		$tunai_periksa = 0;
		foreach ($periksas as $p) {
			$tunai_periksa += $p->tunai;
		}

		$tunai_beli_obat = 0;
		$penjualans = Penjualan::where('created_at', '>', $tanggal)->get();
		foreach ($penjualans as $p) {
			$tunai_beli_obat += $p->harga_jual * $p->jumlah;
		}
		$pendapatans = Pendapatan::where('created_at', '>', $tanggal)->get();
		$total_pendapatan_lain = 0;
		foreach ($pendapatans as $p) {
			$total_pendapatan_lain += $p->nilai;
		}

		return view('pengeluarans.notaz', compact(
			'checkouts', 
			'tanggal', 
			'total_pendapatan_lain', 
			'pendapatans', 
			'tunai_beli_obat', 
			'tunai_periksa', 
			'asuransis', 
			'pembayaran_asuransis', 
			'total_uang_masuk', 
			'total_pembayaran_asuransi', 
			'total_uang_keluar', 
			'uang_di_kasir', 
			'modal_awal', 
			'checkout', 
			'pengeluarans', 
			'totalPengeluarans', 
			'modals', 
			'totalModal'
		));
    }
    public function notaz_post(){

		if (gethostname() == 'kje') {
			$checkout = new PengeluaransController;
			$saldo_saat_ini = $checkout->parameterKasir()['uang_di_kasir'];
		} else {
			exec("mysqldump -u root -pYogaman89 jatielok | gzip > ~/Dropbox/backup11/database_`date '+%m-%d-%Y_%H:%M:%S'`.sql.gz");
		}
		$uang_di_kasir     = $this->parameterKasir()['uang_di_kasir'];
		$total_uang_keluar = $this->parameterKasir()['total_uang_keluar'];
		$uang_di_tangan    = $this->parameterKasir()['uang_di_tangan'];
		$modal_awal        = $this->parameterKasir()['modal_awal'];
		$modal_ids         = $this->parameterKasir()['modal_ids'];
		$tanggal           = $this->parameterKasir()['tanggal'];
		$total_uang_masuk  = $this->parameterKasir()['total_uang_masuk'];
        $query = "select min(jenis_tarif_id) as jenis_tarif_id, min( jt.jenis_tarif ) as jenis_tarif, count(tp.biaya) as jumlah  from transaksi_periksas as tp join periksas as px on px.id=tp.periksa_id join jenis_tarifs as jt on jt.id = tp.jenis_tarif_id where tp.created_at >= '{$tanggal}' group by tp.jenis_tarif_id";
        $transaksis = DB::select($query);
        $pengeluarans = JurnalUmum::where('coa_id', 110000)
                                    ->where('debit', '0')
                                    ->where('created_at', '>=', $tanggal)
                                    ->where('jurnalable_type', '!=', 'App\CheckoutKasir')
                                    ->get(['id']);
        $detail_pengeluarans = [];
        foreach ($pengeluarans as $peng) {
            $detail_pengeluarans[] = $peng->id;
        } 
        $pengeluarans_tangan = JurnalUmum::where('coa_id', 110004)
                                    ->where('debit', '0')
                                    ->where('created_at', '>=', $tanggal)
                                    ->where('jurnalable_type', '!=', 'App\CheckoutKasir')
                                    ->where('jurnalable_type', '!=', 'App\Modal')
                                    ->get(['id']);
        $detail_pengeluarans_tangan = [];
        foreach ($pengeluarans_tangan as $peng) {
            $detail_pengeluarans_tangan[] = $peng->id;
        } 
        $detail_pengeluarans = json_encode( $detail_pengeluarans );
        //pindahkan semua kas di kasir menjadi kas di tangan

        $new_z                 = new CheckoutKasir;
        $new_z->modal_awal     = $modal_awal;
        $new_z->uang_di_kasir  = $uang_di_kasir;
        $new_z->uang_di_tangan = $uang_di_tangan + $uang_di_kasir;
		$last_jurnal_id        = JurnalUmum::orderBy('id', 'desc')->first()->id;

        if (isset($last_jurnal_id)) {
			$new_z->jurnal_umum_id = $last_jurnal_id;
		} else {
            $new_z->jurnal_umum_id = 1;
        }

        $new_z->uang_masuk               = $total_uang_masuk;
        $new_z->uang_keluar              = $total_uang_keluar;
        $new_z->detil_pengeluarans       = $detail_pengeluarans;
        $new_z->detil_pengeluaran_tangan = json_encode( $detail_pengeluarans_tangan );
        $new_z->detil_modals             = json_encode($modal_ids);

        $confirm = $new_z->save();
        if ($confirm) {
            $jurnal                  = new JurnalUmum;
            $jurnal->jurnalable_id   = $new_z->id;
            $jurnal->jurnalable_type = 'App\CheckoutKasir';
            $jurnal->coa_id          = 110004; // Kas di tangan
            $jurnal->debit           = 1;
            $jurnal->nilai           = $uang_di_kasir;
            $jurnal->save();

            $jurnal                  = new JurnalUmum;
            $jurnal->jurnalable_id   = $new_z->id;
            $jurnal->jurnalable_type = 'App\CheckoutKasir';
            $jurnal->coa_id          = 110000; // Kas di kasir
            $jurnal->debit           = 0;
            $jurnal->nilai           = $uang_di_kasir;
            $jurnal->save();
        }
        //tambah semua komponen yang masuk kas, retrieve semua last id nya
        $pesan = Yoga::suksesFlash('Transaksi Checkout ( Nota Z ) tanggal ' . $new_z->created_at . ' <strong>Berhasil</strong> dilakukan');
		return redirect('pengeluarans/nota_z')
			->withPesan($pesan)
			->withModals($modal_awal)
			->withPrint($new_z->id);
    }

    public function erce(){
        $modals = Modal::with('coa')->latest()->paginate(20);
        $sumberUangList = [
            null => '-pilih-',
            301000 => 'Modal',
            110004 => 'Kas di tangan'
        ];
         return view('pengeluarans.rc', compact('modals', 'sumberUangList'));
    }
    public function erce_post(){
        //menambah modal
		
		$rules = [
			'sumber_uang' => 'required',
			'kas_masuk' => 'required',
			'staf_id' => 'required'
		];

		$validator = \Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
        $modal = new Modal;
        $modal->coa_kas_id = Input::get('sumber_uang');
        $modal->modal = Yoga::clean( Input::get('kas_masuk') );
        $modal->staf_id = Input::get('staf_id');
        $modal->keterangan = Input::get('keterangan');
        $modal->save();
        
        $jurnal                  = new JurnalUmum;
        $jurnal->jurnalable_id   = $modal->id;
        $jurnal->jurnalable_type = 'App\Modal';
        $jurnal->coa_id          = 110000; // Kas di kasir
        $jurnal->debit           = 1;
        $jurnal->nilai           = $modal->modal;
        $jurnal->save();

        if ( Input::get('sumber_uang') == 301000 ) {
            $sumberModal = true;
        } else {
            $sumberModal = false;
        }
        //return dd( $sumberModal );
        if ($sumberModal) {
            $jurnal                  = new JurnalUmum;
            $jurnal->jurnalable_id   = $modal->id;
            $jurnal->jurnalable_type = 'App\Modal';
            $jurnal->coa_id          = 301000; // modal
            $jurnal->debit           = 0;
            $jurnal->nilai           = $modal->modal;
            $jurnal->save();
        }else {
            $jurnal                  = new JurnalUmum;
            $jurnal->jurnalable_id   = $modal->id;
            $jurnal->jurnalable_type = 'App\Modal';
            $jurnal->coa_id          = 110004; // kas di tangan
            $jurnal->debit           = 0;
            $jurnal->nilai           = $modal->modal;
            $jurnal->save();
        }
        $pesan = Yoga::suksesFlash('Modal senilai <strong><span class="uang">' . $modal->modal. '</span></strong> telah <strong>BERHASIL</strong> ditambahkan dengan sumber modal dari <strong>'. $jurnal->coa->coa .'</strong>');
        return redirect('pengeluarans/rc')->withpesan($pesan)->withPrint($modal->id);

    }

    public function show_checkout($id){

        $notaz = CheckoutKasir::find($id);
		$buka_kasir = CheckoutKasir::find($id - 1)->created_at;
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
		return view('pengeluarans.show_checkout', compact(
			'notaz', 
			'pengeluarans',
			'pengeluarans_tangan',
			'modals', 
			'buka_kasir', 
			'total_modal', 
			'total_pengeluaran',
			'total_pengeluaran_tangan',
			'total_pemasukan',
			'checkout'
		));
    }
    
    
    private function hutangs($id, $mulai, $akhir){
         
        $query = "select p.id as periksa_id, p.tanggal as tanggal, st.nama as nama_staf, ps.id as pasien_id, ps.nama as nama, asu.nama as nama_asuransi, tunai, piutang, nilai  from jurnal_umums as ju join periksas as p on p.id=ju.jurnalable_id join stafs as st on st.id= p.staf_id join pasiens as ps on ps.id=p.pasien_id join asuransis as asu on asu.id=p.asuransi_id where jurnalable_type='App\\\Periksa' and p.staf_id='{$id}' and ju.coa_id=200001 and ( date(p.created_at) between '{$mulai}' and '{$akhir}' );";
        $hutangs = DB::select($query);

        return $hutangs;
    }
    private function total($id, $mulai, $akhir){
        $query = "select p.id as periksa_id, p.tanggal as tanggal, st.nama as nama_staf, ps.id as pasien_id, ps.nama as nama, asu.nama as nama_asuransi, tunai, piutang, nilai  from jurnal_umums as ju join periksas as p on p.id=ju.jurnalable_id join stafs as st on st.id= p.staf_id join pasiens as ps on ps.id=p.pasien_id join asuransis as asu on asu.id=p.asuransi_id where jurnalable_type='App\\\Periksa' and p.staf_id='{$id}' and ju.coa_id=200001 and ( date(p.created_at) between '{$mulai}' and '{$akhir}' );";
        $hutangs = DB::select($query);
        $total = 0;
        foreach ($hutangs as $hutang) {
            $total += $hutang->nilai;
        }
        return $total;
    }
    public function notaz_detail($id){
         $ids = json_decode($id, true);
         $periksas = Periksa::whereIn('id', $ids)->paginate(4);
		return view('pengeluarans.detail_transaksi', compact('periksas'));
    }
    public function confirm_staf(){
        $email = Input::get('email');
        $password = Input::get('password');

        $user = User::where('email', $email)->first();
        if ($user) {
            $hashedPassword = $user->password; 
        } else {
            return '0';// user belum terdaftar
        }

        if( Hash::check($password, $hashedPassword) ){
            if ($email == 'yoga_email@yahoo.com') {
                return '1'; // user berhasil confirm
            }else{
                return '2';// user tidak punya otoritas
            }
        }else {
            return '3'; // kombinasi email / password salah
        }
    }
    public function bayar_gaji_karyawan(){
	
        $sumber_kas_lists = [null => '-Pilih-'] + Coa::where('id', 'like', '110%')->where('id', 'not like', '110000')->lists('coa', 'id')->all();
        $pembayarans = BayarGaji::latest()->paginate(10);
        return view('pengeluarans.bayar_gaji_karyawan', compact(  'pembayarans' , 'sumber_kas_lists'));
    }
    
   public function bayar_gaji(){
	   DB::beginTransaction();

	   try {
		   $rules                = [
			 "coa_id"           => "required",
			  "staf_id"         => "required",
			  "bulan"           => "required",
			  "tanggal_dibayar" => "required",
			  "gaji_pokok"      => "required",
			  "bonus"           => "required"
		   ];
		   
		   $validator = \Validator::make(Input::all(), $rules);
		   
		   if ($validator->fails())
		   {
			return \Redirect::back()->withErrors($validator)->withInput();
		   }

		   $staf_id         = Input::get('staf_id');
		   $coa_id          = Input::get('coa_id');
		   $bulan           = Input::get('bulan');
		   $bulan           = Yoga::blnPrep($bulan);
		   $gaji_pokok      = Yoga::clean( Input::get('gaji_pokok') );
		   $bonus           = Yoga::clean( Input::get('bonus'));
		   $tanggal_dibayar = Yoga::datePrep( Input::get('tanggal_dibayar') );

			   $jus = JurnalUmum::where('coa_id', '200002' )
				   ->where('debit', '0')
				   ->where('created_at', 'like', $bulan . '%')
				   ->get();
			   $total_bonus = 0;
			   foreach ($jus as $ju) {
				   $total_bonus += $ju->nilai;
			   }

			   $jus = JurnalUmum::where('coa_id', '200002' )
				   ->where('debit', '1')
				   ->where('created_at', 'like', $bulan . '%')
				   ->get();

			   $total_bonus_sudah_dibayar = 0;
			   foreach ($jus as $ju) {
				   $total_bonus_sudah_dibayar += $ju->nilai;
			   }
			   //return $total_bonus_sudah_dibayar . ' total bonys sudah dibayar';

			   
			   $sisa_hutang_bonus  = $total_bonus - $total_bonus_sudah_dibayar;
			   $data               = 'total_bonus = ' . $total_bonus . '<br />';
			   $data              .= 'total_bonus_sudah_dibayar = ' . $total_bonus_sudah_dibayar . '<br />';
			   $data              .= 'sisa_hutang_bonus = ' . $sisa_hutang_bonus . '<br />';
			   $data              .= 'bonus = ' . $bonus . '<br />';


		   $bg                  = new BayarGaji;
		   $bg->staf_id         = $staf_id;
		   $bg->mulai           = $bulan . '-01';
		   $bg->akhir           = date("Y-m-t", strtotime($bulan . '-01'));
		   $bg->tanggal_dibayar = $tanggal_dibayar;
		   $bg->gaji_pokok      = $gaji_pokok;
		   $bg->bonus           = $bonus;
		   $bg->kas_coa_id      = $coa_id;
		   $confirm             = $bg->save();

		   $jurnals = [];

		   $timestamp = date('Y-m-t 23:59:59', strtotime($bulan . '-01'));
		   if ($gaji_pokok > 0) {
			   $jurnals[] = [
				   'jurnalable_id'   => $bg->id,
				   'jurnalable_type' => 'App\BayarGaji',
				   'coa_id'          => 60101,
				   'debit'           => 1,
				   'created_at'      => $timestamp,
				   'updated_at'      => $timestamp,
				   'nilai'           => $gaji_pokok
			   ];
		   }
		   //return $jus;
		   // Hitung hutang kepada asisten dalam satu bulan, jika hutangnya masih lebih banyak lebih dari bonus dari pada yang sudah dibayarkan, maka jurnal masuk semua ke hutang
		   if ($sisa_hutang_bonus >= $bonus) {
			   if ($bonus > 0) {
				   $jurnals[] = [
					   'jurnalable_id'   => $bg->id,
					   'jurnalable_type' => 'App\BayarGaji',
					   'coa_id'          => 200002, // Hutang Kepada Asisten Dokte,
					   'debit'           => 1,
					   'created_at'      => $timestamp,
					   'updated_at'      => $timestamp,
					   'nilai'           => $bonus,
				   ];
			   }
		   } else{
			   if ($sisa_hutang_bonus > 0) {
				   $beban_produksi_hutang_asisten = $bonus - $sisa_hutang_bonus;
			   }else{
				   $beban_produksi_hutang_asisten = $bonus;
			   }
			   if ($sisa_hutang_bonus > 0) {
				   $jurnals[] = [
					   'jurnalable_id'   => $bg->id,
					   'jurnalable_type' => 'App\BayarGaji',
					   'coa_id'          => 200002,// Hutang Kepada Asisten Dokte,
					   'debit'           => 1,
					   'created_at'      => $timestamp,
					   'updated_at'      => $timestamp,
					   'nilai'           => $sisa_hutang_bonus,
				   ];
			   }

			   if ($beban_produksi_hutang_asisten > 0) {
				   $jurnals[] = [
					   'jurnalable_id'   => $bg->id,
					   'jurnalable_type' => 'App\BayarGaji',
					   'coa_id'          => 50205,
					   'created_at'      => $timestamp,
					   'updated_at'      => $timestamp,
					   'debit'           => 1,
					   'nilai'           => $beban_produksi_hutang_asisten,
				   ];
			   }
		   }

		   if ($bonus + $gaji_pokok > 0) {
			   $jurnals[] = [
				   'jurnalable_id'   => $bg->id,
				   'jurnalable_type' => 'App\BayarGaji',
				   'coa_id'          => $coa_id, //Kas Sumbe,
				   'created_at'      => $timestamp,
				   'updated_at'      => $timestamp,
				   'debit'           => 0,
				   'nilai'           => $gaji_pokok + $bonus
			   ];
		   }

		   $gajis = BayarGaji::where('staf_id', $staf_id)
			   ->where("mulai", 'like', $bg->mulai->format('Y-m') . '%')
			   ->get();

		   $staf_ini = Staf::find( $staf_id );
		   $ptkp = Config::where('config_variable', 'Penghasilan Tidak Kena Pajak')->first()->value;

		   $total_gaji_bulan_ini = 0;
		   $total_pph_bulan_ini  = 0;

		   foreach ($gajis as $gaji) {
			   $total_gaji_bulan_ini += $gaji->gaji_pokok + $gaji->bonus;
			   $total_pph_bulan_ini  += $gaji->pph21;
		   }

		   $perhitunganPph_ini = $this->pph21($total_gaji_bulan_ini, $total_pph_bulan_ini, $staf_ini, $ptkp);
		   $tahun_pembayaran  = date("Y", strtotime($bg->mulai));
		   $parameterPtkp      = $this->parameterPtkp($staf_ini, $tahun_pembayaran, 'App\BayarGaji', 'mulai');
		   $menikah            = $parameterPtkp['menikah'];
		   $jumlah_anak        = $parameterPtkp['jumlah_anak'];

		   $bg->total_gaji_bulan_ini   = $total_gaji_bulan_ini;
		   $bg->pph21                  = $perhitunganPph_ini['pph21'];
		   $bg->biaya_jabatan          = $perhitunganPph_ini['biaya_jabatan'];
		   $bg->ptkp_dasar             = $perhitunganPph_ini['ptkp_dasar'];
		   $bg->ptkp                   = $perhitunganPph_ini['ptkp'];
		   $bg->potongan5persen        = $perhitunganPph_ini['potongan5persen'];
		   $bg->potongan15persen       = $perhitunganPph_ini['potongan15persen'];
		   $bg->potongan25persen       = $perhitunganPph_ini['potongan25persen'];
		   $bg->potongan30persen       = $perhitunganPph_ini['potongan30persen'];
		   $bg->npwp                   = $perhitunganPph_ini['npwp'];
		   $bg->gaji_netto             = $perhitunganPph_ini['gaji_netto'];
		   $bg->penghasilan_kena_pajak = $perhitunganPph_ini['penghasilan_kena_pajak'];
		   $bg->pph21setahun           = $perhitunganPph_ini['pph21setahun'];
		   $bg->faktor_kali_pph        = $perhitunganPph_ini['faktor_kali_pph'];
		   $bg->menikah                = $menikah;
		   $bg->jumlah_anak            = $jumlah_anak;
		   $bg->save();

		   if ( $bg->pph21 > 0 ) {
			   $jurnals[] = [
				   'jurnalable_id'   => $bg->id,
				   'jurnalable_type' => 'App\BayarGaji',
				   'coa_id'          => $coa_id,
				   'created_at'      => $timestamp,
				   'updated_at'      => $timestamp,
				   'debit'           => 1,
				   'nilai'           => $bg->pph21
			   ];
			   $jurnals[] = [
				   'jurnalable_id'   => $bg->id,
				   'jurnalable_type' => 'App\BayarGaji',
				   'coa_id'          => 200004, // Hutang pph21
				   'debit'           => 0,
				   'nilai'           => $bg->pph21,
				   'created_at'      => $timestamp,
				   'updated_at'      => $timestamp
			   ];
		   }
		   JurnalUmum::insert($jurnals);
		   $pesan = Yoga::suksesFlash('Pembayaran Gaji kepada <strong>' . Staf::find($staf_id)->nama . '</strong> sebesar <strong class="uang">' . ( $gaji_pokok + $bonus ) . '</strong> telah <strong>BERHASIL</strong>' );
		   DB::commit();
		   return redirect('pengeluarans/bayar_gaji_karyawan')
			   ->withPesan($pesan)
			   ->withPrint($bg->id);
	   } catch (\Exception $e) {
		   DB::rollback();
		   throw $e;
	   }
   }
    private function table($checkout){
        $jurnal_umum_id = $checkout->jurnal_umum_id;
		$query = "select jurnalable_type, jurnalable_id from jurnal_umums as ju ";
		$query .= "where coa_id=110000 and ";
		$query .= "debit = 1 and ";
		$query .= "ju.id > {$jurnal_umum_id} ";
		$query .= "group by jurnalable_type, jurnalable_id;";
		$type_and_id = DB::select($query);
        $text = "select ju.debit, jurnalable_type, ju.id, ju.coa_id, co.coa, ju.nilai, ju.jurnalable_id from jurnal_umums as ju join coas as co on co.id = ju.coa_id where ju.id > {$jurnal_umum_id} ";
		if (count($type_and_id)) {
			$text .= "and ( ";
		}
		foreach ($type_and_id as $k => $value) {
			$type = $value->jurnalable_type;
			$type = explode("\\", $type);
			$jurnalable_type = $type[0] . '\\\\\\' . $type[1];

			$text .= "( jurnalable_type = '" . $jurnalable_type. "' and jurnalable_id = '" . $value->jurnalable_id. "' ) ";
			if ($k < count($type_and_id) -1) {
				$text .= "or ";
			}
		}

		if (count($type_and_id)) {
			$text .= ")";
		}

        $rinci = DB::select($text);
		$array=[];
		foreach ($rinci as $key => $r) {
			$sama = false;
			foreach ($array as $k=>$rr) { 
				foreach ($rr as $rrr) {
					if (
						$r->jurnalable_id == $rrr['jurnalable_id'] && 
						$r->jurnalable_type == $rrr['jurnalable_type']
					) {
						$sama = true;
						$count = count($array[$k]);
						$data = [
							'jurnalable_id' => $r->jurnalable_id,
							'jurnalable_type' => $r->jurnalable_type,
							'id' => $r->id,
							'debit' => $r->debit,
							'coa_id' => $r->coa_id,
							'coa' => $r->coa,
							'nilai' => $r->nilai,
						];

						$array[$k][count( $array[$k] )] = $data;
						break;
					}
				}
			}

			if (!$sama) {
				$count = count( $array );
				$data = [
					'jurnalable_id' => $r->jurnalable_id,
					'jurnalable_type' => $r->jurnalable_type,
					'id' => $r->id,
					'debit' => $r->debit,
					'coa_id' => $r->coa_id,
					'coa' => $r->coa,
					'nilai' => $r->nilai,
				];
				$array[$count][] = $data;
			}
		}
		
        $table = [];
		$errors = [];

        foreach ($array as $rc) {
            $valid = false;
            foreach ($rc as $key => $ar) {
				if (  $ar['coa_id'] == 110000 && $ar[ 'debit' ] == 1 ){
                    $valid = true;
				} else if(  $ar['coa_id'] != 110000 && $ar[ 'debit' ] == 1 ){
                    $valid = false;
				}
                if ($valid && $ar['debit']== 0) {
                    $sama = false;
                    foreach ($table as $k=> $tab) {
                        if( $tab['coa_id'] == $ar[ 'coa_id' ]){
                            $table[$k]['nilai'] = $tab['nilai'] + $ar[ 'nilai' ];
                            $table[$k]['jumlah'] = $tab['jumlah'] + 1;
                            $sama = true;
                            $id_sama = false;
                            foreach ($tab['jurnalable_id'] as $jurnl) {
                                if ($jurnl == $ar[ 'jurnalable_id' ]) {
                                    $id_sama = true;
                                }
                            }
                            if (!$id_sama) {
                                $table[$k]['jurnalable_id'][] = $ar[ 'jurnalable_id' ];
                            }
                        }
                    }
                    if (!$sama) {
                        $table[] =[
                            'id' => $ar[ 'id' ],
                            'coa_id' => $ar[ 'coa_id' ],
                            'coa'    => $ar[ 'coa' ],
                            'nilai'  => $ar[ 'nilai' ],
                            'jumlah' => 1,
                            'jurnalable_id' => [
                                 $ar[ 'jurnalable_id' ]
                            ]
                        ]; 
                    }
                } else if ($ar[ 'debit'  ]== 1 && $ar[ 'coa_id'  ]!= 110000 ) {
                        break;
                }
            }
        }
        return $table;
    }
	public function bagiHasilGigi(){
		$bagi_gigi = BagiGigi::latest()->paginate(10);
		return view('pengeluarans.bagi_hasil_gigi', compact('bagi_gigi'));
	}
	
	public function gajiDokterGigi(){
		$gaji_gigis = GajiGigi::latest()->paginate(10);
		return view('pengeluarans.bayar_dokter_gigi', compact('gaji_gigis'));
	}

	public function gajiDokterGigiEdit($id){
		$gaji_gigi = GajiGigi::find($id);
		return view('pengeluarans.bayar_dokter_gigi_edit', compact('gaji_gigi'));
	}

    
	public function gajiDokterGigiBayar(){

		$rules = [
			'staf_id'         => 'required',
			'petugas_id'      => 'required',
			'nilai'           => 'required',
			'bulan'           => 'required',
			'tanggal_dibayar' => 'required'
		];
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
		$bulan =  Yoga::blnPrep( Input::get('bulan') );

		$nilai                 = Yoga::clean( Input::get('nilai') );
		$gaji                  = new GajiGigi;
		$gaji->staf_id         = Input::get('staf_id');
		$gaji->petugas_id      = Input::get('petugas_id');
		$gaji->nilai           = $nilai;
	    $gaji->tanggal_mulai   = $bulan . '-01';
	    $gaji->tanggal_akhir   = date("Y-m-t", strtotime($bulan . '-01'));
		$gaji->tanggal_dibayar = Yoga::datePrep( Input::get('tanggal_dibayar') );
		$gaji->created_at      = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
		$gaji->updated_at      = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
		$confirm               = $gaji->save();

		if ($confirm) {
			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $gaji->id; // id referensi yang baru dibuat
			$jurnal->jurnalable_type = 'App\GajiGigi';
			$jurnal->coa_id          = 610000; // biaya operasional gaji dokter gigi
			$jurnal->debit           = 1;
			$jurnal->nilai           = $nilai;
			$jurnal->created_at = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
			$jurnal->updated_at = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
			$jurnal->save();
			
			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $gaji->id;// id referensi yang baru dibuat
			$jurnal->jurnalable_type = 'App\GajiGigi';
			$jurnal->coa_id          = Input::get('sumber_coa_id'); // Kas di tangan 110004, Kas di kasir 110000, 
			$jurnal->debit           = 0;
			$jurnal->nilai           = $nilai;
			$jurnal->created_at = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
			$jurnal->updated_at = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
			$jurnal->save();

			$pesan = Yoga::suksesFlash('Gaji Dokter Gigi <strong>' . $gaji->staf->nama . '</strong> sebesar <strong>' . Yoga::buatrp( $gaji->nilai ) . '</strong>, Telah berhasil diInput');
		} else {
			$pesan = Yoga::suksesFlash('Gaji Dokter Gagal diInput');
		}
		return redirect('pengeluarans/gaji_dokter_gigi')->withPesan($pesan);
	}

	public function gajiDokterGigiUpdate($id){


		$rules = [
			'staf_id'         => 'required',
			'petugas_id'      => 'required',
			'nilai'           => 'required',
			'bulan'           => 'required',
			'tanggal_dibayar' => 'required'
		];
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
		$bulan =  Yoga::blnPrep( Input::get('bulan') );

		$nilai                 = Yoga::clean( Input::get('nilai') );

		$gaji                  = GajiGigi::find($id);
		$gaji->staf_id         = Input::get('staf_id');
		$gaji->petugas_id      = Input::get('petugas_id');
		$gaji->nilai           = $nilai;
	    $gaji->tanggal_mulai   = $bulan . '-01';
	    $gaji->tanggal_akhir   = date("Y-m-t", strtotime($bulan . '-01'));
		$gaji->tanggal_dibayar = Yoga::datePrep( Input::get('tanggal_dibayar') );
		$gaji->created_at      = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
		$gaji->updated_at      = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
		$confirm               = $gaji->save();

		JurnalUmum::where('jurnalable_type', 'App\GajiGigi')
				->where('jurnalable_id', $id)
				->delete();

		if ($confirm) {
			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $gaji->id; // id referensi yang baru dibuat
			$jurnal->jurnalable_type = 'App\GajiGigi';
			$jurnal->coa_id          = 610000; // biaya operasional gaji dokter gigi
			$jurnal->debit           = 1;
			$jurnal->nilai           = $nilai;
			$jurnal->created_at = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
			$jurnal->updated_at = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
			$jurnal->save();
			
			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $gaji->id;// id referensi yang baru dibuat
			$jurnal->jurnalable_type = 'App\GajiGigi';
			$jurnal->coa_id          = Input::get('sumber_coa_id'); // Kas di tangan 110004, Kas di kasir 110000, 
			$jurnal->debit           = 0;
			$jurnal->nilai           = $nilai;
			$jurnal->created_at = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
			$jurnal->updated_at = date("Y-m-t 23:59:59", strtotime($bulan . '-01'));
			$jurnal->save();

			$pesan = Yoga::suksesFlash('Gaji Dokter Gigi <strong>' . $gaji->staf->nama . '</strong> sebesar <strong>' . Yoga::buatrp( $gaji->nilai ) . '</strong>, Telah berhasil diInput');
		} else {
			$pesan = Yoga::suksesFlash('Gaji Dokter Gagal diInput');
		}
		return redirect('pengeluarans/gaji_dokter_gigi')->withPesan($pesan);
	}

	public function peralatans(){
		$peralatans = $this->queryPeralatan('App\\\BelanjaPeralatan');
		$bahan_bangunans = $this->queryPeralatan('App\\\BahanBangunan');
		$data = compact( 'peralatans', 'bahan_bangunans');
		return view('pengeluarans.peralatans', $data);
	}
	
	public function belanjaPeralatan(){
		$masa_pakai = Yoga::masaPakai();
		return view('pengeluarans.belanja_peralatan', compact('masa_pakai'));
	}

	public function belanjaPeralatanBayar(){

		$rules = [
			'sumber_uang'       => 'required',
			'supplier_id'       => 'required',
			'nomor_faktur'      => 'required',
			'tanggal_pembelian' => 'required',
			'staf_id'           => 'required',
			'temp'              => 'required'
		];
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
		

		$supplier_id       = Input::get('supplier_id');
		$tanggal_pembelian = Input::get('tanggal_pembelian');
		$nomor_faktur      = Input::get('nomor_faktur');
		$staf_id           = Input::get('staf_id');
		$temp              = Input::get('temp');
		$temp              = json_decode($temp, true);

		$data = [];
		$total_nilai = 0;

		$fb                 = new FakturBelanja;
		$fb->tanggal        = Yoga::datePrep(Input::get('tanggal_pembelian'));
		$fb->nomor_faktur   = Input::get('nomor_faktur');
		$fb->belanja_id     = 4;
		$fb->supplier_id    = Input::get('supplier_id');
		$fb->sumber_uang_id = Input::get('sumber_uang');
		$fb->petugas_id     = $staf_id;
		$fb->save();
		$fb->faktur_image   = $this->imageUploadAlat('faktur', 'faktur_image', $fb->id);
		$confirm = $fb->save();

		$timestamp                    = date('Y-m-d H:i:s');
		$acs                          = [];
		foreach ($temp as $t) {
			$data[]                   = [
				 'faktur_belanja_id' => $fb->id,
				 'staf_id'           => $staf_id,
				 'peralatan'         => $t['peralatan'],
				 'harga_satuan'      => $t['nilai'],
				 'jumlah'            => $t['jumlah'],
				 'masa_pakai'        => $t['masa_pakai'],
				 'created_at'        => $timestamp,
				 'updated_at'        => $timestamp
			];

			foreach ($t['ac'] as $ac) {
				$acs[]             = [

					'merek'             => $ac['merek'],
					'keterangan'        => $ac['keterangan_lokasi'],
					'faktur_belanja_id' => $fb->id,
					'created_at'        => $timestamp,
					'updated_at'        => $timestamp

				];
			}
			$total_nilai += $t['nilai'] * $t['jumlah'];
		}

		$confirm = BelanjaPeralatan::insert($data);
		Ac::insert($acs);

		if ($confirm) {

			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $fb->id; // id referensi yang baru dibuat
			$jurnal->jurnalable_type = 'App\FakturBelanja';
			$jurnal->coa_id          = 120001; // Peralatan
			$jurnal->debit           = 1;
			$jurnal->nilai           = $total_nilai;
			$jurnal->save();
			
			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $fb->id;// id referensi yang baru dibuat
			$jurnal->jurnalable_type = 'App\FakturBelanja';
			$jurnal->coa_id          = Input::get('sumber_uang'); // Kas di tangan 110004, Kas di kasir 110000, 
			$jurnal->debit           = 0;
			$jurnal->nilai           = $total_nilai;
			$jurnal->save();

			$pesan = Yoga::suksesFlash('Input Peralatan telah berhasil');
		} else {
			$pesan = Yoga::suksesFlash('Input Peralatan telah gagal');
		}
		return redirect('pengeluarans/peralatans')
			->withPrint($fb->id)
			->withPesan($pesan);
	}
	public function data(){
		return view('pengeluarans.bukan_obat'
	}

	public function dataAjax(){
		$keterangan     = Input::get('keterangan');
		$tanggal        = Input::get('tanggal');
		$petugas        = Input::get('supplier');
		$displayed_rows = Input::get('displayed_rows');
		$key            = Input::get('key');

		$pecah      = new PasiensAjaxController;
		$keterangan = $pecah->pecah($keterangan);
		$petugas    = $pecah->pecah($petugas);

		$pass = $key * $displayed_rows;

		$datas = $this->queryData($keterangan, $tanggal, $petugas, $pass, $displayed_rows);
		$count = $this->queryData($keterangan, $tanggal, $petugas, $pass, $displayed_rows, true);

		$count = $count[0]->jumlah;

		$pages = ceil( $count / $displayed_rows );

		return [
			'rows' => $count,
			'data' => $datas,
			'pages' => $pages,
			'key' => $key
		];




	}
	
	private function imageUpload($pre, $fieldName, $id){
		if(Input::hasFile($fieldName)) {

			$upload_cover = Input::file($fieldName);
			//mengambil extension
			$extension = $upload_cover->getClientOriginalExtension();

			$upload_cover = Image::make($upload_cover);
			$upload_cover->resize(1000, null, function ($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			});


			//membuat nama file random + extension
			$filename =	 $pre . $id . '.' . $extension;

			//menyimpan bpjs_image ke folder public/img
			$destination_path = public_path() . DIRECTORY_SEPARATOR . 'img/belanja/lain';

			// Mengambil file yang di upload
			$upload_cover->save($destination_path . '/' . $filename);
			
			//mengisi field bpjs_image di book dengan filename yang baru dibuat
			return $filename;
			
		} else {
			return null;
		}
	}
	

	private function imageUploadAlat($pre, $fieldName, $id){
		if(Input::hasFile($fieldName)) {

			$upload_cover = Input::file($fieldName);
			//mengambil extension
			$extension    = $upload_cover->getClientOriginalExtension();

			$upload_cover = Image::make($upload_cover);
			$upload_cover->resize(1000, null, function ($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			});
			//membuat nama file random + extension
			$filename =	 $pre . $id . '.' . $extension;

			//menyimpan bpjs_image ke folder public/img
			$destination_path = public_path() . DIRECTORY_SEPARATOR . 'img/belanja/alat';

			// Mengambil file yang di upload
			$upload_cover->save($destination_path . '/' . $filename);
			
			//mengisi field bpjs_image di book dengan filename yang baru dibuat
			return $filename;
			
		} else {
			return null;
		}
	}
	public function peralatan_detail($id){
		$fakturbelanja = FakturBelanja::find($id);
		return view('pengeluarans.peralatan_detail', compact(
			 'fakturbelanja'
		));
	}
	
	public function belanjaBukanObatDetail($id){
		$peng = Pengeluaran::find($id);
		return view('pengeluarans.bukan_obat_detail', compact(
			 'peng'
		));
	}
	public function bagiHasilGigiPost(){

		$rules               = [
		  "sumber_coa_id"   => "required",
		  "nilai"           => "required",
		  "bulan"           => "required",
		  "petugas_id"      => "required",
		  "tanggal_dibayar" => "required"
		];
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}

       $bulan = Yoga::blnPrep( Input::get('bulan') );


	   if ((int)strtotime( date('Y-m-d H:i:s') ) > (int)strtotime( date($bulan . '-t 23:59:59') ) ) {
			$timestamp = date($bulan . '-t 23:59:59');
	   } else {
			$timestamp = date('Y-m-d H:i:s');
	   }
	   $nilai = Yoga::clean( Input::get('nilai') );

		$gaji                  = new BagiGigi;
		$gaji->petugas_id      = Input::get('petugas_id');
		$gaji->nilai           = $nilai;
	    $gaji->tanggal_mulai   = $bulan . '-01';
	    $gaji->tanggal_akhir   = date("Y-m-t", strtotime($bulan . '-01'));
		$gaji->tanggal_dibayar = Yoga::datePrep( Input::get('tanggal_dibayar') );
		$confirm               = $gaji->save();

		$pph21 = 0;
		if ($confirm) {
			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $gaji->id; // id referensi yang baru dibuat
			$jurnal->jurnalable_type = 'App\BagiGigi';
			$jurnal->coa_id          = 610001; // biaya operasional bagi hasil pelayanan dokter gigi
			$jurnal->debit           = 1;
			$jurnal->nilai           = $nilai;
			$jurnal->created_at = $timestamp;
			$jurnal->updated_at = $timestamp;
			$jurnal->save();
			
			$jurnal                  = new JurnalUmum;
			$jurnal->jurnalable_id   = $gaji->id;// id referensi yang baru dibuat
			$jurnal->jurnalable_type = 'App\BagiGigi';
			$jurnal->coa_id          = Input::get('sumber_coa_id'); // Kas di tangan 110004, Kas di kasir 110000, 
			$jurnal->debit           = 0;
			$jurnal->nilai           = $nilai;
			$jurnal->created_at = $timestamp;
			$jurnal->updated_at = $timestamp;
			$jurnal->save();


			//
			//pph21 provider dokter gigi
			//

			$periode = $gaji->tanggal_mulai->format('Y-m');
			$bagis = BagiGigi::where('tanggal_mulai', 'like', $periode . '%')->get();
			$total_gaji_bulan_ini = 0;
			$total_pph_bulan_ini = 0;
			foreach ($bagis as $bagi) {
				$total_gaji_bulan_ini += $bagi->nilai;
				$total_pph_bulan_ini  += $bagi->pph21;
			}

			$staf_ini                     = Staf::find('D32'); // staf= Sukma Wahyu Wijayanti

			$tahun_pembayaran = date("Y", strtotime($bulan . '-01'));

			//staf_ini, tahun_pembayaran, model
			$parameterPtkp = $this->parameterPtkp($staf_ini, $tahun_pembayaran, 'App\BagiGigi', 'tanggal_mulai');
			$menikah       = $parameterPtkp['menikah'];
			$jumlah_anak   = $parameterPtkp['jumlah_anak'];


			$ptkp                         = Config::where('config_variable', 'Penghasilan Tidak Kena Pajak')->first()->value; // penghasilann tidak kena pajak
			$ptkp_ini                     = $this->perhitunganPtkp($ptkp, $staf_ini->jumlah_anak, $staf_ini->menikah, $staf_ini->jenis_kelamin);
			$perhitunganPph_ini           = $this->pph21($total_gaji_bulan_ini, $total_pph_bulan_ini, $staf_ini, $ptkp);

			$gaji->pph21                  = $perhitunganPph_ini['pph21'];
			$gaji->biaya_jabatan          = $perhitunganPph_ini['biaya_jabatan'];
			$gaji->ptkp                   = $perhitunganPph_ini['ptkp'];
			$gaji->potongan5persen        = $perhitunganPph_ini['potongan5persen'];
			$gaji->potongan15persen       = $perhitunganPph_ini['potongan15persen'];
			$gaji->potongan25persen       = $perhitunganPph_ini['potongan25persen'];
			$gaji->potongan30persen       = $perhitunganPph_ini['potongan30persen'];
			$gaji->npwp                   = $perhitunganPph_ini['npwp'];
			$gaji->gaji_netto             = $perhitunganPph_ini['gaji_netto'];
			$gaji->penghasilan_kena_pajak = $perhitunganPph_ini['penghasilan_kena_pajak'];
			$gaji->pph21setahun           = $perhitunganPph_ini['pph21setahun'];
			$gaji->faktor_kali_pph        = $perhitunganPph_ini['faktor_kali_pph'];
			$gaji->menikah                = $menikah;
			$gaji->jumlah_anak            = $jumlah_anak;
			$gaji->save();


			$pesan = Yoga::suksesFlash('Bagi Hasil Pelayanan Dokter Gigi sebesar <strong>' . Yoga::buatrp( $gaji->nilai ) . '</strong>, Telah berhasil diInput');
		} else {
			$pesan = Yoga::suksesFlash('Bagi Hasil Pelayanan Dokter Gagal diInput');
		}
		return redirect('pengeluarans/bagi_hasil_gigi')->withPesan($pesan);
	}
		
	private function convertArray($collection){
		$array = [];
		 foreach ($collection as $value) {
		 	$array[] = $value;
		 }
		return $array;
	}
	public function product(){
		$param = Input::get('q');
		
		$param = trim($param);


		$data = '%';
		$arr = str_split($param, 1);

		foreach ($arr as $value) {
			$data .= $value . '%';
		}

		$pasiens = Pasien::with('asuransi')->where('nama', 'like', $data)->take(10)->get();

		$data = [];

		foreach ($pasiens as $ps) {
			$data['items'][] = [
				 'id' => $ps->id,
				 'text' => $ps->nama,
				 'asuransi' => $ps->asuransi->nama,
				 'tanggal_lahir' => Yoga::updateDatePrep( $ps->tanggal_lahir ),
				 'bpjs' => $ps->bpjs_image
			];
		}

		return json_encode($data);


	}
	public function getBelanjaPeralatanObject(){
		$jurnal_umum_id = Input::get('jurnal_umum_id');
		$JurnalUmum = JurnalUmum::find($jurnal_umum_id);
		
		$jurnalable_type = explode('\\', $JurnalUmum->jurnalable_type)[1];
		$jurnalable_id = $JurnalUmum->jurnalable_id;

		if ( $jurnalable_type == 'Pengeluaran' ) {
			$object = Pengeluaran::find( $jurnalable_id );
			return dd( $object );

			//$fb = new FakturBelanja;
			//$fb->tanggal = $object->tanggal;
			//$fb->belanja_id = 4;
			//$fb->supplier_id = $object->supplier_id;
			//$fb->sumber_uang_id = $object->supplier_id;
			//$fb->petugas_id = $object->staf_id;
			//$fb->save();

			//BelanjaPeralatan::create([
				 //'faktur_belanja_id' => $fb->id,
				 //'staf_id' => $staf_id,
				 //'peralatan' => $t['peralatan'],
				 //'harga_satuan' => $t['nilai'],
				 //'jumlah' => $t['jumlah'],
				 //'masa_pakai' => $t['masa_pakai']
			//]);


			//$path_before = public_path() . DIRECTORY_SEPARATOR . 'img/lain/' . $object->faktur_image;
			//$ext = pathinfo($path_before, PATHINFO_EXTENSION);
			//$filename = 'faktur' . $fb->id . '.' . $ext;
			//$path_after = public_path() . DIRECTORY_SEPARATOR . 'img/alat/' . $filename;

			//$confirm = rename( 
				//$path_before,
				//$path_after
			//);

			//if ($confirm) {
				//$fb->faktur_image = $filename;
			//}

			//$fb->save();
		}
		return false;
	}
	public function parameterKasir(){
		
        $last_chekcout                = CheckoutKasir::orderBy('id', 'desc')->first();
        $uang_di_tangan               = $last_chekcout->uang_di_tangan;
        $jurnal_umum_id_last_cehckout = $last_chekcout->jurnal_umum_id;
        $tanggal                      = $last_chekcout->created_at;
        $jurnal_umums = JurnalUmum::whereRaw("id > $jurnal_umum_id_last_cehckout and jurnalable_type = 'App\\\Modal' and debit = 0 and (coa_id = 301000 or coa_id=110004) ")
									->get();
        $modal_awal = 0;
		$modal_ids = [];
        foreach ($jurnal_umums as $ju) {
		   $modal_awal +=  $ju->nilai;
		   $modal_ids[] = $ju->id;
        }
        $total_uang_masuk = JurnalUmum::where('id', '>', $jurnal_umum_id_last_cehckout)
                                ->where('coa_id', 110000) 
                                ->where('jurnalable_type', '!=', 'App\Modal')
								->where('created_at', '>=', $last_chekcout->created_at)
                                ->where('debit', '1')
                                ->sum('nilai');

        $total_uang_keluar = JurnalUmum::where('id', '>', $jurnal_umum_id_last_cehckout)
                                ->where('coa_id', 110000) 
								->where('created_at', '>=', $last_chekcout->created_at)
                                ->where('jurnalable_type', '!=', 'App\Modal')
                                ->where('jurnalable_type', '!=', 'App\CheckoutKasir')
                                ->where('debit', '0')
                                ->sum('nilai');

        $uang_di_kasir = $modal_awal + $total_uang_masuk - $total_uang_keluar;

		return [
			'uang_di_kasir' => $uang_di_kasir,
			'total_uang_keluar' => $total_uang_keluar,
			'total_uang_masuk' => $total_uang_masuk,
			'modal_awal' => $modal_awal,
			'uang_di_tangan' => $uang_di_tangan,
			'tanggal' => $tanggal,
			'modal_ids' => $modal_ids
		];
	}
	
	public function GolonganPeralatanCreate(){
		$gols = GolonganPeralatan::all();
		return view('pengeluarans.golsPeralatansCreate', compact('gols'));
	}
	public function GolonganPeralatanPost(){
		$gol = new GolonganPeralatan;
		$gol->golongan_peralatan	= Input::get('golongan_peralatan');
		$gol->masa_pakai	= Input::get('masa_pakai');
		$confirm = $gol->save();
		if ($confirm) {
			$pesan = Yoga::suksesFlash('golongan peralatan baru berhasil dibuat');
		} else {
			$pesan = Yoga::gagalFlash('golongan peralatan baru gagal dibuat');
		}
		return redirect('pengeluarans/peralatans')->withPesan($pesan);
	}
	public function inputHarta(){
		$hartas = InputHarta::latest()->get();
		return view('pengeluarans.inputHarta', compact('hartas'));
	}
	public function postInputHarta(){

		$jurnals            = [];
		$hargaJualClean     = Yoga::clean( Input::get('harga_jual') );
		$hargaClean         = Yoga::clean( Input::get('harga') );
		$tax_amnesty        = Input::get('tax_amnesty');
		$uangMukaClean      = Yoga::clean( Input::get('uang_muka') );
		$timestamp_that_day = Yoga::bulanTahun( Input::get('bulan_tahun_beli') ) . '-01';

		// harta bertambah di debet, hutang bertambah di kredit, bila ada uang muka, kas berkurang di kredit
		// 1. Insert coa_id, coa_penyusutan_id, coa_hutang_id
		// 2. Insert harta di dalam tabel Input_hartas
		// 3. kumpulkan array jurnal_umum  saat transaksi
		// 4. kumpulkan array pembayaran hutang hingga sekarang saat ini
		// 5. kumpulkan array penyusutan asset hingga sekarang saat ini
		// 6. insert jurnal_umums

		// 
		// 1. Insert coa_id, coa_penyusutan_id, coa_hutang_id
		//
	
		$existing_id = (int)Coa::where('id', 'like', '12%')->orderBy('id', 'desc')->first()->id;
		$coa_id = $existing_id + 1;
		$coa_penyusutan_id =  $existing_id + 2;

		$coas[]                = [
			'id'              => $coa_id,
			'coa'             => Input::get('harta'),
			'kelompok_coa_id' => 12,
			'saldo_awal'      => 0,
			'created_at'      => date('Y-m-d H:i:s'),
			'updated_at'      => date('Y-m-d H:i:s')
		];
		$coas[]                = [
			'id'              => $coa_penyusutan_id,
			'coa'             => 'Akumulasi Penyusutan ' .Input::get('harta'),
			'kelompok_coa_id' => 12,
			'saldo_awal'      => 0,
			'created_at'      => date('Y-m-d H:i:s'),
			'updated_at'      => date('Y-m-d H:i:s')
		];

		$existing_id = (int)Coa::where('id', 'like', '2%')->orderBy('id', 'desc')->first()->id;

		$coa_hutang_id = (int)$existing_id + 1;

		$coas[]                = [
			'id'              => $coa_hutang_id,
			'coa'             => 'Hutang ' .Input::get('harta'),
			'kelompok_coa_id' => 2,
			'saldo_awal'      => 0,
			'created_at'      => date('Y-m-d H:i:s'),
			'updated_at'      => date('Y-m-d H:i:s')
		];

		Coa::insert($coas);

		// 
		// 2. Insert harta di dalam tabel Input_hartas
		//
		$ih                    = new InputHarta;
		$ih->harta             = Input::get('harta');
		$ih->tanggal_beli      = $timestamp_that_day;
		$ih->coa_id            = $coa_id;
		$ih->coa_penyusutan_id = $coa_penyusutan_id;
		$ih->tax_amnesty       = $tax_amnesty;
		$ih->coa_hutang_id     = $coa_hutang_id;
		$ih->harga             = $hargaClean;
		$ih->metode_bayar_id   = Input::get('metode_bayar_id');
		$ih->uang_muka         = $uangMukaClean;
		$ih->lama_cicilan      = Input::get('lama_cicilan');
		$ih->masa_pakai        = Input::get('masa_pakai');
		$ih->status_harta_id   = Input::get('status_harta_id');
		$ih->penyusutan        = 0;
		$ih->staf_id           = Input::get('staf_id');
		$ih->save();

		// 
		// 3. kumpulkan array jurnal_umum  saat transaksi
		//
		//
		// harta bertambah di debet, hutang bertambah di kredit, bila ada uang muka, kas berkurang di kredit
		//


		// harta bertambah di debet
		$jurnals[]             = [
			'jurnalable_id'   => $ih->id,
			'jurnalable_type' => 'App\InputHarta',
			'debit'           => 1,
			'nilai'           => $hargaClean,
			'coa_id'          => $coa_id,
			'created_at'      => $timestamp_that_day,
			'updated_at'      => $timestamp_that_day
		];

		// hutang bertambah di kredit
		if ( Input::get('metode_bayar_id') == '2' ) {
			$hutangHarta                    = $hargaClean - $uangMukaClean;
			$jurnals[]                      = [
				'jurnalable_id'            => $ih->id,
				'jurnalable_type'          => 'App\InputHarta',
				'debit'                    => 0,
				'nilai'                    => $hutangHarta,
				'coa_id'                   => $coa_hutang_id,
				'created_at'               => $timestamp_that_day,
				'updated_at'               => $timestamp_that_day
			];
			// kas berkurang di kredit untuk pembayaran uang muka
			// jika ada uang muka
			if ($uangMukaClean) {
				$jurnals[]                  = [
					'jurnalable_id'        => $ih->id,
					'jurnalable_type'      => 'App\InputHarta',
					'debit'                => 0,
					'nilai'                => $uangMukaClean,
					'coa_id'               => 110004,
					'created_at'           => $timestamp_that_day,
					'updated_at'           => $timestamp_that_day
				];
				$jurnals                    = $this->tambahModal($timestamp_that_day, $jurnals);
			}
		} else { // Jika pembayaran tunai

			$jurnals[] = [
				'jurnalable_id' => $ih->id,
				'jurnalable_type' => 'App\InputHarta',
				'debit' => 0,
				'nilai' => $hargaClean,
				'coa_id' => 110004,
				'created_at' => $timestamp_that_day,
				'updated_at' => $timestamp_that_day
			];
			$jurnals = $this->tambahModal($timestamp_that_day, $jurnals);
		}


		$angsuranPerBulan             =0;
		if ( Input::get('metode_bayar_id') == 2 ) {
			$angsuranPerBulan             = ( $hargaClean - $uangMukaClean ) / ( Input::get('lama_cicilan') * 12 );
		}
		$jumlahBulan                  = Yoga::monthInterval($timestamp_that_day, date('Y-m-d H:i:s'));
		$hutangTerbayar               = 0;
		$penyusutanTerbayar           = 0;
		$berapaKalihutangTerbayar     = 0;
		$berapaKalipenyusutanTerbayar = 0;
		$jualSudahMasukJurnal         = false;


			//
			// 4. kumpulkan array pembayaran hutang hingga sekarang saat ini
			//
			for ($i = 0; $i < $jumlahBulan - 1; $i++) {
				$n = (int)$i + 1;	
				$timestamp = Yoga::monthLater($timestamp_that_day, $n);
				if ( Input::get('metode_bayar_id') == 2 ) {
					if ( $hutangHarta - $hutangTerbayar > 0) {
						$bayarBulanIni = (int)min( [ $angsuranPerBulan, ($hutangHarta - $hutangTerbayar ) ] );


						$bayar = new BayarHutangHarta;
						$bayar->pembayaran = $bayarBulanIni;
						$bayar->coa_hutang_id = $coa_hutang_id;
						$bayar->save();

						$jurnals[] = [
							'jurnalable_id'   => $bayar->id,
							'jurnalable_type' => 'App\BayarHutangHarta',
							'debit'           => 1,
							'nilai'           => $bayarBulanIni,
							'coa_id'          => $coa_hutang_id,
							'created_at'      => $timestamp,
							'updated_at'      => $timestamp
						];
						$jurnals[] = [
							'jurnalable_id'   => $bayar->id,
							'jurnalable_type' => 'App\BayarHutangHarta',
							'debit'           => 0,
							'nilai'           => $bayarBulanIni,
							'coa_id'          => 110004,
							'created_at'      => $timestamp,
							'updated_at'      => $timestamp
						];
						$jurnals         = $this->tambahModal($timestamp, $jurnals);
						$hutangTerbayar += $bayarBulanIni;
						$berapaKalihutangTerbayar++;
					}
				}

				//
				// 5. kumpulkan array penyusutan asset hingga sekarang saat ini
				//

				$penyusutanPerBulan = $hargaClean / ( Input::get('masa_pakai') * 12 );

				// variabel ini untuk menentukan bahwa barang tersebut sudah dijual, dengan status_harta dijual && tanggal penjualan sudah lewat
				$barangSudahDijual = (Input::get('status_harta_id') == 2 && ( Yoga::bulanTahun( Input::get('bulan_tahun_jual') ) . '-01 00:00:00') <= $timestamp );

				if ( 
					$hargaClean - $penyusutanTerbayar > 0 && // jika harga barang dikurangi total jumlah seluruh penyusutan item ini masih lebih dari 0 
					( Input::get('status_harta_id') == 1 || !$barangSudahDijual) && // status barang sudah dijual tapi belum lewat waktu dijual 
					Input::get('tax_amnesty') == "0"
				) { 
					
					
					$bayarPenyusutan      = (int)min( [ $penyusutanPerBulan, ( $hargaClean - $penyusutanTerbayar ) ] );
					$susut                = new Penyusutan;
					$susut->penyusutan    = $bayarPenyusutan;
					$susut->keterangan    = 'Penyusutan ' . Input::get('harta');
					$susut->tanggal_mulai = $timestamp;
					$susut->tanggal_akhir = date("Y-m-t", strtotime($timestamp));
					$confirm = $susut->save();


					$jurnals[] = [

						'jurnalable_id'   => $susut->id,
						'jurnalable_type' => 'App\Penyusutan',
						'debit'           => 1,
						'nilai'           => $bayarPenyusutan,
						'coa_id'          => 612312,
						'created_at'      => $timestamp,
						'updated_at'      => $timestamp

					];
					$jurnals[] = [

						'jurnalable_id'   => $susut->id,
						'jurnalable_type' => 'App\Penyusutan',
						'debit'           => 0,
						'nilai'           => $bayarPenyusutan,
						'coa_id'          => $coa_penyusutan_id,
						'created_at'      => $timestamp,
						'updated_at'      => $timestamp

					];
					$penyusutanTerbayar += $bayarPenyusutan;
					$berapaKalipenyusutanTerbayar++;

				}
				if ($barangSudahDijual && !$jualSudahMasukJurnal) {
					$pj             = new PenjualanAset;
					$pj->harta_id   = $ih->id;
					$pj->harga_beli = $hargaClean;
					$pj->harga_jual = $hargaJualClean;
					$pj->penyusutan = $penyusutanTerbayar;
					$pj->staf_id    = Input::get('staf_id');
					$confirm        = $pj->save();
					if ($confirm) {
						$jurnals[] = [
							'jurnalable_id'   => $pj->id,
							'jurnalable_type' => 'App\PenjualanAset',
							'debit'           => 1,
							'nilai'           => $hargaJualClean,
							'coa_id'          => 110004,
							'created_at'      => $timestamp,
							'updated_at'      => $timestamp
						];
						$hargaWajar = $hargaClean - $penyusutanTerbayar; // harga perolehan dikurangi pernyusutan
						if ($hargaWajar>0) {
							$jurnals[] = [
								'jurnalable_id'   => $pj->id,
								'jurnalable_type' => 'App\PenjualanAset',
								'debit'           => 0,
								'nilai'           => $hargaClean-$penyusutanTerbayar,
								'coa_id'          => $coa_id,
								'created_at'      => $timestamp,
								'updated_at'      => $timestamp
							];
						}
						$keuntungan = $hargaJualClean-$hargaWajar;
						if ( $keuntungan > 0 ) {
							$jurnals[] = [
								'jurnalable_id'   => $pj->id,
								'jurnalable_type' => 'App\PenjualanAset',
								'debit'           => 0,
								'nilai'           => $keuntungan,
								'coa_id'          => 70100,
								'created_at'      => $timestamp,
								'updated_at'      => $timestamp
							];
						} else {
							$jurnals[] = [
								'jurnalable_id'   => $pj->id,
								'jurnalable_type' => 'App\PenjualanAset',
								'debit'           => 1,
								'nilai'           => abs( $keuntungan ),
								'coa_id'          => 70100,
								'created_at'      => $timestamp,
								'updated_at'      => $timestamp
							];
						}
					}
					$jualSudahMasukJurnal = true;
				}
			}

		$confirm             = JurnalUmum::insert($jurnals);
		$ih->penyusutan      = $penyusutanTerbayar;
		$ih->hutang_terbayar = $hutangTerbayar;
		$ih->save();

		if ($confirm) {
			$text = '<ul>';
			$text .= '<li>Harta Berhasil Ditambahkan berupa <strong>' . Input::get('harta'). '</strong></li>';
			if ( Input::get('metode_bayar_id') == 2 ) {
				$text .= '<li><strong>' . $berapaKalihutangTerbayar . '</strong> kali hutang terbayar total <strong>' . Yoga::buatrp( $hutangTerbayar ). '</strong></li>';
			}
			if ( $barangSudahDijual ) {
				$text .= '<li>berhasil dijual</li>';
				$text .= '<li>seharga <strong>' . Input::get('harga_jual'). '</strong></li>';
				$text .= '<li>harga beli<strong> ' . Yoga::buatrp( $pj->harga_beli ). '</strong></li>';
				$text .= '<li>harga jual<strong> ' . Yoga::buatrp( $pj->harga_jual ). '</strong></li>';
				$text .= '<li>penyusutan<strong> ' . Yoga::buatrp( $pj->penyusutan ). '</strong></li>';
				$text .= '<li>harga wajar<strong> ' . Yoga::buatrp( $hargaWajar ). '</strong></li>';
				$text .= '<li>keuntungan<strong>  ' . Yoga::buatrp( $keuntungan ). '</strong></li>';
			}
			$text .= '<li><strong>' . $berapaKalipenyusutanTerbayar . '</strong> kali penyusutan terbayar total <strong>' . Yoga::buatrp( $penyusutanTerbayar ). '</strong></li>';
			$text .= '<li><strong>'.count( $jurnals ).'</strong> kali jurnal umum tersubmit</li>';
			$text .= '</ul>';
			$pesan = Yoga::suksesFlash($text);
		} else {
			$pesan = Yoga::gagalFlash('Harta Gagal Ditambahkan');
		}
		return redirect()->back()->withPesan($pesan);
	}
	public function selsihUang($timestamp, $jurnals){

		$kasJurnal = 0;
		foreach ($jurnals as $ju) {
			if ($ju['coa_id'] == 110004 && $ju['debit'] == 1) {
				$kasJurnal += $ju['nilai'];
			}
			if ($ju['coa_id'] == 110004 && $ju['debit'] == 0) {
				$kasJurnal -= $ju['nilai'];
			}
		}

		$query  = "SELECT sum(CASE WHEN debit = 1 THEN nilai ELSE 0 END) as debit, ";
		$query .= "sum(CASE WHEN debit = 0 THEN nilai ELSE 0 END) as kredit ";
		$query .= "FROM jurnal_umums ";
		$query .= "WHERE created_at <= '{$timestamp}' ";
		$query .= "AND coa_id like '110004' ";

		$ju = DB::select($query)[0];

		if ( count( $ju ) > 0 ) {
			$jummlahKas = $ju->debit - $ju->kredit;
		} else {
			$jummlahKas =0;
		}

		$result = $kasJurnal + $jummlahKas;

		Log::info('===============================================================');
		Log::info('result = ' . $result);
		Log::info('jummlahKas = ' . $jummlahKas);
		Log::info('kasJurnal = ' . $kasJurnal);
		Log::info('===============================================================');
		return $result;
	}
	public function tambahModal($time, $jurnals){
		$selisih = $this->selsihUang($time, $jurnals);
		if ($selisih <= 0) {
			Log::info('Dibuat Modal');
			$tambahanModal = (int)abs($selisih);
			$modal = new Modal;
			$modal->coa_kas_id = 301000;
			$modal->modal = $tambahanModal;
			$modal->staf_id = Input::get('staf_id');
			$modal->keterangan = 'Modal untuk pembelian harta ' . Input::get('harta');
			$modal->created_at = $time;
			$modal->updated_at = $time;
			$modal->save();

			$jurnals[] = [
				'jurnalable_id' => $modal->id,
				'jurnalable_type' => 'App\Modal',
				'debit' => 1,
				'nilai' => $tambahanModal,
				'coa_id' => 110004,
				'created_at' => $time,
				'updated_at' => $time
			];

			$jurnals[] = [
				'jurnalable_id' => $modal->id,
				'jurnalable_type' => 'App\Modal',
				'debit' => 0,
				'nilai' => $tambahanModal,
				'coa_id' => 301000,
				'created_at' => $time,
				'updated_at' => $time
			];
			Log::info('===============================================================');
			Log::info('seleisih = ' . $selisih);
			Log::info('tambahanModal = ' . $tambahanModal);
			Log::info('===============================================================');
		} else {
			Log::info('TIDAK Dibuat Modal');
		}
		return $jurnals;
	}
	public function pph21($total_gaji_bulan_ini, $total_pph_bulan_ini, $staf_ini, $ptkp){

		 $pph21                  = 0;
		 $total_ptkp             = 0;
		 $biaya_jabatan          = 0;
		 $potongan5persen        = 0;
		 $potongan15persen       = 0;
		 $gaji_netto             = 0;
		 $gaji_netto_setahun     = 0;
		 $potongan25persen       = 0;
		 $potongan30persen       = 0;
		 $penghasilan_kena_pajak = 0;
		 $faktor_kali_pph        = 0;
		 $pph21setahun           = 0;

		   //jika total gaji bulan ini melebihi ptkp
		   if ( $total_gaji_bulan_ini > $ptkp ) {
			   // biaya jabatan = 5% x gaji bulan ini;
				$biaya_jabatan      = (int)$total_gaji_bulan_ini * 0.05;
			   // biaya jabatan maksimal yang diperkenankan adalah 500rb;
				if ($biaya_jabatan > 500000) {
					$biaya_jabatan = 500000;
				}
			   // gaji netto adalah gaji yang diterima bulan ini dikurangi biaya jabatan
				$gaji_netto         = $total_gaji_bulan_ini - $biaya_jabatan;
				$gaji_netto_setahun = $gaji_netto * 12;
				// total ptkp per tahun adalah ptkp ( 12 (bulan) + status pernikahan ( 1 bila menikah, 0 bila tidak menikah ) + jumlah anak );
				$total_ptkp         = 12 * $this->perhitunganPtkp($ptkp, $staf_ini->jumlah_anak, $staf_ini->menikah, $staf_ini->jenis_kelamin);
				//penghasilan kena pajak adalah gaji netto setahun dikurangi ptkp, ini yang akan diberlakukan tarif pph21
				$penghasilan_kena_pajak = $gaji_netto_setahun - $total_ptkp;

				// pengenaan tarif pph
				// 
				//WP dengan penghasilan tahunan sampai dengan Rp 50 juta adalah 5%
				//WP dengan penghasilan tahunan di atas Rp 50 juta - Rp 250 juta adalah 15%
				//WP dengan penghasilan tahunan di atas Rp 250 juta - Rp 500 juta adalah 25%
				//WP dengan penghasilan tahunan di atas Rp 500 juta adalah 30%
				$pph21_setahun_ini            = $this->pph21setahun( $penghasilan_kena_pajak );
				$pph21setahun                 = $pph21_setahun_ini['pph21setahun'];
				$potongan5persen              = $pph21_setahun_ini['potongan5persen'];
				$potongan15persen1            = $pph21_setahun_ini['potongan15persen1'];
				$potongan15persen2            = $pph21_setahun_ini['potongan15persen2'];
				$potongan15persen3            = $pph21_setahun_ini['potongan15persen3'];
				$potongan15persen             = $pph21_setahun_ini['potongan15persen'];
				$potongan25persen             = $pph21_setahun_ini['potongan25persen'];
				$potongan30persen             = $pph21_setahun_ini['potongan30persen'];
				//Untuk Wajib Pajak yang tidak memiliki NPWP, dikenai tarif pph 21 sebesar 20% lebih tinggi dari mereka yang memiliki NPWP.
				$faktor_kali_pph              = 1;
				if (empty(trim( $staf_ini->npwp ))) {
					$faktor_kali_pph          = 1.2;
				}
				$pph21setahun                 = $pph21setahun * $faktor_kali_pph;
				//pph21 yang dibayarkan bulan ini dibagi 12 dari pph21 setahun dikurangi pph21 yang sudah dibayarkan bulan ini
				$pph21                        = ( $pph21setahun / 12 ) - $total_pph_bulan_ini;
		   }
		   return [
			   'ptkp_dasar'                  => Yoga::buatrp( $ptkp ),
			   'ptkp'                        => $total_ptkp,
			   'npwp'                        => $staf_ini->npwp,
			   'jumlah_anak'                 => $staf_ini->jumlah_anak,
			   'menikah'                     => $staf_ini->menikah,
			   'ptkp_ini'                    => $ptkp,
			   'total_gaji_bulan_ini'        => $total_gaji_bulan_ini,
			   'biaya_jabatan'               => $biaya_jabatan,
			   'gaji_netto_setahun'          => $gaji_netto_setahun,
			   'total_ptkp'                  => $total_ptkp,
			   'penghasilan_kena_pajak'      => $penghasilan_kena_pajak,
			   'potongan5persen'             => $potongan5persen,
			   'potongan15persen'            => $potongan15persen,
			   'potongan25persen'            => $potongan25persen,
			   'potongan30persen'            => $potongan30persen,
			   'pph21setahun'                => $pph21setahun,
			   'faktor_kali_pph'             => $faktor_kali_pph,
			   'pph21setahun'                => $pph21setahun,
			   'gaji_netto'                  => $gaji_netto,
			   'gaji_netto_setahun'          => $gaji_netto_setahun,
			   'pph21'                       => $pph21
		   ];
	}
	private function pph21setahun( $penghasilan_kena_pajak ){
		$potongan5persen                      = 0;
		$potongan15persen                     = 0;
		$potongan15persen1                    = 0;
		$potongan15persen2                    = 0;
		$potongan15persen3                    = 0;
		$potongan25persen                     = 0;
		$potongan30persen                     = 0;
		if ( $penghasilan_kena_pajak         <= 50000000) {
			$potongan5persen                  = $penghasilan_kena_pajak * 0.05;
		} else if(  $penghasilan_kena_pajak  <= 250000000 ){
			$potongan5persen                  = 50000000 * 0.05;
			$potongan15persen                += ( $penghasilan_kena_pajak - 50000000 ) * 0.15;
			$potongan15persen1               += ( $penghasilan_kena_pajak - 50000000 ) * 0.15;
		} else if(   $penghasilan_kena_pajak <= 500000000  ){
			$potongan5persen                  = 50000000 * 0.05;
			$potongan15persen                += 200000000 * 0.15;
			$potongan15persen2               += ( $penghasilan_kena_pajak - 50000000 ) * 0.15;
			$potongan25persen                += ( $penghasilan_kena_pajak - 250000000 ) * 0.25;
		} else if(    $penghasilan_kena_pajak > 500000000   ){
			$potongan5persen                  = 50000000 * 0.05;
			$potongan15persen                += 200000000 * 0.15;
			$potongan15persen3               += ( $penghasilan_kena_pajak - 50000000 ) * 0.15;
			$potongan25persen                += 250000000 * 0.25;
			$potongan30persen                += ( $penghasilan_kena_pajak - 500000000 ) * 0.30;
		}
		$pph21setahun                         = $potongan5persen + $potongan15persen + $potongan25persen + $potongan30persen;
		if ( $pph21setahun < 0 ) {
			$pph21setahun                     = 0;
		}
		if ( $potongan5persen < 0 ) {
			$potongan5persen                  = 0;
		}
		if ( $potongan15persen < 0 ) {
			$potongan15persen                 = 0;
		}
		if ( $potongan25persen < 0 ) {
			$potongan25persen                 = 0;
		}
		if ( $potongan30persen < 0 ) {
			$potongan30persen                 = 0;
		}
		return [
			'potongan15persen1'              => $potongan15persen1,
			'potongan15persen2'              => $potongan15persen2,
			'potongan15persen3'              => $potongan15persen3,
			'pph21setahun'                   => $pph21setahun,
			'potongan5persen'                => $potongan5persen,
			'potongan15persen'               => $potongan15persen,
			'potongan25persen'               => $potongan25persen,
			'potongan30persen'               => $potongan30persen
		];
	}
	private function perhitunganPtkp($ptkp, $jumlah_anak, $menikah, $jenis_kelamin){
		if ($jumlah_anak > 3) {
			$jumlah_anak_ptkp                 = 3;
		} else {
			$jumlah_anak_ptkp                 = $jumlah_anak;
		}
		if ( $jenis_kelamin                  == 0 ) {
			return $ptkp;
		} else {
			return $ptkp + $ptkp/12*$menikah + $ptkp/12*$jumlah_anak_ptkp;
		}
	}
	private function parameterPtkp($staf_ini, $tahun_pembayaran, $model, $mulai_parameter){
		$menikah             = $staf_ini->menikah;
		$jumlah_anak         = $staf_ini->jumlah_anak;

		try {
			$bagi_gigis      = $model::where($mulai_parameter, 'like',  $tahun_pembayaran . '%')
										->where('staf_id', $staf_ini->id)
										->firstOrFail();
			if ( isset($bagi_gigis->menikah) ) {
				$menikah     = $bagi_gigis->menikah;
			}
			if ( isset($bagi_gigis->jumlah_anak) ) {
				$jumlah_anak = $bagi_gigis->jumlah_anak;
			}
			return $bagi_gigis;
		} catch (\Exception $e) {
		}

		return [
			'menikah' => $menikah,
			'jumlah_anak' => $jumlah_anak,
		];
	}
	public function gopay(){

		try {

			$ju = JurnalUmum::whereNull('coa_id')->firstOrFail();

		} catch (\Exception $e) {

			$query  = "SELECT ";
			$query .= "SUM(CASE WHEN menambah = '1' THEN nilai ELSE 0 END) AS menambah,";
			$query .= "SUM(CASE WHEN menambah = '0' THEN nilai ELSE 0 END) AS mengurangi ";
			$query .= "FROM go_pays";

			$menambah   = 0;
			$mengurangi = 0;
			$data = DB::select($query)[0];
			if ($data->menambah) {
				$menambah = $data->menambah;
			}
			if ($data->mengurangi) {
				$mengurangi = $data->mengurangi;
			}

			$saldo = $menambah - $mengurangi;

			$gopays = GoPay::with('pengeluaran.staf')->latest()->paginate(10);

			return view('pengeluarans.gopay', compact(
				'gopays',
				'saldo'
			));
		}
		return redirect('jurnal_umums/coa')->withPesan(Yoga::gagalFlash('Ada beberapa Chart Of Account yang harus disesuaikan dulu'));
	}
	private function queryData($keterangan, $tanggal, $petugas, $pass, $displayed_rows, $count = false){
		$query  = "SELECT ";
		if (!$count) {
			$query .= "pg.id as pg_id, ";
			$query .= "pg.tanggal as tanggal, ";
			$query .= "pg.keterangan as keterangan, ";
			$query .= "pg.nilai as nilai, ";
			$query .= "sp.nama as supplier, ";
			$query .= "sp.id as supplier_id ";
		} else {
			$query .= "count(pg.id) as jumlah ";
		}
		$query .= "FROM pengeluarans as pg ";
		$query .= "JOIN suppliers as sp on sp.id = pg.supplier_id ";
		$query .= "WHERE ";
		$query .= "(pg.keterangan like ? or ? = '') ";
		$query .= "AND (pg.tanggal like ? or ? = '') ";
		$query .= "AND (sp.nama like ? or ? = '') ";
		$query .= "ORDER BY pg.created_at desc ";
		if (!$count) {
			$query .= "LIMIT {$pass}, {$displayed_rows} ";
		}	
		$data = DB::select($query, [
			'%' . $keterangan . '%',
			$keterangan ,
			'%' . $tanggal . '%',
			$tanggal ,
			'%' . $petugas . '%',
			$petugas 
		]);
		return $data;
	}
	public function gojek(){
		$query  = "SELECT ";
		$query .= "SUM(CASE WHEN menambah = '1' THEN nilai ELSE 0 END) AS menambah,";
		$query .= "SUM(CASE WHEN menambah = '0' THEN nilai ELSE 0 END) AS mengurangi ";
		$query .= "FROM go_pays";
		$datas = DB::select($query);
		$total = $datas[0]->menambah - $datas[0]->mengurangi;
		$ggs = GoPay::latest()->paginate(15);

		return view('pengeluarans.gojek', compact('ggs', 'total'));


	}
	public function tambahGopay(){
		$rules           = [
			'nilai'   => 'required',
			'tanggal'   => 'required',
			'sumber_uang_id'   => 'required',
			'staf_id'   => 'required'
		];
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
		$gg          = new GoPay;
		$gg->nilai   = Yoga::clean( Input::get('nilai') );
		$gg->menambah = 1;
		$gg->tanggal = Yoga::datePrep( Input::get('tanggal') );
		$gg->sumber_uang_id = Input::get('sumber_uang_id');
		$gg->staf_id = Input::get('staf_id');
		$confirm = $gg->save();
		if ($confirm && $gg->nilai > 0) {

			$jurnals = [];
			$jurnals[] = [
				'jurnalable_id'   => $gg->id,
				'jurnalable_type' => 'App\GoPay',
				'debit'           => 1,
				'coa_id'          => '112001',
				'nilai'           => $gg->nilai,
				'created_at'      => $gg->tanggal->format('Y-m-d H:i:s'),
				'updated_at'      => $gg->tanggal->format('Y-m-d H:i:s')
			];

			$jurnals[] = [
				'jurnalable_id'   => $gg->id,
				'jurnalable_type' => 'App\GoPay',
				'debit'           => 0,
				'coa_id'          => Input::get('sumber_uang_id'),
				'nilai'           => $gg->nilai,
				'created_at'      => $gg->tanggal->format('Y-m-d H:i:s'),
				'updated_at'      => $gg->tanggal->format('Y-m-d H:i:s')
			];
			JurnalUmum::insert($jurnals);
		}


		$pesan = Yoga::suksesFlash('Go Pay berhasil ditambah pulsa sebesar ' . Yoga::buatrp($gg->nilai));
		return redirect()->back()->withPesan($pesan);

	}
	public function pakaiGopay(){

		$rules           = [
			'staf_id'   => 'required',
			'tujuan'   => 'required',
			'nilai'   => 'required'
		];
		
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}

		$gg             = new GoPay;
		$gg->nilai      = Yoga::clean( Input::get('nilai') );
		$gg->staf_id    = Input::get('staf_id');
		$gg->menambah   = 0;
		$gg->tanggal = Yoga::datePrep( Input::get('tanggal') );
		$gg->keterangan = Input::get('tujuan');
		$confirm        = $gg->save();

		if ($confirm && $gg->nilai > 0) {
			$jurnals = [];
			$jurnals[] = [
				'jurnalable_id'   => $gg->id,
				'jurnalable_type' => 'App\GoPay',
				'debit'           => 1,
				'coa_id'          => '612345',
				'nilai'           => $gg->nilai,
				'created_at'      => $gg->tanggal->format('Y-m-d H:i:s'),
				'updated_at'      => $gg->tanggal->format('Y-m-d H:i:s')
			];

			$jurnals[] = [
				'jurnalable_id'   => $gg->id,
				'jurnalable_type' => 'App\GoPay',
				'debit'           => 0,
				'coa_id'          => '112001',
				'nilai'           => $gg->nilai,
				'created_at'      => $gg->tanggal->format('Y-m-d H:i:s'),
				'updated_at'      => $gg->tanggal->format('Y-m-d H:i:s')
			];
			JurnalUmum::insert($jurnals);
		}
		$pesan = Yoga::suksesFlash('Go Pay berhasil dipakai sebesar ' . Yoga::buatrp($gg->nilai));
		return redirect()->back()->withPesan($pesan);
	}
	public function showInputHarta($id){
		$harta = InputHarta::find($id);
		return view('pengeluarans.showInputHarta', compact(
			'harta'
		));
	}
	private function queryPeralatan($model){

		$query  = "SELECT ";
		if ($model == 'App\\\BelanjaPeralatan') {
			$query .= "bp.peralatan as peralatan,";
		} else {
			$query .= "bp.keterangan as peralatan,";
		}
		$query .= "fb.tanggal as tanggal,";
		$query .= "st.nama as nama,";
		$query .= "bp.harga_satuan as harga_satuan,";
		$query .= "bp.jumlah as jumlah, ";
		$query .= "sum(pn.nilai) as penyusutan ";
		if ($model == 'App\\\BelanjaPeralatan') {
			$query .= ",bp.masa_pakai as masa_pakai ";
		}
		$query .= "FROM penyusutans as pn ";
		if ($model == 'App\\\BelanjaPeralatan') {
			$query .= "JOIN belanja_peralatans as bp on bp.id = pn.susutable_id ";
		} else {
			$query .= "JOIN bahan_bangunans as bp on bp.id = pn.susutable_id ";
		}
		$query .= "JOIN faktur_belanjas as fb on fb.id = bp.faktur_belanja_id ";
		$query .= "JOIN stafs as st on st.id = fb.petugas_id ";
		$query .= "WHERE susutable_type= '{$model}' ";
		$query .= "GROUP BY pn.susutable_id ";
		$query .= "ORDER BY bp.id desc";
		return DB::select($query);

	}
	
}
