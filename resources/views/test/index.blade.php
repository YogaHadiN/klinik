@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Test

@stop
@section('page-title') 
<h2></h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Test</strong>
	  </li>
</ol>
@stop

@section('content') 
	tab
@stop
@section('footer') 

@stop
