<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Periksa;
use App\Classes\Yoga;
use App\PesertaBpjsPerbulan;
use App\Http\Controllers\LaporansController;
use App\Http\Controllers\PdfsController;

class CekDanMasukkanProlanis
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $pdf                  = new PdfsController;
        $periksa_id           = $request->id;
        $periksa              = Periksa::find( $periksa_id );
        $pasien               = $periksa->pasien;
        /* dd( $pdf->htTerkendali($periksa) ); */
        $ht_terkendali_persen = $this->cariPersentaseHtTerkendali($periksa);

        if (
            $ht_terkendali_persen < 13 &&//jika rppt belum mencapai 13 %
            $pasien->prolanis_ht &&//jika pasien merupakan pasien prolanis_ht
            $periksa->asuransi_id == '32' &&//jika pemeriksaan menggunakan pembayaran BPJS
            $pdf->htTerkendali($periksa) &&//jika pasien masuk kategori tekanan darah terkendali
            !is_null($pasien->prolanis_ht_flagging_image)//jika pasien belum diflagging prolanis hipertensi
        ) {
            $pesan = Yoga::gagalFlash('Pasien ini harus diflagging sebagai Pasien Prolanis BPJS, harap upload bukti bahwa pasien sudah didaftarkan sebagai pasien prolanis BPJS');
            return redirect('pasiens/' . $pasien->id . '/edit')->withPesan($pesan);
        }
        return $next($request);
    }
    /**
     * undocumented function
     *
     * @return void
     */
    private function cariPersentaseHtTerkendali($periksa)
    {
        $bulanTahunPeriksa    = Carbon::parse($periksa->tanggal)->format('Y-m');
        $lap                  = new LaporansController;
		$rppt                 = $lap->cariJumlahProlanis(date('Y-m'));
		$jumlah_prolanis_ht   = $rppt['jumlah_prolanis_ht'];
		$status_ht            = $lap->cariStatusHt(date('Y-m'), $jumlah_prolanis_ht);
		return $status_ht['ht_terkendali_persen'];
    }
    
}
