@extends('layout.master')

@section('title') 
Klinik Jati Elok | Tunggakan Asuransi {{ $year }}

@stop
@section('page-title') 
<h2>Tunggakan Asuransi {{ $year }}</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Tunggakan Asuransi {{ $year }}</strong>
	  </li>
</ol>

@stop
@section('content') 
		<div class="table-responsive">
			<table class="table table-hover table-condensed table-bordered">
				<thead>
					<tr>
						<th>Nama Asuransi</th>
						<th>Tunai</th>
						<th>Piutang</th>
						<th>Sudah Dibayar</th>
						<th>Sisa Dibayar</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($data) > 0)
						@foreach($data as $d)
							<tr>
								<td>{{ $d->nama }}</td>
								<td class="uang">{{ $d->tunai }}</td>
								<td class="uang">{{ $d->piutang }}</td>
								<td class="uang">{{ $d->sudah_dibayar }}</td>
								<td class="uang">{{ $d->sisa_piutang }}</td>
								<td nowrap class="autofit">
									<a class="btn btn-info" href="{{ url('asuransis/' . $d->asuransi_id .'/hutang/pembayaran') }}" target="_blank">Show</a>
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="6" class="text-center">Tidak ada data untuk ditampilkan</td>
						</tr>
					@endif
				</tbody>
				<tfoot>
					<tr>
						<th>TOTAL</th>
						<th class="uang">{{ $total_tunai }}</th>
						<th class="uang">{{ $total_piutang }}</th>
						<th class="uang">{{ $total_sudah_dibayar }}</th>
						<th class="uang">{{ $total_sisa_piutang }}</th>
					</tr>
				</tfoot>
			</table>
		</div>
		
	
@stop
@section('footer') 
	
@stop
