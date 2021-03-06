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

	@if (isset($failures))
	   <div class="alert alert-danger" role="alert">
		  <strong>Errors:</strong>
		  <ul>
			 @foreach ($failures as $failure)
				@foreach ($failure->errors() as $error)
					<li>{{ $error }} pada baris ke {{ $failure->row()   }}</li>
				@endforeach
			 @endforeach
		  </ul>
	   </div>
	@endif
		<div class="row">
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">
						</h3>
					</div>
					<div class="panel-body">
						{!! Form::open([
							'url'    => 'peserta_bpjs_perbulans/editDataPasien',
							"class"  => "m-t",
							"role"   => "form",
							"method" => "post",
							"files"  => "true"
						]) !!}
							<div class="form-group{{ $errors->has('nama_file') ? ' has-error' : '' }}">
								{!! Form::label('nama_file', 'Upload') !!}
								{!! Form::file('nama_file', ['class' => 'rq']) !!}
								{!! $errors->first('nama_file', '<p class="help-block">:message</p>') !!}
							</div>
							<div class="form-group @if($errors->has('bulan')) has-error @endif">
								{!! Form::label('bulan', 'Bulan', ['class' => 'control-label']) !!}
								{!! Form::select('bulan' , App\Classes\Yoga::bulanList(), null, ['class' => 'form-control rq']) !!}
								@if($errors->has('bulan'))<code>{{ $errors->first('bulan') }}</code>@endif
							</div>
							<div class="form-group @if($errors->has('tahun')) has-error @endif">
								{!! Form::label('tahun', 'Tahun', ['class' => 'control-label']) !!}
								{!! Form::select('tahun' , App\Classes\Yoga::tahunList(), null, [
									'class'       => 'form-control rq',
									'placeholder' => '- Pilih -'
								]) !!}
								@if($errors->has('tahun'))<code>{{ $errors->first('tahun') }}</code>@endif
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
					<th>Periode</th>
					<th>Jumlah Denominator HT</th>
					<th>Jumlah Denominator DM</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@if($peserta_bpjs_perbulans->count() > 0)
					@foreach($peserta_bpjs_perbulans as $p)
						<tr>
							<td>{{ $p->id }}</td>
							<td>{{ $p->bulanTahun->format('M Y')}}</td>
							<td>{{ $p->jumlah_ht}}</td>
							<td>{{ $p->jumlah_dm}}</td>
							<td> 
								{!! Form::open(['url' => 'peserta_bpjs_perbulans/' .$p->id, 'method' => 'delete']) !!}
									<a href="{{ url('peserta_bpjs/' . $p->nama_file) }}" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-download" aria-hidden="true"></i> Download</a>
									{!! Form::submit('Delete', [
										'class'   => 'btn btn-danger btn-sm',
										'onclick' => 'return confirm("Anda yakin mau menghapus ' . $p->id . '-' . $p->name.'?");return false;'
									]) !!}
								{!! Form::close() !!}
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
