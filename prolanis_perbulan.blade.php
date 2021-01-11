@extends('layout.master')

@section('title') 
Klinik Jati Elok | Prolanis Bulan {{ date('d M Y') }}

@stop
@section('page-title') 
<h2>Prolanis Bulan {{ date('d M Y') }}</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Prolanis Bulan {{ date('d M Y') }}</strong>
	  </li>
</ol>

@stop
@section('content') 
		<div class="table-responsive">
			<table class="table table-hover table-condensed table-bordered">
				<thead>
					<tr>
						<th>Nama</th>
						<th>Tanggal</th>
						<th>Tekanan Darah</th>
						<th>Pemeriksaan</th>
					</tr>
				</thead>
				<tbody>
					@if(count($periksas) > 0)
						@foreach($periksas as $p)
							<tr>
								<td>{{ $p['tanggal'] }}</td>
								<td>{{ $p['nama'] }}</td>
								<td>{{ $p['sistolik'] }} / {{ $p['diastolik'] }} mmHg</td>
								<td>
									@if( isset( $p['transaksi_periksas'] ) )
										<ul>
										@foreach ($p['transaksi_periksas'] as $t)
											<li> {{ $t['jenis_tarif'] }} : {{ $t['keterangan_pemeriksaan'] }} </li>
										@endforeach
										</ul>
									@endif
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="4" class="text-center">Tidak ada data </td>
						</tr>
					@endif
				</tbody>
			</table>
		</div>
		
@stop
@section('footer') 
	
@stop
