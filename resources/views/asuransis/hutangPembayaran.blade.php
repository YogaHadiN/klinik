@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Riwayat Hutang dan Pembayaran

@stop
@section('page-title') 
<h2>Riwayat Hutang dan Pembayaran</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Riwayat Hutang dan Pembayaran</strong>
	  </li>
</ol>
@stop
@section('content') 
	@include('asuransis.templateHutangPembayaran')
@stop
@section('footer') 

@stop
