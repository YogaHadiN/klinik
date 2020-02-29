@extends('layout.master')

@section('title') 
	Klinik Jati Elok | Rekening Bank {{ $rekenings->first()->akun }}

@stop
@section('page-title') 
<h2>Rekening Bank {{ $rekenings->first()->akun }}</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Rekening Bank {{ $rekenings->first()->akun }}</strong>
	  </li>
</ol>

@stop
@section('content') 
	<div class="table-responsive">
		<table class="table table-hover table-condensed table-bordered">
			<thead>
				<tr>
					<th>Deskripsi</th>
					<th>Debet</th>
					<th>Kredit</th>
					<th>Saldo Akhir</th>
				</tr>
			</thead>
			<tbody>
				@if($rekenings->count() > 0)
					@foreach($rekenings as $rekening)
						<tr>
							<td>{{ $rekening->deskripsi }}</td>
							@if( $rekening->debet )
								<td class="text-right">{{ App\Classes\Yoga::( $rekening->nilai ) }}</td>
							@else
								<td class="text-right">{{ App\Classes\Yoga::('0') }}</td>
							@endif
							@if( !$rekening->debet )
								<td class="text-right">{{ App\Classes\Yoga::( $rekening->nilai ) }}</td>
							@else
								<td class="text-right">{{ App\Classes\Yoga::('0') }}</td>
							@endif
							<td>{{ $rekening->saldo_akhir }}</td>
						</tr>
					@endforeach
				@else
					<tr>
						<td colspan="4" class="text-center">Tidak ada data untuk ditampilkan</td>
					</tr>
				@endif
			</tbody>
		</table>
	</div>
@stop
@section('footer') 
	
@stop

