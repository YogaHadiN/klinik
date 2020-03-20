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
<script>
	cariPembayaranAsuransi();
	var timeout;
	var length = $("#table_pembayaran_asuransi").find('thead').find('th').length;
	function clearAndSearch(key = 0){
		$("#pembayaran_asuransi_container").html("<tr><td colspan='" +length + "' class='text-center'><img class='loader' src='" + base + "/img/loader.gif'></td></tr>");
		window.clearTimeout(timeout);
		timeout = window.setTimeout(function(){
			if($('#paging').data("twbs-pagination")){
				$('#paging').twbsPagination('destroy');
			}
			cariPembayaranAsuransi(key);
		},600);
	}
	var base = '{{ url("/") }}';
    $(function () {
          if( $('#print').length > 0 ){
            window.open("{{ url('pdfs/pembayaran_asuransi/' . Session::get('print')) }}", '_blank');
          }
    });

  function dummySubmit(){
    if (validatePass()) {
      $('#submit').click();
    }
  }
  function asuransiChange(control){
	  var asuransi_id = $(control).val();
	  
	  var param = { 
	  	'asuransi_id' : asuransi_id
	  };
	  $.post('{{ url('pendapatans/pembayaran/asuransis/riwayatHutang') }}', param, function(data) {
		  data = JSON.parse(data);
		  var temp = '<table class="table table-hover table-condensed table-bordered DTs">';
		  temp += '<table class="table table-hover table-condensed table-bordered"><thead> <tr> <th>Bulan</th> <th>Hutang</th> <th>Sudah Dibayar</th> </tr> </thead>';
		  temp += '<tbody>';
			for (var i = 0; i < data.length; i++) {
				if( i < 8 ){
					temp += '<tr>';
					temp += '<td class="uangNew">' + data[i].bulan + '-' + data[i].tahun + '</td>';
					temp += '<td class="text-right">' + data[i].hutang + '</td>';
					temp += '<td class="text-right">' + data[i].sudah_dibayar + '</td>';
					temp += '</tr>';
				}
			};
		  temp += '</tbody> </table>';
		  $('#riwayatHutang').html(temp);
		  $('#riwayat_hutang_asuransi').dataTable();
		  $('#namaAsuransi').html(
			  '<a href="' +base + '/asuransis/' + data[0].asuransi_id + '/hutang/pembayaran">Riwayat Hutang'+ data[0].nama_asuransi +'</a>'
		  );
	  });
  }
	function cariPembayaranAsuransi(key = 0){
		var pages;
		var id                 = $('#table_pembayaran_asuransi').find('.id').val();
		var created_at         = $('#table_pembayaran_asuransi').find('.created_at').val();
		var nama_asuransi      = $('#table_pembayaran_asuransi').find('.nama_asuransi').val();
		var periode            = $('#table_pembayaran_asuransi').find('.periode').val();
		var pembayaran         = $('#table_pembayaran_asuransi').find('.pembayaran').val();
		var tanggal_pembayaran = $('#table_pembayaran_asuransi').find('.tanggal_pembayaran').val();
		var tujuan_kas         = $('#table_pembayaran_asuransi').find('.tujuan_kas').val();

		$.get(base + '/pendapatans/pembayaran_asuransi/cari_pembayaran',
			{ 
				id:                 id,
				created_at:         created_at,
				nama_asuransi:      nama_asuransi,
				periode:            periode,
				pembayaran:         pembayaran,
				displayed_rows:     $('#displayed_rows').val(),
				tanggal_pembayaran: tanggal_pembayaran,
				tujuan_kas:         tujuan_kas,
				key:         key
			},
			function (data, textStatus, jqXHR) {
				console.log('data.data');
				console.log(data.data);
				var temp = '';
				if( data.data.length > 0 ){
					for (var i = 0; i < data.data.length; i++) {
						temp += '<tr>'
						temp += '<td>'
						temp += data.data[i].id
						temp += '</td>'
						temp += '<td>'
						temp += data.data[i].created_at
						temp += '</td>'
						temp += '<td>'
						temp += data.data[i].nama_asuransi
						temp += '</td>'
						temp += '<td>'
						temp += data.data[i].periode
						temp += '</td>'
						temp += '<td class="text-right">'
						temp += uang(data.data[i].pembayaran)
						temp += '</td>'
						temp += '<td>'
						temp += data.data[i].tanggal_pembayaran
						temp += '</td>'
						temp += '<td>'
						temp += data.data[i].tujuan_kas
						temp += '</td>'
						temp += '</tr>'
					}
				} else {
						temp += '<tr>'
						temp += '<td class="text-center" colspan=' + length+ '>'
						temp += 'Tidak ada data untuk ditampilkan'
						temp += '</td>'
						temp += '</tr>'
				}
				$('#pembayaran_asuransi_container').html(temp);
				$('#rows').html(data.rows);
				pages = data.pages;
				$('#paging').twbsPagination({
					startPage: parseInt(key) +1,
					totalPages: pages,
					{{-- totalPages: 3, --}}
					visiblePages: 7,
					onPageClick: function (event, page) {
						cariPembayaranAsuransi(parseInt( page ) -1);
					}
				});
			}
		);
	}
</script>
@stop


