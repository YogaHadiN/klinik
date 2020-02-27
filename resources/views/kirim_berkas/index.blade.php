@extends('layout.master')

@section('title') 
Klinik Jati Elok | Kirim Berkas

@stop
@section('page-title') 
<h2>Kirim Berkas</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Kirim Berkas</strong>
	  </li>
</ol>

@stop
@section('content') 
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">
				<div class="panelRight">
					<a class="btn btn-success" href="{{ url('kirim_berkas/create') }}">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 
						Create
					</a>
				</div>
			</h3>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<thead>
						<tr>
							<th>Tanggal Kirim</th>
							<th>Staf</th>
							<th>Rekap Tagihan</th>
							<th>Belanja_id</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@if($kirim_berkas->count() > 0)
							@foreach($kirim_berkas as $kirim)
								<tr>
									<td>{{ App\Classes\Yoga::updateDatePrep( $kirim->tanggal ) }}</td>
									<td>
										<div class="table-responsive">
											<table class="table table-hover table-condensed table-bordered">
												<tbody>
													@foreach($kirim->petugas_kirim as $petugas)	
														<tr>
															<td>{{ $petugas->staf->nama }}</td>
															<td>{{ $petugas->role_pengiriman->role_pengiriman }}</td>
														</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</td>
									<td>
										<div class="table-responsive">
											<table class="table table-hover table-condensed table-bordered">
												<tbody>
													@foreach($kirim->rekap_tagihan as $k => $tagihan)	
														<tr>
															<td>{{ $k }}</td>
															<td class="text-right">{{ $tagihan['jumlah_tagihan'] }} Tagihan</td>
															<td class="text-right">{{ App\Classes\Yoga::buatrp( $tagihan['total_tagihan'] ) }}</td>
														</tr>
													@endforeach
												</tbody>
											</table>
										</div>
										
									</td>
									<td>{{ $kirim->pengeluaran_id }}</td>
									<td nowrap class="autofit">

										<a class="btn btn-info btn-sm" href="{{ url('kirim_berkas/' . $kirim->id . '/edit') }}">
											<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
											Edit
										</a>

									</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td colspan="">
									{!! Form::open(['url' => 'kirim_berkas/imports', 'method' => 'post', 'files' => 'true']) !!}
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

