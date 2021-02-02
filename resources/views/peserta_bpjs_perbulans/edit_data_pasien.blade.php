@extends('layout.master')

@section('title') 
Klinik Jati Elok | Edit Data Pasien RPPT

@stop
@section('page-title') 
<h2>Edit Data Pasien RPPT</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Edit Data Pasien RPPT</strong>
	  </li>
</ol>

@stop
@section('content') 
	<h2>Terdapat {{ count($ht) + count($dm) }} Data yang harus dikoreksi</h2>
	<a href="#" class="float">
		<i class="fa fa-2x fa-object-group my-float"></i>
	</a>
	{!! Form::text('bulanTahun', $bulanTahun, ['class' => 'form-control hide' , 'id' => 'bulanTahun']) !!}
	@include('peserta_bpjs_perbulans.form')
	@include('peserta_bpjs_perbulans.form', ['ht' => $dm])
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<a href="{{ url('peserta_bpjs_perbulans') }}" class="btn btn-primary btn-lg btn-block">Selesai</a>
			</div>
		</div>
@stop
@section('footer') 

{!! HTML::script('js/peserta_bpjs_perbulans_edit_data.js')!!}
	
@stop
