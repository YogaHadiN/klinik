<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Moota;
use Log;
use App\Rekening;
use App\AkunBank;
use App\Asuransi;
use App\Http\Controllers\PendapatansController;

class cekMutasi extends Command
{

	private $input_dibayar;
	private $input_mulai;
	private $input_staf_id;
	private $input_akhir;
	private $input_tanggal_dibayar;
	private $input_asuransi_id;
	private $input_temp;
	private $input_coa_id;
	private $input_catatan_container;
	private $input_rekening_id;
	private $input_invoice_id;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cek:mutasi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		//define semua bank yang ada
		Log::info('==================================================================================================================================');
		Log::info('Cek Mutasi Dilakukan');
		Log::info('==================================================================================================================================');

		$banks = Moota::banks();
		$kata_kuncis = $this->kata_kuncis();
		foreach ($banks['data'] as $bank) {
			$bank_id = $bank->bank_id;
			$newBank = AkunBank::findOrNew($bank_id);
			if ( !$newBank->id ) {
				$newBank->id             = $bank_id;
				$newBank->nomor_rekening = $bank->account_number;
				$newBank->akun           = $bank->bank_type;
				$newBank->save();
			}
			$mutasis = Moota::mutation( $newBank->id )->month()->toArray();
			$insertMutasi = [];
			foreach ($mutasis['data'] as $mutasi) {
				if ( $mutasi->type == 'CR' ) {
					$debet = 0;
				} else {
					$debet = 1;
				}
				$newRekening = Rekening::findOrNew($mutasi->mutation_id);
				/* if ( !$newRekening->id ) { */
					$insertMutasi[] = [
						'id'           => $mutasi->mutation_id,
						'akun_bank_id' => $newBank->id,
						'tanggal'      => $mutasi->created_at,
						'deskripsi'    => $mutasi->description,
						'nilai'        => $mutasi->amount,
						'saldo_akhir'  => $mutasi->balance,
						'debet'        => $debet
					];
					$mutasi->description = 'dfasdfasdfa etiqa ajsdlfja;';
					$mutasi->amount      =  13240000;

					$this->input_dibayar           = $mutasi->amount;
					$this->input_staf_id           = 16;
					$this->input_tanggal_dibayar   = $mutasi->created_at;
					$this->input_coa_id            = 110001;
					$this->input_rekening_id       = $mutasi->mutation_id;
					if (!$debet) {
						$this->checkIfMatchKeyWord($kata_kuncis, $mutasi->description, $mutasi->amount);
					}
				/* } */
			}
			dd('this');
			Rekening::insert($insertMutasi);
		}
		Log::info('==================================================================================================================================');
		Log::info('Cek Mutasi Selesai');
		Log::info('==================================================================================================================================');
    }
	public function kata_kuncis(){
		$asuransis = Asuransi::whereNotNull('kata_kunci')->get();

		$kata_kuncis = [];
		foreach ($asuransis as $asu) {
			if (!empty($asu->kata_kunci)) {
				$kata_kuncis[] = [
					'asuransi_id' => $asu->id,
					'kata_kunci'  => $asu->kata_kunci
				];
			}
		}
		return $kata_kuncis;
	}
	private function checkIfMatchKeyWord($kata_kuncis, $description, $nilai){
		$pendapatan = new PendapatansController;
		foreach ($kata_kuncis as $kk) {
			$kata_kunci = $kk['kata_kunci'];
			$asuransi_id = $kk['asuransi_id'];
			if (strpos($description, $kata_kunci )) {
				$invoices = $pendapatan->invoicesQuery($asuransi_id, $nilai);
				if (count($invoices)) {
					/* dd('ivoice', $invoices[0]); */
					$inv_id = $invoices[0]->invoice_id;

					$inv = Invoice::with('piutang_asuransi.periksa')->where('id',$inv_id)->first();

					$this->input_asuransi_id       = $kk['asuransi_id'];
					$this->input_invoice_id        = $inv_id;
					$this->input_mulai             = $inv->tanggal_mulai;
					$this->input_temp              = $this->tempInput($inv);
					$this->input_akhir             = $inv->tanggal_akhir;
					$this->input_catatan_container = [];
					$pendapatan->inputData();
				}
			}
		}

	}
	private function tempInput($inv){
		$piutang_asuransis = $inv->piutang_asuransi;
		$data = [];
		foreach ($piutang_asuransis as $pa) {
			$data[] = [
				'piutang_id'       => $pa->id,
				'periksa_id'       => $pa->periksa_id,
				'pasien_id'        => $pa->periksa->pasien_id,
				'nama_pasien'      => $pa->periksa->pasien->nama,
				'nama_pasien'      => $pa->periksa->tunai,
				'nama_pasien'      => $pa->periksa->piutang,
				'pembayaran'       => $pa->sudah_dibayar,
				'total_pembayaran' => null,
				'akan_dibayar'     => $pa->piutang - $pa->sudah_dibayar,
				'tanggal'          => $pa->periksa->tanggal,
				'sudah_dibayar'    => $pa->sudah_dibayar
			];
		}
		return json_encode($data);
	}
}
