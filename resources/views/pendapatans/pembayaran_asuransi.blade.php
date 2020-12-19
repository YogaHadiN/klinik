@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Laporan Pembayaran Asuransi
@stop
@section('page-title') 
 <h2>Pembayaran Asuransi</h2>
 <ol class="breadcrumb">
      <li>
          <a href="{!! url('laporans')!!}">Home</a>
      </li>
      <li class="active">
          <strong>Pembayaran Asuransi</strong>
      </li>
</ol>
@stop
@section('content') 
@if ( Session::has('print') )
    <div id="print">
    </div>
@endif
@if(isset($id))
{!! Form::open([
	'url'    => 'pengeluarans/pembayaran_asuransi/show/' . $id,
	"class"  => "m-t",
	"role"   => "form",
	"files"  => "true",
	"method" => "post"
]) !!}
@else
{!! Form::open([
	'url'    => 'pengeluarans/pembayaran_asuransi/show',
	"class"  => "m-t",
	"role"   => "form",
	"files"  => "true",
	"method" => "post"
]) !!}
@endif
<div class="row">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <div class="panel panel-default">
          <div class="panel-body">
            <h1>Pembayaran Asuransi</h1>
            <hr>
					{!! Form::text('session_print', Session::get('print'), ['class' => 'form-control hide', 'id' => 'session_print']) !!}
				<div class="form-group @if($errors->has('asuransi_id'))has-error @endif">
				  {!! Form::label('asuransi_id', 'Asuransi', ['class' => 'control-label']) !!}
				  {!! Form::select('asuransi_id', $asuransi_list , null , [
					  'class'            => 'selectpick form-control rq',
					  'data-live-search' => 'true',
					  'onchange'               => 'asuransiChange(this);return false;'
				  ]) !!}
				  @if($errors->has('asuransi_id'))<code>{{ $errors->first('asuransi_id') }}</code>@endif
				</div>
				<div class="form-group @if($errors->has('mulai'))has-error @endif">
				  {!! Form::label('mulai', 'Mulai', ['class' => 'control-label']) !!}
                  {!! Form::text('mulai', null, ['class' => 'form-control rq tanggal']) !!}
				  @if($errors->has('mulai'))<code>{{ $errors->first('mulai') }}</code>@endif
				</div>
				<div class="form-group @if($errors->has('akhir'))has-error @endif">
				  {!! Form::label('akhir', 'Akhir', ['class' => 'control-label']) !!}
                  {!! Form::text('akhir', null, ['class' => 'form-control rq tanggal']) !!}
				  @if($errors->has('akhir'))<code>{{ $errors->first('akhir') }}</code>@endif
				</div>
				@if(\Auth::id() == 28)
				<div class="form-group{{ $errors->has('excel_pembayaran') ? ' has-error' : '' }}">
					{!! Form::label('excel_pembayaran', 'Excel Pembayaran') !!}
					{!! Form::file('excel_pembayaran') !!}
					  @if($errors->has('excel_pembayaran'))<code>{{ $errors->first('excel_pembayaran') }}</code>@endif
				</div>
				@endif
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
  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
  	<div class="panel panel-info">
  		<div class="panel-body">
			<h2 id="namaAsuransi">
				
			</h2>
  			<div id="riwayatHutang">
  				
  			</div>
  		</div>
  	</div>
  </div>
</div>
{!! Form::close() !!}
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class-"table-responsive">
			<div class="row">
				<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
					Menampilkan <span id="rows"></span> hasil
				</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 padding-bottom">
					{!! Form::select('displayed_rows', App\Classes\Yoga::manyRows(), 15, [
						'class' => 'form-control',
						'onchange' => 'clearAndSearch();return false;',
						'id'    => 'displayed_rows'
					]) !!}
				</div>
			</div>
			<table class="table table-hover table-condensed" id="table_pembayaran_asuransi">
				<thead>
					<tr>
						<th>
							Id
							{!! Form::text('id', null, [
								'class' => 'form-control id',
								'onkeyup' => 'clearAndSearch();return false',
							]) !!}
						</th>
						<th>
							Created At
							{!! Form::text('created_at', null, [
								'class' => 'form-control created_at',
								'onkeyup' => 'clearAndSearch(); return false'
							]) !!}
						</th>
						<th>
							Nama Asuransi
							{!! Form::text('nama_asuransi', null, [
								'class' => 'form-control nama_asuransi',
								'onkeyup' => 'clearAndSearch(); return false'
							]) !!}
						</th>
						<th>
							Periode
							{!! Form::text('periode', null, [
								'class' => 'form-control periode',
								'onkeyup' => 'clearAndSearch(); return false'
							]) !!}
						</th>
						<th>
							Pembayaran
							{!! Form::text('pembayaran', null, [
								'class' => 'form-control pembayaran',
								'onkeyup' => 'clearAndSearch(); return false'
							]) !!}
						</th>
						<th>
							Tanggal Pembayaran
							{!! Form::text('tanggal_pembayaran', null, [
								'class' => 'form-control tanggal_pembayaran',
								'onkeyup' => 'clearAndSearch(); return false'
							]) !!}
						</th>
						<th>
							Tujuan Kas
							{!! Form::text('tujuan_kas', null, [
								'class' => 'form-control tujuan_kas',
								'onkeyup' => 'clearAndSearch(); return false'
							]) !!}
						</th>
						<th>
							Action
						</th>
					</tr>
				</thead>
				<tbody id="pembayaran_asuransi_container"></tbody>
			</table>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div id="page-box">
						<nav class="text-right" aria-label="Page navigation" id="paging">

						</nav>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>
@stop
@section('footer') 
	<script src="{!! url('js/twbs-pagination/jquery.twbsPagination.min.js') !!}"></script>
	<script src="{!! url('js/pembayaran_asuransi.js') !!}"></script>
@stop


