<?php

namespace App\Http\Middleware;

use Closure;
use App\Periksa;
use App\Classes\Yoga;

class BelumMasukKasir
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
		$periksa_id =  $request->route()->parameter('id') ;
		try {
			$periksa = Periksa::findOrFail($periksa_id);
			
		} catch (\Exception $e) {
			$pesan = Yoga::gagalFlash('Pasien tidak ditemukan');
			return redirect()->back()->withPesan($pesan);
		}
		if (
			$periksa->lewat_poli == 0
		) {
			$pesan = Yoga::gagalFlash('Pasien sudah ada di antrian periksa, tidak perlu dikembalikan lagi');
			return redirect()->back()->withPesan($pesan);
		}
		if (
			$periksa->lewat_kasir == 1
		) {
			$pesan = Yoga::gagalFlash('Pasien sudah dicetak statusnya, hubungi Petugas apabila ingin merubah status pasien ini');
			return redirect()->back()->withPesan($pesan);
		}
        return $next($request);
    }
}
