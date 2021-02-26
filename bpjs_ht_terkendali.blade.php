@extends('layout.master')

@section('title') 
Klinik Jati Elok | Laporan Prolanis Hipertensi Terkendali Bulan {{$bulanThn}}

@stop
@section('page-title') 
<h2>Laporan Prolanis Hipertensi Terkendali Bulan {{$bulanThn}}</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Laporan Prolanis Hipertensi Terkendali Bulan {{$bulanThn}}</strong>
	  </li>
</ol>

@stop
@section('content') 
	

@stop
@section('footer') 
	
@stop
