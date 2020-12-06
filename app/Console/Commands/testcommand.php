<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Outbox;
use App\User;
use App\Pengeluaran;
use App\Woowa;
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
use App\Tarif;
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
		$this->testAsuransi();
		/* $this->resetPembayaranAsuransis(); */

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
			'937',
			'940',
			'945',
			'978',
			'979',
			'980',
			'981',
			'982',
			'995',
			'1004',
			'1019',
			'1048',
			'1049',
			'1054',
			'1055',
			'1066',
			'1082',
			'1083',
			'1087',
			'1100',
			'1110',
			'1114',
			'1122',
			'1125',
			'1126',
			'1127',
			'1128'
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
	private function resetPembayaranAsuransi($pembayaran_asuransi_id)
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
	
	
}
