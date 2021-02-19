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
		$this->updateRekeningHalt();
		/* $p= Pasien::find( '170405023' ); */
		/* dd( $p->tanggal_lahir ); */
		/* $p->tanggal_lahir; */

		/* $this->testAdmedika(); */
		/* dd( Carbon::createFromFormat('d-m-Y', '12/25/94') ); */
		/* $this->testAsuransi(); */
		/* $this->updatePC2020(); */
		/* $this->resetPembayaranAsuransis(); */
		/* $this->sederhanakanGaji(); */
		/* $this->rppt(); */
		/* $this->promoRapidTestCovid(); */
		/* $this->testJurnalUmum(); */
	}
	/**
	* undocumented function
	*
	* @return void
	*/
	private function testAdmedika()
	{
		$uri="https://mobile.admedika.co.id/admedgateway/services/api/?method=CustomerHost"; //url web service bpjs;
		/* $uri="https://dvlp.bpjs-kesehatan.go.id:9081/pcare-rest-v3.0/provider/0/3"; //url web service bpjs; */
		/* $uri="https://dvlp.bpjs-kesehatan.go.id:9081/pcare-rest-v3.0/peserta/0001183422677"; //url web service bpjs; */

		$tokenAuth          = env('ADMEDIKA_TOKEN_AUTH'); //Admedika Token Auth
		$serviceID          = env('ADMEDIKA_SERVICE_ID'); //Admedika Token Auth
		$customerID         = env('ADMEDIKA_CUSTOMER_ID'); //Admedika Token Auth
		$requestID          = env('ADMEDIKA_REQUEST_ID'); //Admedika Token Auth
		$txnData            = env('ADMEDIKA_TXN_DATA'); //Admedika Token Auth
		$txnRequestDateTime = env('ADMEDIKA_TXN_REQUEST_DATE_TIME'); //Admedika Token Auth

		$headers = array( 
					"Accept: application/json", 
					"tokenAuth: ".$tokenAuth,
					"serviceID: ".$serviceID,
					"customerID: ".$customerID,
					"requestID: ".$requestID,
					"txnData: ".$txnData,
					"txnRequestDateTime: ".$txnRequestDateTime
				); 

		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		$data = curl_exec($ch);
		curl_close($ch);
		dd($data);
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
	private function updateRekeningHalt()
	{
		$rekenings = Rekening::all();

		$cont_abaikan_transaksis= [];
		$rek_temp = [];

		$dont_delete = [
			"Arz6obyOKjK",
			 "G1kaG5wa5jg",
			 "mVz5ZK1vwjv",
			 "eMkbmLx6LWY",
			 "VPW8BlAmnzr",
			 "pPkBg3y3dzB",
			 "57WnapgdYjl",
			 "Z6zKlxvnLjJ",
			 "32zpap2ExjA",
			 "bLjJ3MLKeWO",
			 "G1kaGxO6Bjg",
			 "Epzw01pJLjN",
			 "eMkbmKrnGWY",
			 "QdkN2y6Ydke",
			 "VGjZ5yb0Ezr",
			 "ylzrB0lDMzx",
			 "VPW8BGdw5zr",
			 "0RkQ2xZm1zG",
			 "Aqz9nJq2ajP",
			 "pPkBgDPlRzB",
			 "ylzrBpy51zx",
			 "pokORNOMyWa",
			 "olk4N3AodjJ",
			 "ypkvKZ9vPzM",
			 "G4kY2xgNXzp",
			 "Arz6oALBwjK",
			 "3ykVO67KVzN",
			 "ylzrBZ0gEzx",
			 "NZjx8Y25dj4",
			 "q7WPOyBKnjA",
			 "Z4jAgaVn9kA",
			 "ylzrBZ5v8zx",
			 "9xzXOMA95kP",
			 "KwjmaXBZpjr",
			 "Z4jAgaDqlkA",
			 "3EWgaZwpMzP",
			 "KwjmaX4l6jr",
			 "ylzrBZdxrzx",
		];
		foreach ($rekenings as $k => $r) {
			if ( !in_array( $r->id, $dont_delete  ) ) {
				$rek_temp[] = [
					'id'                     => $k +1,
					'akun_bank_id'           => $r->akun_bank_id,
					'tanggal'                => $r->tanggal->format('Y-m-d'),
					'deskripsi'              => $r->deskripsi,
					'nilai'                  => $r->nilai,
					'saldo_akhir'            => $r->saldo_akhir,
					'debet'                  => $r->debet,
					'created_at'             => $r->created_at->format('Y-m-d'),
					'updated_at'             => $r->updated_at->format('Y-m-d'),
					'pembayaran_asuransi_id' => $r->pembayaran_asuransi_id,
					'old_id'                 => $r->id
				];
				$cont_abaikan_transaksis[] = [
					'old_id' => $r->id,
					'new_id' => $k + 1
				];
			}
		}
		$adding = array(
					0 => array('tanggal' => '2021-02-17 00:00:00', 'deskripsi' => 'MCM InhouseTrf CS-CS 068476TBK022021 DARI ASURANSI JIWA INHEALTH INDONESIA 068476TBK022021', 'nilai' => '2455000'),
					1 => array('tanggal' => '2021-02-17 00:00:00', 'deskripsi' => 'MCM InhouseTrf CS-CS INV3KJEPYR160207 80000186 DARI AXA MANDIRI FINANCIAL SERVICES INV3KJEPYR160207 80000186', 'nilai' => '220000'),
					2 => array('tanggal' => '2021-02-17 00:00:00', 'deskripsi' => 'MCM InhouseTrf CS-CS INV3KJEPYR160207 80000186 DARI AXA MANDIRI FINANCIAL SERVICES INV3KJEPYR160207 80000186', 'nilai' => '55000'),
					3 => array('tanggal' => '2021-02-16 00:00:00', 'deskripsi' => 'INW.CN-SKN CR SA-MCS ASURANSI RELIANCE INDONESIA - 022 CIMB NIAGA PURWAKARTA RELIANCE PROV 2009103315503 20210215993 CMB2215279303200 2021021600', 'nilai' => '590000'),
					4 => array('tanggal' => '2021-02-15 00:00:00', 'deskripsi' => 'INW.CN-SKN CR SA-MCS ADMINISTRASI MEDIKA PT - 014 BANK CENTRAL ASIA ASURANSI BCA LIFE INV 2 KJEPYR181015 PPU.-3BRV-0205 2021021500', 'nilai' => '385000'),
					5 => array('tanggal' => '2021-02-15 00:00:00', 'deskripsi' => 'INW.CN-SKN CR SA-MCS ACA - 046 DBS INV/3/KJE/PYR-161123/II/21 SC 0307OP1008264565 2021021500', 'nilai' => '135000'),
					6 => array('tanggal' => '2021-02-11 00:00:00', 'deskripsi' => 'MCM InhouseTrf CS-CS INV/2/KJE/PYR-2001 31/II/202HCB DARI SOMPO INSURANCE INDONESIA INV/2/KJE/PYR-2001 31/II/202HCB', 'nilai' => '230000'),
					7 => array('tanggal' => '2021-02-10 00:00:00', 'deskripsi' => 'MCM InhouseTrf CS-CS INV/2/KJE/PYR-2006 03/II/2021SC DARI ADMINISTRASI MEDIKA INV/2/KJE/PYR-2006 03/II/2021SC', 'nilai' => '470000'),
					8 => array('tanggal' => '2021-02-10 00:00:00', 'deskripsi' => 'MCM InhouseTrf CS-CS BT21020800096585 DARI ASURANSI JIWA INHEALTH INDONESIA BT21020800096585', 'nilai' => '225000'),
					9 => array('tanggal' => '2021-02-10 00:00:00', 'deskripsi' => 'SA Cash Dep NoBook YOGA HADI NUGROHO 01-02', 'nilai' => '10000000'),
					10 => array('tanggal' => '2021-02-09 00:00:00', 'deskripsi' => 'MCM InhouseTrf CS-CS FWD Claim Non-ASO GAS/2021/00852673 DARI FWD INSURANCE INDONESIA FWD Claim Non-ASO GAS/2021/00852673', 'nilai' => '140000'),
					11 => array('tanggal' => '2021-02-09 00:00:00', 'deskripsi' => 'MCM InhouseTrf CS-CS JASINDO ADMEDIKA JASINDO 2020 DARI ASURANSI JASA INDONESIA (PERSERO) 202102091336635216 202102091543741133', 'nilai' => '1320000'),
					12 => array('tanggal' => '2021-02-05 00:00:00', 'deskripsi' => 'MCM InhouseTrf CS-CS DARI PURI WIDIYANI MARTIADEWI', 'nilai' => '10000000'),
					13 => array('tanggal' => '2021-02-05 00:00:00', 'deskripsi' => 'CME DrCS CrCS (H2H) INV/3/KJE/PYR-2009 17/II/20SC DARI ADMINISTRASI MEDIKA INV/3/KJE/PYR-2009 17/II/20SC', 'nilai' => '110000'),
					14 => array('tanggal' => '2021-02-04 00:00:00', 'deskripsi' => 'INW.CN-SKN CR SA-MCS ASURANSI ETIQA INTERNASI - 014 BANK CENTRAL ASIA 000009 PV E001 02 LGEIT000494-0-SUHE PPU.-13FH-0206 2021020400', 'nilai' => '425000'),
					15 => array('tanggal' => '2021-02-03 00:00:00', 'deskripsi' => 'CME DrCS CrCS (H2H) INV/6/KJE/PYR-2101 11/I/21-HCB DARI ADMINISTRASI MEDIKA INV/6/KJE/PYR-2101 11/I/21-HCB', 'nilai' => '515000'),
				);

		$k++;
		foreach ($adding as $add) {
			$rek_temp[] = [
				'id'                     => $k++,
				'akun_bank_id'           => 'pG1karGazgM',
				'tanggal'                => $add['tanggal'],
				'deskripsi'              => $add['deskripsi'],
				'nilai'                  => $add['nilai'],
				'saldo_akhir'            => 0,
				'debet'                  => 0,
				'created_at'             => Carbon::now()->format('Y-m-d h:i:s'),
				'updated_at'             => Carbon::now()->format('Y-m-d h:i:s'),
				'pembayaran_asuransi_id' => null,
				'old_id'                 => null
			];
		}

		/* dd( $rek_temp ); */
		Rekening::whereNotIn('id', $dont_delete)->delete();
		Rekening::insert($rek_temp);
		foreach ($cont_abaikan_transaksis as $c) {
			AbaikanTransaksi::where('transaksi_id', $c['old_id'])->update([
				'transaksi_id' => $c['new_id']
			]);
		}
	}
	
	/**
	* undocumented function
	*
	* @return void
	*/
	
}
