<?php


namespace App\Http\Controllers;

use Input;
use Image;
use App\Http\Requests;
use App\Http\Controllers\PengeluaransController;
use App\Staf;
use App\Pph21Dokter;
use App\Classes\Yoga;

class StafsController extends Controller
{


	public $input_id;
	public $input_alamat_domisili;
	public $input_alamat_ktp;
	public $input_email;
	public $input_titel;
	public $input_ktp;
	public $input_jenis_kelamin;
	public $input_nama;
	public $input_no_hp;
	public $input_no_telp;
	public $input_ada_penghasilan_lain;
	public $input_str;
	public $input_menikah;
	public $input_jumlah_anak;
	public $input_npwp;
	public $input_image;
	public $input_ktp_image;
	public $input_str_image;
	public $input_sip_image;
	public $input_gambar_npwp;
	public $input_kartu_keluarga;
	public $input_tanggal_lahir;
	public $input_tanggal_lulus;
	public $input_tanggal_mulai;
	public $input_universitas_asal;

	  public function __construct() {
		$this->input_alamat_domisili      = Input::get('alamat_domisili');
		$this->input_alamat_ktp           = Input::get('alamat_ktp');
		$this->input_email                = Input::get('email');
		$this->input_titel                = Input::get('titel');
		$this->input_ktp                  = Input::get('ktp');
		$this->input_jenis_kelamin        = Input::get('jenis_kelamin');
		$this->input_nama                 = ucwords(strtolower(Input::get('nama'))) . ', ' . Input::get('titel');
		$this->input_no_hp                = Input::get('no_hp');
		$this->input_no_telp              = Input::get('no_telp');
		$this->input_ada_penghasilan_lain = Input::get('ada_penghasilan_lain');
		$this->input_str                  = Input::get('str');
		$this->input_menikah              = Input::get('menikah');
		$this->input_jumlah_anak          = Input::get('jumlah_anak');
		$this->input_npwp                 = Input::get('npwp');
		$this->input_tanggal_lahir        = Yoga::datePrep( Input::get('tanggal_lahir') );
		$this->input_tanggal_lulus        = Yoga::datePrep( Input::get('tanggal_lulus') );
		$this->input_tanggal_mulai        = Yoga::datePrep( Input::get('tanggal_mulai') );
		$this->input_universitas_asal     = Input::get('universitas_asal');
        $this->middleware('super', ['only' => ['delete']]);
        /* $this->middleware('super', ['only' => ['delete','update']]); */
    }

	/**
	 * Display a listing of stafs
	 *
	 * @return Response
	 */
	public function index()
	{
		$stafs = Staf::all();
		// return 'oke';
		return view('stafs.index', compact('stafs'));
	}

	/**
	 * Show the form for creating a new staf
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('stafs.create');
	}

	/**
	 * Store a newly created staf in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = \Validator::make(Input::all(), Staf::$rules);

		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}
		$staf                 = new Staf;
		$staf->id             = Yoga::customId('App\Staf');
		$this->inputData($staf);


		return redirect('stafs')->withPesan(Yoga::suksesFlash('Staf baru <strong>' . $nama . '</strong> Berhasil <strong>Dimasukkan</strong>'));
	}

	/**
	 * Display the specified staf.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$staf = Staf::findOrFail($id);

		return view('stafs.show', compact('staf'));
	}

	/**
	 * Show the form for editing the specified staf.
	 *
	 * @param  int  $id
     * @return Response
	 */
	public function edit($id)
	{
		$staf = Staf::find($id);

		return view('stafs.edit', compact('staf'));
	}

	/**
	 * Update the specified staf in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$validator = \Validator::make(Input::all(), Staf::$rules);
		if ($validator->fails())
		{
			return \Redirect::back()->withErrors($validator)->withInput();
		}

		$staf = Staf::find($id);
		$this->inputData($staf);
		return redirect('stafs')->withPesan(Yoga::suksesFlash('Staf <strong>' . Input::get('nama') . '</strong> Berhasil <strong>Diubah</strong>'));
	}

	/**
	 * Remove the specified staf from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$staf = Staf::find($id);
		$nama = $staf->nama;
		if (!$staf->delete()) {
			return redirect()->back();
		}

		return redirect('stafs')->withPesan(Yoga::suksesFlash('Staf <strong>' . $nama . '</strong> Berhasil <strong>Dihapus</strong>'));
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
			$destination_path = public_path() . DIRECTORY_SEPARATOR . 'img/staf';

			// Mengambil file yang di upload
			$upload_cover->save($destination_path . '/' . $filename);
			
			//mengisi field bpjs_image di book dengan filename yang baru dibuat
			return 'img/staf/'. $filename;
			
		} else {
			return $staf->$fieldName;
		}

	}

	public function formPph21(){
		return view('stafs.formPph21', compact(
		));
}
public function cetakPph21(){
	return view('stafs.formPph21', compact(
	));
}
public function pph21DokterUmum(){

	$pphs = Pph21Dokter::latest()->paginate(20);
	return view('stafs.pphdokter', compact('pphs') );

}
	public function pph21dokterPost($id, $staf_id){
		$ada_penghasilan_lain = Input::get('ada_penghasilan_lain');
		$staf = Staf::find($staf_id);;
		$peng = new PengeluaransController;
		$tanggal_potong                       = date('Y-m-t 23:59:59', strtotime("-1 month"));
		$tahun_kemarin = date("Y",strtotime("-1 month"));

		$pph                                 = Pph21Dokter::find($id);
		$perhitunganPph_ini                  = $peng->pph21dokter($staf, $tahun_kemarin, $tanggal_potong, $ada_penghasilan_lain);
		$pph->pph21                          = $perhitunganPph_ini['pph21_kurang_bayar'];
		$pph->ptkp_dasar                     = $perhitunganPph_ini['ptkp_dasar'];
		$pph->potongan5persen_setahun        = $perhitunganPph_ini['potongan5persen'];
		$pph->potongan15persen_setahun       = $perhitunganPph_ini['potongan15persen'];
		$pph->potongan25persen_setahun       = $perhitunganPph_ini['potongan25persen'];
		$pph->potongan30persen_setahun       = $perhitunganPph_ini['potongan30persen'];
		$pph->penghasilan_bruto_setahun      = $perhitunganPph_ini['penghasilan_bruto_setahun'];
		$pph->penghasilan_kena_pajak_setahun = $perhitunganPph_ini['ptkp_setahun'];
		$pph->ada_penghasilan_lain           = $ada_penghasilan_lain;
		$confirm                             = $pph->save();

		$pesan = Yoga::suksesFlash('');
		return redirect()->back()->withPesan($pesan);
		
	}
	public function inputData(){
		
			$staf->alamat_domisili      = $this->input_alamat_domisili;
			$staf->alamat_ktp           = $this->input_alamat_ktp;
			$staf->email                = $this->input_email;
			$staf->titel                = $this->input_titel;
			$staf->ktp                  = $this->input_ktp;
			$staf->jenis_kelamin        = $this->input_jenis_kelamin;
			$staf->nama                 = $this->input_nama;;
			$staf->no_hp                = $this->input_no_hp;
			$staf->no_telp              = $this->input_no_telp;
			$staf->ada_penghasilan_lain = $this->input_ada_penghasilan_lain;
			$staf->str                  = $this->input_str;
			$staf->menikah              = $this->input_menikah;
			$staf->jumlah_anak          = $this->input_jumlah_anak;
			$staf->npwp                 = $this->input_npwp;
			$staf->tanggal_lahir        = Yoga::datePrep( $this->input_tanggal_lahir );
			$staf->tanggal_lulus        = Yoga::datePrep( $this->input_tanggal_lulus );
			$staf->tanggal_mulai        = Yoga::datePrep( $this->input_tanggal_mulai );
			$staf->universitas_asal     = $this->input_universitas_asal;
			$staf->save();
	}

}
