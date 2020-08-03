<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Moota;
use Log;
use App\Rekening;
use App\AkunBank;

class cekMutasi19Terakhir extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cek:mutasi20terakhir';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek 20 transaksi terakhir';

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
			$mutasis = Moota::mutation( $newBank->id )->latest(19)->toArray();
			$insertMutasi = [];
			foreach ($mutasis as $mutasi) {
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
				}
			}
			Rekening::insert($insertMutasi);
		}
		Log::info('==================================================================================================================================');
		Log::info('Cek Mutasi Selesai');
		Log::info('==================================================================================================================================');
    }
}
