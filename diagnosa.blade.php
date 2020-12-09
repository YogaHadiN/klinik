@extends('layout.master')

@section('title') 
Klinik Jati Elok | Laporan Diagnosa BPJS bulan {{ $bulan }}

@stop
@section('page-title') 
<h2>Laporan Diagnosa BPJS bulan {{ $bulan }}</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Laporan Diagnosa BPJS bulan {{ $bulan }}</strong>
	  </li>
</ol>
@stop
@section('content') 
		<div class="table-responsive">
			<table class="table table-hover table-condensed table-bordered">
				<thead>
					<tr>
						<th>Tanggal</th>
						<th>Nama</th>
						<th>Nomor BPJS</th>
						<th>Diagnosa ICD</th>
					</tr>
				</thead>
				<tbody>
					@if($periksas->count() > 0)
						@foreach($periksas as $periksa)
							<tr>
								<td>{{ $periksa->tanggal }}</td>
								<td>{{ $periksa->pasien->nama }}</td>
								<td>{{ $periksa->pasien->nomor_asuransi_bpjs }}</td>
								<td>{{ $periksa->diagnosa->icd10->id }} - {{ $periksa->diagnosa->icd10->diagnosaICD }} </td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="4" class="text-center"> Tidak ada data untuk ditampilkan </td>
						</tr>
					@endif
				</tbody>
			</table>
		</div>
		
@stop
@section('footer') 
	
@stop
