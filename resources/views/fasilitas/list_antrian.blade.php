@extends('layout.master')

@section('title') 
Klinik Jati Elok | List Antrian

@stop
@section('page-title') 
<h2>List Antrian</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>List Antrian</strong>
	  </li>
</ol>

@stop
@section('content') 
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<div class="panelLeft">
					List Antrian
				</div>
				<div id="Antrian Terakhir" class="panelRight">
				</div>
			</h3>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<thead>
						<tr>
							<th>Id</th>
							<th>Tanggal</th>
							<th>Nomor Antrian</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@if($antrians->count() > 0)
							@foreach($antrians as $antrian)
								<tr>
									<td>{{ $antrian->id }}</td>
									<td>{{ $antrian->created_at->format('d M y') }}</td>
									<td>{{ $antrian->jenis_antrian->prefix }}{{ $antrian->nomor }}</td>
									<td nowrap class="autofit">
										{!! Form::open(['url' => 'antrians/' . $antrian->id, 'method' => 'delete']) !!}
											<a class="btn btn-info btn-sm" href="{{ url('antrians/proses/' . $antrian->id) }}">
												<span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Proses
											</a>
											<button class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus {{ $antrian->nomor_antrian }} ?')" type="submit"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete</button>
											<a target="_blank" class="btn btn-success btn-sm" href="{{ url('pdfs/antrian/' . $antrian->id) }}">
												<i class="fas fa-print"></i> Print Antrian
											</a>
										{!! Form::close() !!}
									</td>

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
		</div>
	</div>
@stop
@section('footer') 
	
@stop
