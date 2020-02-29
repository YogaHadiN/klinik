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
		{{ $rekenings->links() }}
		<table class="table table-hover table-condensed table-bordered">
			<thead>
				<tr>
					<th>
						Tanggal
                        {!! Form::text('tanggal', null, ['class' => 'form-control-inline tgl form-control ajaxsearchrekening', 'id' => 'tanggal'])!!}
					</th>
					<th>
						Deskripsi
                        {!! Form::text('deskripsi', null, ['class' => 'form-control-inline deskripsi form-control ajaxsearchrekening', 'id' => 'deskripsi'])!!}
					</th>
					<th>
						Kredit
					</th>
					<th>
						Saldo
					</th>
				</tr>
			</thead>
			<tbody>
				@if($rekenings->count() > 0)
					@foreach($rekenings as $rekening)
						<tr>
							<td nowrap>{{ $rekening->tanggal->format('Y-m-d') }}</td>
							<td>{{ $rekening->deskripsi }}</td>
							@if( !$rekening->debet )
								<td nowrap class="text-right">{{ App\Classes\Yoga::buatrp( $rekening->nilai ) }}</td>
							@else
								<td nowrap class="text-right">{{ App\Classes\Yoga::buatrp('0') }}</td>
							@endif
							<td nowrap>{{ App\Classes\Yoga::buatrp( $rekening->saldo_akhir ) }}</td>
						</tr>
					@endforeach
				@else
					<tr>
						<td colspan="4" class="text-center">Tidak ada data untuk ditampilkan</td>
					</tr>
				@endif
			</tbody>
		</table>
		{{ $rekenings->links() }}
	</div>
@stop
@section('footer') 
	
@stop

