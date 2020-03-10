@extends('layout.master')

@section('title') 
Klinik Jati Elok | Piutang Asuransi

@stop
@section('page-title') 
<h2>Piutang Asuransi</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Piutang Asuransi</strong>
	  </li>
</ol>

@stop
@section('content') 
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				Piutang Asuransi
			</h3>
		</div>
		<div class="panel-body">

			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<thead>
						<tr>
							<th>Nama Pasien</th>
							<th>Piutang</th>
							<th>Nomor Asuransi</th>
							<th>Piutang</th>
						</tr>
					</thead>
					<tbody>
						@if($piutangs->count() > 0)
							@foreach($piutangs as $piutang)
								<tr>
									<td>{{ $piutang->periksa->pasien->nama }}</td>
									<td>{{ $piutang->periksa->asuransi->nama }}</td>
									<td>{{ $piutang->periksa->nomor_asuransi }}</td>
									<td>{{ App\Classes\Yoga::buatrp( $piutang->piutang - $piutang->sudah_dibayar ) }}</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td colspan="4">
									{!! Form::open(['url' => 'piutangs/imports', 'method' => 'post', 'files' => 'true']) !!}
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
		</div>
	</div>
@stop
@section('footer') 
	
@stop

