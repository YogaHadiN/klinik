@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Pesan Keluar

@stop
@section('page-title') 
<h2>Pesan Keluar</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Pesan Keluar</strong>
	  </li>
</ol>

@stop
@section('content') 
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="panel-title">Update pada {{ date('d-m-Y H:i:s') }}</div>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>Message</th>
							<th nowrap>Nomor Tujuan</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@if($outbox->count() > 0)
							@foreach($outbox as $o)
								<tr>
									<td>{{ $i->pesan }}</td>
									<td>{{ $i->periksa->pasien->no_telp }}</td>
									<td nowrap> <a class="btn btn-xs btn-primary" href="{{ url("") }}">delete</a> </td>
								</tr>
							@endforeach
						@else
							<tr>
								<td class="text-center" colspan="3">Tidak Ada Data Untuk Ditampilkan :p</td>
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
