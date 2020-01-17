<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Classes\Yoga;
use App\JurnalUmum;
use App\Http\Controllers\JurnalUmumsController;
use Input;
use DB;

class LaporanLabaRugisController extends Controller
{

  public function __construct()
    {
        $this->middleware('notready', ['only' => ['perBulan']]);
    }
    public function index(){

		$query  = "SELECT year(created_at) as tahun ";
		$query .= "FROM jurnal_umums ";
		$query .= "GROUP BY year(created_at);";
		$data = DB::select($query);

		$tahun=[];
		foreach ($data as $d) {
			$tahun[$d->tahun] = $d->tahun;
		}

		$periode = [
			null => ' - Pilih - ',
			'1' => 'Per Bulan',
			'2' => 'Per Tahun'
		];


		$data_bulan = array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");

		$bulan = [];

		foreach ($data_bulan as $k => $b) {
			$bulan[$k +1] = $b;
		}

		return view('laporan_laba_rugis.index',compact(
			'tahun',
			'bulan'
		));
    }
    public function bikinan(){
		$periode = [
			null => ' - Pilih - ',
			'1' => 'Per Bulan',
			'2' => 'Per Tahun'
		];
    	return view('laporan_laba_rugis.bikinan',compact('periode'));
    }


	public function bikinanShow(){
		$periode = Input::get('periode');
		$bulan = Input::get('bulan');
		$tahun = Input::get('tahun');

		if ($periode == '1') {
			return redirect('laporan_laba_rugis/perBulan/'.$bulan . '/' . $tahun . '/bikinan');
		} else if ($periode == '2'){
			return redirect('laporan_laba_rugis/perTahun/'.$tahun . '/bikinan');
		}
	}

	public function show(){
		$bulan_awal  = Input::get('bulan_awal');
		$bulan_akhir = Input::get('bulan_akhir');
		$tahun_awal  = Input::get('tahun_awal');
		$tahun_akhir = Input::get('tahun_akhir');

		$tanggal_awal  = $tahun_awal. "-" . str_pad($bulan_awal, 2, '0', STR_PAD_LEFT)  . "-01"  ;
		$tanggal_akhir = $tahun_akhir. "-" . str_pad($bulan_akhir, 2, '0', STR_PAD_LEFT) . "-01"  ;
		$tanggal_akhir = date("Y-m-t", strtotime($tanggal_akhir));

		$tempLaporanLabaRugi = $this->tempLaporanLabaRugiRangeByDate($tanggal_awal, $tanggal_akhir);

		$pendapatan_usahas   = $tempLaporanLabaRugi['pendapatan_usahas'];
		$hpps                = $tempLaporanLabaRugi['hpps'];
		$biayas              = $tempLaporanLabaRugi['biayas'];
		$pendapatan_lains    = $tempLaporanLabaRugi['pendapatan_lains'];
		$bulan               = $tempLaporanLabaRugi['bulan'];
		$tahun               = $tempLaporanLabaRugi['tahun'];
		$bebans              = $tempLaporanLabaRugi['bebans'];

    	return view('laporan_laba_rugis.show', compact(
            'pendapatan_usahas',
            'hpps',
            'tanggal_awal',
            'tanggal_akhir',
            'biayas',
            'pendapatan_lains',
            'bulan',
            'tahun',
            'bebans'
        ));
	}

    public function perBulanBikinan($bulan, $tahun){

		$templaporanlabarugibikinan = $this->templaporanlabarugibikinan($bulan, $tahun, 'perBulan');
		$pendapatan_usahas = $templaporanlabarugibikinan['pendapatan_usahas'];
		$hpps              = $templaporanlabarugibikinan['hpps'];
		$biayas            = $templaporanlabarugibikinan['biayas'];
		$pendapatan_lains  = $templaporanlabarugibikinan['pendapatan_lains'];
		$bulan             = $templaporanlabarugibikinan['bulan'];
		$tahun             = $templaporanlabarugibikinan['tahun'];
		$bebans            = $templaporanlabarugibikinan['bebans'];
		//return $pendapatan_usahas['akuns'];
    	return view('laporan_laba_rugis.show', compact(
            'pendapatan_usahas',
            'hpps',
            'biayas',
            'pendapatan_lains',
            'bulan',
            'tahun',
            'bebans'
        ));
    }
	public function perTahunBikinan($tahun){
		$path = Input::path();
		/* $jn = new JurnalUmumsController; */
		/* $ju->notReady($path); */
		$templaporanlabarugibikinan = $this->templaporanlabarugibikinan(null, $tahun, 'perTahun');
		$pendapatan_usahas = $templaporanlabarugibikinan['pendapatan_usahas'];
		$hpps              = $templaporanlabarugibikinan['hpps'];
		$biayas            = $templaporanlabarugibikinan['biayas'];
		$pendapatan_lains  = $templaporanlabarugibikinan['pendapatan_lains'];
		$bulan             = $templaporanlabarugibikinan['bulan'];
		$tahun             = $templaporanlabarugibikinan['tahun'];
		$bebans            = $templaporanlabarugibikinan['bebans'];
		//return $pendapatan_usahas['akuns'];
    	return view('laporan_laba_rugis.show', compact(
            'pendapatan_usahas',
            'hpps',
            'biayas',
            'pendapatan_lains',
            'bulan',
            'tahun',
            'bebans'
        ));
	}
    public function perBulan($bulan, $tahun){
		$per = '/pdfs/laporan_laba_rugi/perBulan/'. $bulan . '/' . $tahun ;
		$tempLaporanLabaRugi = $this->tempLaporanLabaRugi($bulan, $tahun, 'perBulan');
		$pendapatan_usahas   = $tempLaporanLabaRugi['pendapatan_usahas'];
		$hpps                = $tempLaporanLabaRugi['hpps'];
		$biayas              = $tempLaporanLabaRugi['biayas'];
		$pendapatan_lains    = $tempLaporanLabaRugi['pendapatan_lains'];
		$bulan               = $tempLaporanLabaRugi['bulan'];
		$tahun               = $tempLaporanLabaRugi['tahun'];
		$bebans              = $tempLaporanLabaRugi['bebans'];

    	return view('laporan_laba_rugis.show', compact(
            'pendapatan_usahas',
            'hpps',
            'per',
            'biayas',
            'pendapatan_lains',
            'bulan',
            'tahun',
            'bebans'
        ));
    }
	public function perTahun($tahun){
		$path = Input::path();
		$per = '/pdfs/laporan_laba_rugi/perTahun/'. $tahun;
		$tempLaporanLabaRugi = $this->tempLaporanLabaRugi(null, $tahun, 'perTahun');
		$pendapatan_usahas = $tempLaporanLabaRugi['pendapatan_usahas'];
		$hpps              = $tempLaporanLabaRugi['hpps'];
		$biayas            = $tempLaporanLabaRugi['biayas'];
		$pendapatan_lains  = $tempLaporanLabaRugi['pendapatan_lains'];
		$bulan             = $tempLaporanLabaRugi['bulan'];
		$tahun             = $tempLaporanLabaRugi['tahun'];
		$bebans            = $tempLaporanLabaRugi['bebans'];
		//return $pendapatan_usahas['akuns'];
    	return view('laporan_laba_rugis.show', compact(
            'pendapatan_usahas',
            'hpps',
            'per',
            'biayas',
            'pendapatan_lains',
            'bulan',
            'tahun',
            'bebans'
        ));
	}
	public function tempLaporanLabaRugi($bulan, $tahun, $periode){
		$query              = "select ";
		$query             .= "coa_id as coa_id, ";
		$query             .= "c.coa as coa, ";
		$query             .= "abs( sum( if ( debit = 1, nilai, 0 ) ) - sum( if ( debit = 0, nilai, 0 ) ) ) as nilai ";
		$query             .= "from jurnal_umums as j join coas as c on c.id = j.coa_id ";
		if ($periode       == 'perBulan') {
			$query         .= "where j.created_at like '{$tahun}-{$bulan}%' ";
		}else if( $periode == 'perTahun' ) {
			$query         .= "where j.created_at like '{$tahun}%' ";
		}
		$query             .= "and ( coa_id like '4%' or coa_id like '5%' or coa_id like '6%' or coa_id like '7%' or coa_id like '8%' ) ";
		$query             .= "group by coa_id ";

        $akuns              = DB::select($query);

		return $this->olahDataLaporanLabaRugi($akuns, $bulan, $tahun);
	}

	public function tempLaporanLabaRugiRangeByDate($tanggal_awal, $tanggal_akhir){
		$query  = "select ";
		$query .= "coa_id as coa_id, ";
		$query .= "c.coa as coa, ";
		$query .= "abs( sum( if ( debit = 1, nilai, 0 ) ) - sum( if ( debit = 0, nilai, 0 ) ) ) as nilai ";
		$query .= "from jurnal_umums as j join coas as c on c.id = j.coa_id ";
		$query .= "where date(j.created_at) between '{$tanggal_awal}' and '{$tanggal_akhir}'  ";
		$query .= "and ( coa_id like '4%' or coa_id like '5%' or coa_id like '6%' or coa_id like '7%' or coa_id like '8%' ) ";
		$query .= "group by coa_id ";
        $akuns              = DB::select($query);
		return $this->olahDataLaporanLabaRugi($akuns, null, null, $tanggal_awal, $tanggal_akhir);
	}

	public function templaporanlabarugibikinan($bulan, $tahun, $periode){
		$query              = "select ";
		$query             .= "coa_id as coa_id, ";
		$query             .= "px.asuransi_id as asuransi_id, ";
		$query             .= "j.jurnalable_type as jurnalable_type, ";
		$query             .= "c.coa as coa, ";
		$query             .= "abs( sum( if ( debit = 1, nilai, 0 ) ) - sum( if ( debit = 0, nilai, 0 ) ) ) as nilai ";
		$query             .= "from jurnal_umums as j join coas as c on c.id = j.coa_id ";
		$query             .= "left join periksas as px on px.id = j.jurnalable_id ";
		if ($periode       == 'perBulan') {
			$query         .= "where j.created_at like '{$tahun}-{$bulan}%' ";
		}else if( $periode == 'perTahun' ) {
			$query         .= "where j.created_at like '{$tahun}%' ";
		}
		$query             .= "and ( coa_id like '4%' or coa_id like '5%' or coa_id like '6%' or coa_id like '7%' or coa_id like '8%' ) ";
		$query             .= "and jurnalable_type not like 'App\\\Periksa' and (asuransi_id not like 0 or asuransi_id is null) ";
		$query             .= "group by coa_id ";
        $akuns              = db::select($query);

		return $this->olahDataLaporanLabaRugi($akuns, $bulan, $tahun);
	}
	private function olahDataLaporanLabaRugi($akuns, $bulan, $tahun, $tanggal_awal = null, $tanggal_akhir = null){

		$pendapatan_usahas['akuns'] = [];
		$hpps['akuns']              = [];
		$pendapatan_usahas['akuns'] = [];
		$hpps['akuns']              = [];
		$biayas['akuns']            = [];
		$pendapatan_lains['akuns']  = [];
		$bebans['akuns']            = [];

		$pendapatan_usahas['total_nilai'] = 0;
		$hpps['total_nilai']              = 0;
		$pendapatan_usahas['total_nilai'] = 0;
		$hpps['total_nilai']              = 0;
		$biayas['total_nilai']            = 0;
		$pendapatan_lains['total_nilai']  = 0;
		$bebans['total_nilai']            = 0;
		foreach ($akuns as $a) {
			if (substr($a->coa_id, 0, 1) === '4') {
				$pendapatan_usahas['akuns'][] = $a;
				$pendapatan_usahas['total_nilai'] += $a->nilai;
			} else if( substr($a->coa_id, 0, 1) === '5' ){
				$hpps['akuns'][] = $a;
				$hpps['total_nilai'] += $a->nilai;
			} else if( substr($a->coa_id, 0, 1) === '6' ){
				$biayas['akuns'][] = $a;
				$biayas['total_nilai'] += $a->nilai;
			} else if( substr($a->coa_id, 0, 1) === '7' ){
				$pendapatan_lains['akuns'][] = $a;
				$pendapatan_lains['total_nilai'] += $a->nilai;
			} else if( substr($a->coa_id, 0, 1) === '8' ){
				$bebans['akuns'][] = $a;
				$bebans['total_nilai'] += $a->nilai;
			}
		}
		return [
            'pendapatan_usahas' => $pendapatan_usahas,
            'hpps'              => $hpps,
            'biayas'            => $biayas,
            'pendapatan_lains'  => $pendapatan_lains,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'tanggal_awal'        => $tanggal_awal,
            'tanggal_akhir'       => $tanggal_akhir,
            'bebans'            => $bebans
		];
	}
}
