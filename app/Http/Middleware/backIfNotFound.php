<?php

namespace App\Http\Middleware;

use Closure;

class backIfNotFound
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
		try {
			$request->jenis_antrian = JenisAntrian::findOrFail($jenis_antrian_id);
			return $next($request);
		} catch (\Exception $e) {
			$pesan = Yoga::gagalFlash('Ruang Periksa tidak ditemukan');
			return redirect()->back()->withPesan($pesan);
		}
    }
}
