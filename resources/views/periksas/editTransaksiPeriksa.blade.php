@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Edit Transaksi Periksa

@stop
@section('page-title') 
<h2>Edit Transaksi Periksa</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Edit Transaksi Periksa</strong>
	  </li>
</ol>
@stop
@section('content') 
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="panel panel-info">
							<div class="panel-heading">
								<div class="panel-title">
									<div class="panelLeft">
										Edit Transaksi Periksa
									</div>
								</div>
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-hover table-condensed">
										<thead>
											<tr>
												<th class="hide id">id</th>
												<th>Jenis Tarif</th>
												<th>Biaya</th>
											</tr>
										</thead>
										<tbody>
											@if($periksa->transaksii->count() > 0)
												@foreach($periksa->transaksii as $k => $t)
													<tr>
														<td class='hide id'>{{ $t->id }}</td>
														<td class='jenis_tarif'>{{ $t->jenisTarif->jenis_tarif }}</td>
														<td>
														   {!! Form::text('nilai', $t->biaya, [
															   'class' => 'form-control uangInput text-right',
															   'title' => $k,
															   'onkeyup' => 'transaksiPeriksa(this);return false;'
														   ]) !!}
													   </td>
													</tr>
												@endforeach
											@else
												<tr>
													<td class="text-center" colspan="4">Tidak Ada Data Untuk Ditampilkan :p</td>
												</tr>
											@endif
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<div class="panelLeft">
									<div class="panel-title">Informasi</div>
								</div>
								<div class="panelRight">
								</div>
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-hover table-condensed table-bordered">
										<tbody>
											<tr>
												<td>Periksa Id</td>
												<td>{{ $periksa->id }}</td>
											</tr>
											<tr>
												<td>Nama Pasien</td>
												<td>{{ $periksa->pasien_id }}-{{ $periksa->pasien->nama }}</td>
											</tr>
											<tr>
												<td>Pembayaran</td>
												<td>{{ $periksa->asuransi->nama }}</td>
											</tr>
											<tr>
												<td>Nama Dokter</td>
												<td>{{ $periksa->staf->nama }}</td>
											</tr>
											<tr>
												<td>Tanggal</td>
											</tr>
											<tr>
												<td>Jam</td>
												<td>{{ $periksa->created_at->format('H:i:s') }}</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<div class="panel-title">
							<div class="panelLeft">
								Edit Tunai / Piutang
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-hover table-condensed">
								<thead>
									<tr>
										<th>Pembayaran</th>
										<th>Nilai</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td> Tunai </td>
										<td id="tunai">
										   {!! Form::text('tunai', $periksa->tunai, [
											   'class' => 'form-control uangInput text-right tunai',
											   'onkeyup' => 'periksaKeyUp(this, "tunai");return false;'
										   ]) !!}
									   </td>
									</tr>
									<tr>
										<td>Piutang</td>
										<td id="piutang">
										   {!! Form::text('piutang', $periksa->piutang, [
											   'class' => 'form-control uangInput text-right piutang',
											   'onkeyup' => 'periksaKeyUp(this, "piutang");return false;'
										   ]) !!}
									   </td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="alert alert-danger">
						<h2>Peringatan</h2>
						<p>Halaman ini hanya untuk Super Admin atau Manager</p>
						<p>Jika Anda bukan termasuk diantara keduanya Harap segera logout</p>
					</div>
					<div class="alert alert-info">
						<h2>Petunjuk</h2>
						<ul>
							<li>Jumlah nilai pada kolom debit dan kolom kredit pada <strong>Jurnal Umum</strong>  harus sama (seimbang)</li>
							<li>Jumlah nilai seluruh baris pada <strong>Transaksi Periksa</strong> dan <strong>Edit Tunai Piutang</strong> harus sama</li>
							<li>Jumlah tersebut harus sama dengan <strong>jumlah harta</strong> yang diterima pada transaksi ini</li>
							<li>Harta yang dimaksud pada halaman ini adalah
								<ul>
									@foreach( $periksa->jurnals as $j )
										@if( substr( $j->coa_id, 0, 2 ) == '11' && $j->debit == 1  )
											<li>{{ $j->coa->coa }}</li>
										@endif
									@endforeach
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="panelLeft">
					Jurnal Umum
				</div>
			</div>
			<div class="panel-body">
				@include('jurnal_umums.templateJurnal', [
					'jurnals' => $periksa->jurnals,
					'delete' => true,
					'count' => $periksa->jurnals->count()
				])
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
		@include('jurnal_umums.formManualInput')
	</div>
</div>

{!! Form::open(['url' => 'periksas/' . $periksa->id . '/update/transaksiPeriksa', 'method' => 'post']) !!}

	<div class="row hide">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group @if($errors->has('nomor_asuransi'))has-error @endif">
			  {!! Form::label('nomor_asuransi', 'Nomor Asuransi', ['class' => 'control-label']) !!}
			  {!! Form::text('nomor_asuransi', $periksa->nomor_asuransi, array(
					'class'         => 'form-control rq'
				))!!}
			  @if($errors->has('nomor_asuransi'))<code>{{ $errors->first('nomor_asuransi') }}</code>@endif
			</div>
		</div>
	</div>
	<div>
	<div class="hide">
		<h2>Temp</h2>
	</div>
	<div class="hide">
		{!! Form::textarea('temp', '[]', ['class' => 'form-control textareacustom', 'id' => 'temp']) !!}
	</div>
	<div class="hide">
		<h2>Jurnals</h2>
		{!! Form::textarea('jurnals', $periksa->jurnals, ['class' => 'form-control textareacustom', 'id' => 'jurnals']) !!}
	</div>
	<div>
	<div class="hide">
		<h2>Transaksis</h2>
		{!! Form::textarea('transaksis', $periksa->transaksii, ['class' => 'form-control textareacustom', 'id' => 'transaksis']) !!}
	</div>
	<div>
	<div class="hide">
		<h2>Periksa</h2>
		{!! Form::textarea('periksa', $periksa, ['class' => 'form-control textareacustom', 'id' => 'periksa']) !!}
	</div>
	<div class="row">
		<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<button class="btn btn-lg btn-block btn-success" type="button" onclick='dummySubmit(this);return false;'>Submit</button>
					{!! Form::submit('Submit', ['class' => 'btn btn-block btn-success hide', 'id' => 'submit']) !!}
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<a class="btn btn-lg btn-danger btn-block" href="{{ url('laporans') }}">Cancel</a>
				</div>
			</div>
		</div>
	</div>
{!! Form::close() !!}
@stop

@section('footer') 
<script src="{!! asset('js/jurnalManual.js') !!}"></script>
<script src="{!! asset('js/transaksiPeriksaEdit.js') !!}"></script>
@stop
	
