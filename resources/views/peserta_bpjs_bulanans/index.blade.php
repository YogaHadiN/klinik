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
		<div class="row">
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">
						</h3>
					</div>
					<div class="panel-body">
						{!! Form::open([
							'url'    => 'peserta_bpjs_perbulans',
							"class"  => "m-t",
							"role"   => "form",
							"method" => "post",
							"files"  => "true"
						]) !!}
							<div class="form-group{{ $errors->has('nama_file') ? ' has-error' : '' }}">
								{!! Form::label('nama_file', 'Upload') !!}
								{!! Form::file('nama_file') !!}
								{!! $errors->first('nama_file', '<p class="help-block">:message</p>') !!}
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<button class="btn btn-success btn-lg btn-block" type="button" onclick='dummySubmit(this);return false;'>Submit</button>
									{!! Form::submit('Submit', ['class' => 'btn btn-success hide', 'id' => 'submit']) !!}
								</div>
							</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
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
								Action
							</td>
						</tr>
					@endforeach
				@else
					<tr>
						<td colspan="4" class="text-center">Data tidak ditemukan</td>
					</tr>
				@endif
			</tbody>
		</table>
	</div>
@stop
@section('footer') 
	<script type="text/javascript" charset="utf-8">
		function dummySubmit(control){
			if(validatePass2(control)){
				$('#submit').click();
			}
		}
	</script>
@stop
