@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Laporan Neraca

@stop
@section('page-title') 
 <h2>List Laporan Neraca</h2>
 <ol class="breadcrumb">
      <li>
          <a href="{!! url('laporans')!!}">Home</a>
      </li>
      <li class="active">
          <strong>List Laporan Neraca</strong>
      </li>
</ol>
@stop
@section('content') 
{!! Form::open(['url' => 'laporan_neracas/show', 'method' => 'get']) !!}
<div class="panel panel-default">
  <div class="panel-body">
    <h1>Pilih Laporan Neraca</h1>
    <hr>
    <div class="row">
      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		  <div class="form-group @if($errors->has('tanggal'))has-error @endif">
		    {!! Form::label('tanggal', 'Tanggal', ['class' => 'control-label tanggal']) !!}
            {!! Form::text('tanggal', date('d-m-Y'), ['class' => 'form-control rq']) !!}
		    @if($errors->has('tanggal'))<code>{{ $errors->first('tanggal') }}</code>@endif
		  </div>
        <div class="form-group">
          <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
              <button class="btn btn-success btn-block btn-lg" type="button" onclick="dummySubmit(); return false;">Submit</button>
              <button class="btn btn-success btn-block btn-lg hide" id="submit" type="submit">Submit</button>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
              <a href="{{ url('laporan_neracas') }}" class="btn btn-danger btn-block btn-lg">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{!! Form::close() !!}
@stop
@section('footer') 
<script>
  function dummySubmit(){
    if (validatePass()) {
      $('#submit').click();
    }
  }
</script>

@stop
