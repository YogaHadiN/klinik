@extends('layout.master')

@section('title') 
Klinik Jati Elok | Peserta BPJS bulanan

@stop
@section('page-title') 
<h2>Peserta BPJS bulanan</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Peserta BPJS bulanan</strong>
	  </li>
</ol>

@stop
@section('content') 
	
		<div class="table-responsive">
			<table class="table table-hover table-condensed table-bordered">
				<thead>
					<tr>
						<th>ID</th>
						<th>Tanggal</th>
						<th>Nama File</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@if($peserta_bpjs_bulanans->count() > 0)
						@foreach($peserta_bpjs_bulanans as $p)
							<tr>
								<td>{{ $p->id }}</td>
								<td>{{ $p->created_at->format('d M Y')}}</td>
								<td>{{ $p->nama_file }}</td>
								<td nowrap class="autofit">
									{!! Form::open(['url' => 'model/' . $->id, 'method' => 'delete']) !!}
										<a class="btn btn-warning btn-sm" href="{{ url('model/' . $->id . '/edit') }}"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Edit</a>
										<button class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus {{ ->id }} - {{ -> }} ?')" type="submit"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete</button>
									{!! Form::close() !!}
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="">
								{!! Form::open(['url' => 'model/imports', 'method' => 'post', 'files' => 'true']) !!}
									<div class="form-group">
										{!! Form::label('file', 'Data tidak ditemukan, upload data?') !!}
										{!! Form::file('file') !!}
										{!! Form::submit('Upload', ['class' => 'btn btn-primary', 'id' => 'submit']) !!}
									</div>
								{!! Form::close() !!}
							</td>
						</tr>
					@endif
				</tbody>
			</table>
		</div>
		

@stop
@section('footer') 
	
@stop
