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
	{!! Form::open(
	[
		'url' => 'test', 
		'method' => 'post',
		"files"  => "true"
	]) !!}
		<div class="form-group{{ $errors->has('rekening') ? ' has-error' : '' }}">
			{!! Form::label('rekening', 'Rekening') !!}
			{!! Form::file('rekening') !!}
			{!! $errors->first('rekening', '<p class="help-block">:message</p>') !!}
		</div>
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				@if(isset($update))
					<button class="btn btn-success btn-block" type="button" onclick='dummySubmit(this);return false;'>Update</button>
				@else
					<button class="btn btn-success btn-block" type="button" onclick='dummySubmit(this);return false;'>Submit</button>
				@endif
				{!! Form::submit('Submit', ['class' => 'btn btn-success hide', 'id' => 'submit']) !!}
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<a class="btn btn-danger btn-block" href="{{ url('home/') }}">Cancel</a>
			</div>
		</div>
	{!! Form::close() !!}
	
@stop
@section('footer') 
<script type="text/javascript" charset="utf-8">
	function dummySubmit(control){
		if(validatePass2(control)){
			$('#submit').click();
		}
	}
</script>

@stop
