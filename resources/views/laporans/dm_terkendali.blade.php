@extends('layout.master')

@section('title') 
Klinik Jati Elok | Laporan DM Terkendali

@stop
@section('page-title') 
<h2>Laporan DM Terkendali</h2>
<ol class="breadcrumb">
            <li>
                <a href="{{ url('laporans')}}">Home</a>
            </li>
            <li class="active">
                <strong>Laporan DM Terkendali</strong>
            </li>
</ol>

@stop
@section('content') 
    <h2>{{ count($dm) }} Pasien Prolanis DM Berobat Bulan Ini</h2>
<div class="table-responsive">
    <table class="table table-hover table-condensed table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Tanggal Lahir</th>
                <th>Alamat</th>
                <th>Gula Darah</th>
            </tr>
        </thead>
        <tbody>
            @if(count($dm) > 0)
                @foreach($dm as $k => $d)
                    <tr>
                        <td>{{ $k + 1 }}</td>
                        <td>{{ $d->tanggal }}</td>
                        <td>{{ $d->nama }}</td>
                        <td>{{ $d->tanggal_lahir }}</td>
                        <td>{{ $d->alamat }}</td>
                        <td>{{ $d->keterangan_pemeriksaan }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@stop
@section('footer') 
    
@stop
