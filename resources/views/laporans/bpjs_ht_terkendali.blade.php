@extends('layout.master')

@section('title') 
    Klinik Jati Elok | Laporan Prolanis HT Terkendali {{ $bulanThn->format('Y-m') }}

@stop
@section('page-title') 
    <h2>Laporan Prolanis HT Terkendali {{ $bulanThn->format('Y-m') }}</h2>
<ol class="breadcrumb">
            <li>
                <a href="{{ url('laporans')}}">Home</a>
            </li>
            <li class="active">
                <strong>Laporan Prolanis HT Terkendali {{ $bulanThn }}</strong>
            </li>
</ol>

@stop
@section('content') 
    @include('pasiens.prolanis_perbulan_template', ['prolanis' => 'pasien_ht_terkendali', 'bukan_pdf' => true])
@stop
@section('footer') 
    
@stop
