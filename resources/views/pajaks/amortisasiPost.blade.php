@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Pelaporan Amortisasi

@stop
@section('page-title') 
<h2>Pelaporan Amortisasi</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Pelaporan Amortisasi</strong>
	  </li>
</ol>
@stop
@section('content') 
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="panelLeft">
				<h3>Laporan Penyusutan Tahun Pajak {{ $tahun }}</h3>
			</div>
			<div class="panelRight">
				<a class="btn btn-warning btn-lg" target="_blank" href="{{ url('pdfs/amortisasi/'. $tahun) }}">Cetak PDF</a>
			</div>
		</div>
		<div class="panel-body">
			<div>
			  <!-- Nav tabs -->
			  <ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#peralatan" aria-controls="peralatan" role="tab" data-toggle="tab">Peralatan</a></li>
				<li role="presentation"><a href="#bahan_bangunan" aria-controls="bahan_bangunan" role="tab" data-toggle="tab">Bahan Bangunan</a></li>
			  </ul>

			  <!-- Tab panes -->
			  <div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="peralatan">
					<div class="table-responsive">
					@include('pajaks.formAmortisasi', ['peralatans' => $peralatans])
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="bahan_bangunan">
					<div class="table-responsive">
					@include('pajaks.formAmortisasi', ['peralatans' => $bahan_bangunans])
					</div>
				</div>
			  </div>

			</div>	
		</div>
	</div>
@stop
@section('footer') 

@stop		
