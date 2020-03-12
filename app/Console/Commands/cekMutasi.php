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
				if ( !$newRekening->id ) {
					$insertMutasi[] = [
						'id'           => $mutasi->mutation_id,
						'akun_bank_id' => $newBank->id,
						'tanggal'      => $mutasi->created_at,
						'deskripsi'    => $mutasi->description,
						'nilai'        => $mutasi->amount,
						'saldo_akhir'  => $mutasi->balance,
						'debet'        => $debet
					];
					if (!$debet) {
						$this->checkIfMatchKeyWord($kata_kuncis, $mutasi->description, $mutasi->amount);
					}
				}
			}
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
			$kata_kuncis[] = [
				'asuransi_id' => $asu->id,
				'kata_kunci'  => $asu->kata_kunci
			];
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
				if ($invoices->count()) {
					//Lakukan Pembayaran update semua rekening id dan semua piutang asuransi terkait
				}
			}
		}

	}
}
