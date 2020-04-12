<?php

namespace App\Http\Middleware;

use Closure;

class adminOnly
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
		if (
			!( Auth::user()->role == '4' || Auth::user()->role == '6')
		) {
			 $pesan = Yoga::gagalFlash( 'Anda tidak diizinkan melakukan operasi ini');
			 return redirect()->back()->withPesan($pesan);
        } 
        return $next($request);
    }
}
