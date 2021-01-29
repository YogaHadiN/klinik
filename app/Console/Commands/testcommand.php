<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Outbox;
use Carbon\Carbon;
use App\Ht;
use App\Dm;
use App\User;
use App\Pengeluaran;
use App\Woowa;
use App\BayarDokter;
use App\GajiGigi;
use App\BayarGaji;
use App\PengantarPasien;
use App\Role;
use App\Panggilan;
use App\Rekening;
use App\PiutangAsuransi;
use App\Asuransi;
use App\Sms;
use App\StatusBpjs;
use App\AntrianPoli;
use App\KunjunganSakit;
use App\PembayaranAsuransi;
use App\CatatanAsuransi;
use App\AbaikanTransaksi;
use App\PiutangDibayar;
use App\NotaJual;
use App\PoliAntrian;
use App\KirimBerkas;
use App\PasienRujukBalik;
use App\JenisTarif;
use App\Pasien;
use App\Invoice;
use App\Terapi;
use App\AntrianPeriksa;
use App\Jobs\sendEmailJob;
use App\Tarif;
use App\DataDuplikat;
use App\FakturBelanja;
use App\JurnalUmum;
use App\Periksa;
use App\JenisAntrian;
use App\Telpon;
use App\Http\Controllers\WablasController;
use DB;
use Artisan;
use Mail;
use Log;
use Input;

class testcommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:command';
	public $jumlah_piutang_asuransi;
	public $jumlah_invoice;
	public $akumulasi_periksa_ids;
	public $jumlah_rekening;
	public $jumlah_nota_jual;
	public $jumlah_jurnal_umum;
	public $jumlah_piutang_dibayar;
	public $jumlah_pembayaran_asuransi;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is spartaaaa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

		$this->jumlah_piutang_asuransi    = 0;
		$this->jumlah_invoice             = 0;
		$this->jumlah_rekening            = 0;
		$this->jumlah_nota_jual           = 0;
		$this->jumlah_jurnal_umum         = 0;
		$this->jumlah_piutang_dibayar     = 0;
		$this->jumlah_pembayaran_asuransi = 0;
		$this->akumulasi_periksa_ids      = [];
    }

	public $estetika_buka = true;
	public $gigi_buka = true;


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		dd( preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", '2001-12-28') );
		/* dd( Carbon::createFromFormat('d-m-Y', '12/25/94') ); */
		/* $this->testAsuransi(); */
		/* $this->updatePC2020(); */
		/* $this->resetPembayaranAsuransis(); */
		/* $this->sederhanakanGaji(); */
		/* $this->rppt(); */
		/* $this->promoRapidTestCovid(); */
		/* $this->testJurnalUmum(); */
	}
	private function webhook(){
		$data["license"]="5c286f1ed7121";
		$data["url"]    ="https://yourwebsite.com/listen.php"; // message data will push to this url
		$data["no_wa"]  = "6289648615564";    //sender number registered in woowa
		$data["action"] = "set";

		$url="http://api.woo-wa.com/v2.0/webhook";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$err = curl_error($ch);
		curl_close ($ch);
		if ($err) {
			dd("cURL Error #:" . $err);
		} else {
			dd( $result);
		}
	}
	private function thisCoba(){
		$client->request('GET', '/get', [
			'headers' => [
				'User-Agent' => 'testing/1.0',
				'Accept'     => 'application/json',
				'X-Foo'      => ['Bar', 'Baz']
			]
		]);
	}


    private function apiBPJS()
    {

		$uri="https://dvlp.bpjs-kesehatan.go.id:9081/pcare-rest-v3.0/dokter/0/13"; //url web service bpjs;
		/* $uri="https://dvlp.bpjs-kesehatan.go.id:9081/pcare-rest-v3.0/provider/0/3"; //url web service bpjs; */
		/* $uri="https://dvlp.bpjs-kesehatan.go.id:9081/pcare-rest-v3.0/peserta/0001183422677"; //url web service bpjs; */
		$consID 	= env('BPJS_CONSID'); //customer ID anda
		$secretKey 	= env('BPJS_SECRET_KEY'); //secretKey anda

		$pcareUname = env('BPJS_PCARE_UNAME'); //username pcare
		$pcarePWD 	= env('BPJS_PCARE_PWD'); //password pcare anda
		$kdAplikasi	= env('BPJS_KD_APLIKASI'); //kode aplikasi

		$stamp		= time();
		$data 		= $consID.'&'.$stamp;

		$signature = hash_hmac('sha256', $data, $secretKey, true);
		$encodedSignature = base64_encode($signature);	
		$encodedAuthorization = base64_encode($pcareUname.':'.$pcarePWD.':'.$kdAplikasi);	

		$headers = array( 
					"Accept: application/json", 
					"X-cons-id:".$consID, 
					"X-timestamp: ".$stamp, 
					"X-signature: ".$encodedSignature, 
					"X-authorization: Basic " .$encodedAuthorization 
				); 

		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		$data = curl_exec($ch);
		curl_close($ch);
		dd($data);
		/* return $data; */
	}
	private function errorLog(){
		DB::statement("delete from coas where id in (select co.id from coas as co left join jurnal_umums as ju on ju.coa_id = co.id where ju.coa_id is null and co.id like '12%')");
		DB::statement("delete from coas where id in (select co.id from coas as co left join jurnal_umums as ju on ju.coa_id = co.id where ju.coa_id is null and co.id like '10%')");
		DB::statement("update jurnal_umums set nilai = 20000 where id = 226687;");
		DB::statement("update jurnal_umums set nilai = 35000 where id = 393460;");
		DB::statement("update jurnal_umums set nilai = 20000 where id = 459209;");
		DB::statement("update jurnal_umums set nilai = 35000 where id = 520931;");
		DB::statement("update jurnal_umums set nilai = 115000 where id = 721494;");
		DB::statement("update jurnal_umums set nilai = 35000 where id = 758562;");
		DB::statement("update jurnal_umums set nilai = 15000 where id = 768188;");
		DB::statement("update jurnal_umums set nilai = 35000 where id = 819723;");
		DB::statement("update jurnal_umums set nilai = 20000 where id = 964228;");
		DB::statement("update jurnal_umums set nilai = 35000 where id = 983506;");
		DB::statement("update jurnal_umums set nilai = 85000 where id = 307335;");
		DB::statement("delete from jurnal_umums where jurnalable_type = 'App\\\Pengeluaran' and jurnalable_id = 5182;");
		DB::statement("delete from pengeluarans where id = 5182;");
	}

	private function resetPembayaranAsuransis(){
		$pembayaran_asuransi_ids = [
				'1171'
		];

		foreach ($pembayaran_asuransi_ids as $pembayaran_asuransi_id) {
			$this->resetPembayaranAsuransi($pembayaran_asuransi_id);
		}

		dd(
			'piutang_asuransis = ' . $this->jumlah_piutang_asuransi,
			'invoice = ' .$this->jumlah_invoice,
			'rekening = ' .$this->jumlah_rekening,
			'nota_jual = ' .$this->jumlah_nota_jual,
			'jurnal_umum = ' .$this->jumlah_jurnal_umum,
			'piutang_dibayar = ' .$this->jumlah_piutang_dibayar,
			'pembayaran_asuransi = ' .$this->jumlah_pembayaran_asuransi,
			json_encode( $this->akumulasi_periksa_ids )
		);

	}
	/**
	* undocumented function
	*
	* @return void
	*/
	public function resetPembayaranAsuransi($pembayaran_asuransi_id)
	{
		$pembayaran_asuransi = PembayaranAsuransi::find( $pembayaran_asuransi_id );

		$piutang_dibayars    = PiutangDibayar::where('pembayaran_asuransi_id', $pembayaran_asuransi_id)->get();

		$periksa_ids         = [];

		foreach ($piutang_dibayars as $piutang) {

			$this->akumulasi_periksa_ids[] = $piutang->periksa_id;
			$periksa_ids[]                 = $piutang->periksa_id;

		}

		// update piutang asuransi
		$this->jumlah_piutang_asuransi = $this->jumlah_piutang_asuransi + PiutangAsuransi::whereIn('periksa_id', $periksa_ids)->update([
			'sudah_dibayar' => 0
		]);
		// piutang dibayar di delete

		$this->jumlah_invoice = $this->jumlah_invoice + Invoice::where('pembayaran_asuransi_id', $pembayaran_asuransi_id)->update([
			'pembayaran_asuransi_id' => null
		]);

		// update rekenings
		$this->jumlah_rekening = $this->jumlah_rekening + Rekening::where('pembayaran_asuransi_id', $pembayaran_asuransi_id)->update([
			'pembayaran_asuransi_id' => null
		]);

		// delete nota_jual
		$this->jumlah_nota_jual = $this->jumlah_nota_jual + NotaJual::destroy( $pembayaran_asuransi->nota_jual_id );

		// delete jurnal_umum
		$this->jumlah_jurnal_umum = $this->jumlah_jurnal_umum + JurnalUmum::where('jurnalable_id', $pembayaran_asuransi->nota_jual_id)
					->where('jurnalable_type', 'App\\NotaJual')
					->delete();

		$this->jumlah_piutang_dibayar = $this->jumlah_piutang_dibayar + PiutangDibayar::where('pembayaran_asuransi_id', $pembayaran_asuransi_id)->delete();

		$this->jumlah_pembayaran_asuransi = $this->jumlah_pembayaran_asuransi + $pembayaran_asuransi->delete();
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function testAsuransi()
	{
		 dd( [ null => 'Tidak' ] + Asuransi::list() );
		 /* dd(Asuransi::where('aktif', 1)->pluck('nama', 'id')); */
		 /* dd(Asuransi::pluck('nama', 'id')->all()); */
	}
	private function updatePC2020(){

		$periksas = Periksa::with('pasien')
							->where('asuransi_id', '200216001')
							->orWhere('asuransi_id', '200216001')
							->orWhere('asuransi_id', '200312001')
							->orWhere('asuransi_id', '200312002')
							->orWhere('asuransi_id', '37')
							->get();

		foreach ($periksas as $periksa) {
			$periksa->asuransi_id = $periksa->pasien->asuransi_id;
			$periksa->save();
		}


	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function promoRapidTestCovid() {
		$query          = "SELECT ";
		$query         .= "REPLACE(no_telp, '.', '') as no_telp, ";
		$query         .= "id ";
		$query         .= "FROM pasiens ";
		$query         .= "WHERE (no_telp like '+628%' ";
		$query         .= "OR no_telp like '08%') ";
		$query         .= "AND no_telp not like '%/%' ";
		$query         .= "AND CHAR_LENGTH(no_telp) >9 ";
		$query         .= "GROUP BY no_telp";
		$data           = DB::select($query);
		$duplikats      = DataDuplikat::all();
		$arrayDuplikat  = [];
		foreach ($duplikats as $d) {
			$arrayDuplikat[] = $d->no_telp;
		}
		$returnData = [];
		$dataduplikats=[];
		$bolehdimasukkan = false;
		foreach ($data as $foo) {
			if ( !in_array( $foo->no_telp, $arrayDuplikat ) ) {
				$returnData[] = [
					'no_telp' => $foo->no_telp,
					'pesan'   => $this->pesanPromo($foo->id)
				];

				if ( $foo->no_telp == '0895363089282' ) {
					$bolehdimasukkan = true;
				}

				/* Sms::send($foo->no_telp, $this->pesanPromo($foo->id)); */
				if ( !$bolehdimasukkan ) {
					$dataduplikats[] = [
						'no_telp' => $foo->no_telp
					];
				}
			}
		}
		DataDuplikat::insert($dataduplikats);
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function sederhanakanGaji()
	{
		$datas= [];
		$gaji_dokters = BayarDokter::all();
		$gaji_gigis = GajiGigi::all();
		foreach ($gaji_dokters as $gaji) {
			if ( !empty ( $gaji->petugas_id )) {
				$petugas_id = $gaji->petugas_id;
			} else {
				$petugas_id = 16;
			}
			$datas[] = [ 
				'staf_id'              => $gaji->staf_id,
				'mulai'                => $gaji->mulai,
				'akhir'                => $gaji->akhir,
				'gaji_pokok'           => $gaji->nilai,
				'bonus'                => 0,
				'tanggal_dibayar'      => $gaji->tanggal_dibayar,
				'sumber_uang_id'       => $gaji->sumber_uang_id,
				'created_at'           => $gaji->created_at,
				'updated_at'           => $gaji->updated_at,
				'petugas_id'           => $petugas_id,
				'hutang'               => $gaji->hutang
			];
		}
		foreach ($gaji_gigis as $gaji) {
			if ( !empty ( $gaji->petugas_id )) {
				$petugas_id = $gaji->petugas_id;
			} else {
				$petugas_id = 16;
			}
			$datas[] = [ 
				'staf_id'              => $gaji->staf_id,
				'mulai'                => $gaji->mulai,
				'akhir'                => $gaji->akhir,
				'gaji_pokok'           => $gaji->nilai,
				'bonus'                => 0,
				'tanggal_dibayar'      => $gaji->tanggal_dibayar,
				'sumber_uang_id'       => 110000,
				'petugas_id'           => $petugas_id,
				'created_at'           => $gaji->created_at,
				'updated_at'           => $gaji->updated_at,
				'hutang'               => 0
			];
		}
		BayarGaji::insert($datas);
		DB::statement('drop table bayar_dokters');
		DB::statement('drop table gaji_gigis');
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	public function testQueue()
	{
		$foos = [
			11,12,13,14,15,16,17,18,19,110
		];
		foreach ($foos as $foo) {
			sendEmailJob::dispatch($foo)->delay(now()->addSeconds(1));
		}
		return 'sukses!!';
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function perbaikiBayarDokterDanGajiGigi()
	{
		$hitung = [];
		$jurnal_umums  = JurnalUmum::where('jurnalable_type', 'App\\BayarDokter')->get();
		foreach ($jurnal_umums as $ju) {
			$created_at           = $ju->created_at;
			$query                = "SELECT bg.id as id from bayar_gajis as bg ";
			$query               .= "JOIN stafs as stf on stf.id = bg.staf_id ";
			$query               .= "WHERE stf.titel = 'dr' ";
			$query               .= "AND bg.created_at = '{$created_at}';";
			$bayar_gaji           = DB::select($query);
			if ( count($bayar_gaji) ) {
				$ju->jurnalable_id    = $bayar_gaji[0]->id;
				$ju->jurnalable_type  = 'App\\BayarGaji';
				$ju->save();
			}
		}
		$jurnal_umums  = JurnalUmum::where('jurnalable_type', 'App\\GajiGigi')->get();
		foreach ($jurnal_umums as $ju) {
			$query                = "SELECT bg.id as id from bayar_gajis as bg ";
			$query               .= "JOIN stafs as stf on stf.id = bg.staf_id ";
			$query               .= "WHERE stf.titel = 'drg' ";
			$query               .= "AND bg.gaji_pokok = '{$ju->nilai}' ";
			$query               .= "AND bg.created_at = '{$ju->created_at}';";
			$bayar_gaji           = DB::select($query);
			if ( count($bayar_gaji) ) {
				$ju->jurnalable_id    = $bayar_gaji[0]->id;
				$ju->jurnalable_type  = 'App\\BayarGaji';
				$ju->save();
			}
		}
	}

	private function testJurnalUmum() {
		$hitung = [];
		/* $jurnal_umums  = JurnalUmum::all(); */
		$query  = "SELECT *";
		$query .= "FROM jurnal_umums;";
		$data = DB::select($query);
		dd('kil');
		foreach ($jurnal_umums as $ju) {
			dd( $ju );
		}
	}
	private function pesanPromo($id){

		$pesan = "*Klinik Jati Elok*";
		$pesan .= PHP_EOL;
		$pesan .= "*Komp. Bumi Jati Elok Blok A I No. 4-5*";
		$pesan .= PHP_EOL;
		$pesan .= "*Jl. Raya Legok - Parung Panjang km. 3*";
		$pesan .= PHP_EOL;
		$pesan .= "Melayani";
		$pesan .= PHP_EOL;
		$pesan .= "Rapid Test Antibody & RapiD Test Antigen (Swab Test Antigen)";
		$pesan .= PHP_EOL;
		$pesan .= PHP_EOL;
		$pesan .= "*Paket 1 | Rapid Test Antibody*";
		$pesan .= PHP_EOL;
		$pesan .= "(Rp.150.000)";
		$pesan .= PHP_EOL;
		$pesan .= "hasil keluar 15-30 menit";
		$pesan .= PHP_EOL;
		$pesan .= "darah kapiler";
		$pesan .= PHP_EOL;
		$pesan .= PHP_EOL;
		$pesan .= "*Paket 2 | Rapid Test Antigen (Swab Antigen)*";
		$pesan .= PHP_EOL;
		$pesan .= "(Rp.250.000)";
		$pesan .= PHP_EOL;
		$pesan .= "hasil keluar 30 menit- 1 jam";
		$pesan .= PHP_EOL;
		$pesan .= "metode swab belakang hidung / tenggorokan";
		$pesan .= PHP_EOL;
		$pesan .= "*Sebagai syarat perjalanan udara/laut/darat";
		$pesan .= PHP_EOL;
		$pesan .= "*dengan perjanjian";
		$pesan .= PHP_EOL;
		$pesan .= PHP_EOL;
		$pesan .= "Informasi hubungi 021 5977 529";
		$pesan .= PHP_EOL;
		$pesan .= "Atau whatsapp ke nomor 082278065959";
		$pesan .= PHP_EOL;
		$pesan .= "Atau klik https://wa.wizard.id/df2299";
		$pesan .= PHP_EOL;
		$pesan .= PHP_EOL;
		$pesan .= $id;

		return $pesan;
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function rppt()
	{
		$dms = Dm::all();
		foreach ($dms as $dm) {
			$pasiens = Pasien::where('tanggal_lahir', $dm->tanggal_lahir)
						->where('sex', $dm->jenis_kelamin)
						->where('nama', 'like', '%' .$dm->nama. '%')
						->get();
			if ( $pasiens->count() ==  1 ) {
				foreach ($pasiens as $p) {
					$p->prolanis_dm = 1;
					$p->save();
				}
				$dm->delete();
				
			}
		}

		$hts   = Ht::all();
		foreach ($hts as $ht) {
			$pasiens = Pasien::where('tanggal_lahir', $ht->tanggal_lahir)
						->where('sex', $ht->jenis_kelamin)
						->where('nama', 'like', '%' .$ht->nama. '%')
						->get();
			if ( $pasiens->count() ==  1 ) {
				foreach ($pasiens as $p) {
					$p->prolanis_ht = 1;
					$p->save();
				}
				$ht->delete();
			}
		}
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function storePasien($param)
	{
	}
	private function storePasien($param)
	{
	}
	
}
