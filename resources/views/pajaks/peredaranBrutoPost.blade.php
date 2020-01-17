@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Peredaran Bruto

@stop
@section('page-title') 
<h2>Peredaran Bruto</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Peredaran Bruto</strong>
	  </li>
</ol>
@stop
@section('content') 
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="panel-title">
				<div class="panelLeft">
					<h3>Laporan Peredaran Bruto Tahun {{ $tahun }}</h3>
				</div>
				<div class="panelRight">
					<a class="btn btn-warning btn-lg" href="{{ url('pdfs/peredaranBruto/' . $tahun) }}">Cetak Pdf</a>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<thead>
						<tr>
							<th>Bulan</th>
							<th>Peredaran Bruto</th>
						</tr>
					</thead>
					<tbody>
						@if(count($peredaranBruto) > 0)
							@foreach( $peredaranBruto as $b )
								<tr>
									<td>{{ $b->bulan }}</td>
									<td class="text-right">{{App\Classes\Yoga::buatrp(  $b->total  )}}</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td class="text-center" colspan="">Tidak ada data untuk ditampilkan</td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>
			
		</div>
	</div>
@stop
@section('footer') 

@stop
