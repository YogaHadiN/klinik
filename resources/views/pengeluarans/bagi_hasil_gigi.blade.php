@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Bagi Hasil Pelayanan Gigi

@stop
@section('page-title') 
<h2>Bagi Hasil Pelayanan Gigi</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Bagi Hasil Pelayanan Gigi</strong>
	  </li>
</ol>

@stop
@section('content') 
	{!! Form::open(['url' => 'pengeluarans/bagi_hasil_gigi', 'method' => 'post']) !!}

	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="panel-title">Bagi Hasil Gigi</div>
				</div>
				<div class="panel-body">
					<h1>Bagi Hasil Gigi</h1>
					<hr>
					<div class="form-group @if($errors->has('sumber_coa_id'))has-error @endif">
					  {!! Form::label('sumber_coa_id', 'Sumber Uang', ['class' => 'control-label']) !!}
					  {!! Form::select('sumber_coa_id' , App\Classes\Yoga::sumberCoaList(), 110004, ['class' => 'form-control']) !!}
					  @if($errors->has('sumber_coa_id'))<code>{{ $errors->first('sumber_coa_id') }}</code>@endif
					</div>
					<div class="form-group @if($errors->has('nilai'))has-error @endif">
					  {!! Form::label('nilai', 'Nilai', ['class' => 'control-label']) !!}
					  {!! Form::text('nilai' , null, ['class' => 'form-control uangInput']) !!}
					  @if($errors->has('nilai'))<code>{{ $errors->first('nilai') }}</code>@endif
					</div>
					<div class="form-group @if($errors->has('bulan'))has-error @endif">
					  {!! Form::label('bulan', 'Bulan Periode', ['class' => 'control-label']) !!}
					  {!! Form::text('bulan' , null, ['class' => 'form-control bulanTahun']) !!}
					  @if($errors->has('bulan'))<code>{{ $errors->first('bulan') }}</code>@endif
					</div>
					<div class="form-group @if($errors->has('petugas_id'))has-error @endif">
					  {!! Form::label('petugas_id', 'Petugas Penginput', ['class' => 'control-label']) !!}
					  {!! Form::select('petugas_id' , App\Classes\Yoga::stafList(), null, ['class' => 'form-control selectpick', 'data-live-search' => 'true']) !!}
					  @if($errors->has('petugas_id'))<code>{{ $errors->first('petugas_id') }}</code>@endif
					</div>
					<div class="form-group @if($errors->has('tanggal_dibayar'))has-error @endif">
					  {!! Form::label('tanggal_dibayar', 'Tanggal Dibayar', ['class' => 'control-label']) !!}
					  {!! Form::text('tanggal_dibayar' , date('d-m-Y'), ['class' => 'form-control tanggal']) !!}
					  @if($errors->has('tanggal_dibayar'))<code>{{ $errors->first('tanggal_dibayar') }}</code>@endif
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<button class="btn btn-success btn-block btn-lg" type="button" onclick="dummySubmit(); return false;">Submit</button>
								<button class="btn btn-success btn-block btn-lg hide" id="submit" type="submit">Submit</button>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<a href="{{ url('laporan_laba_rugis') }}" class="btn btn-danger btn-block btn-lg">Cancel</a>
							</div>
						</div>
					</div>
			  </div>
			</div>
		</div>
	</div>
	<div class="panel panel-success">
		<div class="panel-heading">
			<div class="panel-title">Daftar Pembayaran Bagi Hasil</div>
		</div>
		<div class="panel-body">
			<?php echo $bagi_gigi->appends(Input::except('page'))->links(); ?>
			<div class="table-responsive">
				<table class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>Petugas Penginput</th>
							<th>Bagi Hasil Dibayarkan</th>
							<th>Pph21</th>
							<th>Periode Bulan</th>
							<th>Tanggal Dibayar</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach($bagi_gigi as $gaji)
							<tr>
							<td>{{ $gaji->petugas->nama }}</td>
								<td class="uang">{{ $gaji->nilai }}</td>
								<td class="uang">{{ $gaji->pph21 }}</td>
								<td class="text-center">{{ $gaji->tanggal_mulai->format('M Y') }}</td>
								<td class="text-center">{{ $gaji->tanggal_dibayar->format('d-m-Y') }}</td>
								<td> <a class="btn btn-info btn-xs btn-block" href="{{ url('pdfs/bagi_hasil_gigi/' . $gaji->id) }}" >struk</a> </td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<?php echo $bagi_gigi->appends(Input::except('page'))->links(); ?>
		</div>
	</div>
	
	
	{!! Form::close() !!}
@stop
@section('footer') 
	<script type="text/javascript" charset="utf-8">
		function dummySubmit(){
			 if(validatePass()){
			 	$('#submit').click();
			 }
		}
		
		
	</script>
	
@stop

