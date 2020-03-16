@extends('layout.master')

@section('title') 
	Klinik Jati Elok | Rekening Bank {{ $rekening->akun }}
@stop
@section('head') 
	<style type="text/css" media="all">
		.kolom_1{
			width: 10% !important;
		}
		.kolom_2{
			width: 10% !important;
		}
		.kolom_3{
			width: 50% !important;
		}
		.kolom_4{
			width: 10% !important;
		}
	</style>
@stop
@section('page-title') 
<h2>Rekening Bank {{ $rekening->akun }}</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Rekening Bank {{ $rekening->akun_bank->akun }}</strong>
	  </li>
</ol>
@stop
@section('content') 
	{!! Form::text('akun_bank_id', $rekening->akun_bank_id, ['class' => 'form-control hide', 'id' => 'akun_bank_id']) !!}
	<div class="table-responsive">
			<div class="row">
			  	<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
			  		Menampilkan <span id="rows"></span> hasil
			  	</div>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 padding-bottom">
					{!! Form::select('displayed_rows', App\Classes\Yoga::manyRows(), 15, [
						'class' => 'form-control',
						'onchange' => 'clearAndSelectPasien();return false;',
						'id'    => 'displayed_rows'
					]) !!}
				</div>
			  </div>
		<table id="table_rekening" class="table table-hover table-condensed table-bordered">
			<thead>
				<tr>
					<th nowrap class="kolom_1">
						ID
					</th>
					<th nowrap class="kolom_2">
						Tanggal
                        {!! Form::text('tanggal', null, [
							'class' => 'form-control-inline tgl form-control ajaxsearchrekening',
							'onkeyup' => 'clearAndSearch();return false;',
							'id'    => 'tanggal'
						])!!}
					</th>
					<th class="kolom_3">
						Deskripsi
                        {!! Form::text('deskripsi', null, [
							'class' => 'form-control-inline deskripsi form-control ajaxsearchrekening',
							'onkeyup' => 'clearAndSearch();return false;',
							'id' => 'deskripsi'
						])!!}
					</th>
					<th nowrap class="kolom_4">
						Kredit
					</th>
					<th nowrap class="kolom_4">
						Action
						{!! Form::select('pembayaran_null',[
								0 => 'Semua' ,
								1 => 'Belum Dicek' ,
								2 => 'Sudah Dicek' 
							], 0, [
							'class'   => 'form-control-inline pembayaran_null form-control ajaxsearchrekening',
							'onchange' => 'clearAndSearch();return false;',
							'id'      => 'pembayaran_null'
						])!!}
					</th>
				</tr>
			</thead>
			<tbody id="rek_container">

			</tbody>
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
@stop
@section('footer') 
	<script src="{!! url('js/twbs-pagination/jquery.twbsPagination.min.js') !!}"></script>
	<script type="text/javascript" charset="utf-8">
		search();
		var timeout;
		var length = $("#rek_container").closest('table').find('thead').find('th').length;
		function clearAndSearch(key = 0){
			 {{-- $("#rek_container").html("<tr><td colspan='2'></td><td colspan='2' class='text-center'><img src='" + base + "/img/loader.gif'></td><td colspan='3'></td></tr>"); --}}
			
			$("#rek_container").html("<tr><td colspan='" +length + "' class='text-center'><img class='loader' src='" + base + "/img/loader.gif'></td></tr>");
			window.clearTimeout(timeout);
			timeout = window.setTimeout(function(){
				if($('#paging').data("twbs-pagination")){
					$('#paging').twbsPagination('destroy');
				}
				search(key);
			},600);
		}
		function search(key = 0){
			var pages;
			 $.get(base + '/rekening_bank/search',
			 	{ 
					'tanggal':         $('#tanggal').val(),
					'akun_bank_id':    $('#akun_bank_id').val(),
					'deskripsi':       $('#deskripsi').val(),
					'pembayaran_null': $('#pembayaran_null').val(),
					'displayed_rows':  $('#displayed_rows').val(),
					'key':             key
				},
			 	function (data, textStatus, jqXHR) {
					{{-- $('#paging').html(''); --}}
					var temp = '';
					for (var i = 0; i < data.data.length; i++) {
						temp += '<tr';
						if( data.data[i].pembayaran_asuransi_id ){
							temp += ' class="success"';
						}
						temp +='>';
						temp += '<td nowrap class="kolom_1">';
						temp += data.data[i].id;
						temp += '</td>';
						temp += '<td nowrap class="kolom_2">';
						temp += data.data[i].tanggal;
						temp += '</td>';
						temp += '<td class="kolom_3">';
						temp += data.data[i].deskripsi;
						temp += '</td>';
						temp += '<td class="text-right kolom_4" nowrap>';
						temp += uang(data.data[i].nilai);
						temp += '</td>';
						temp += '<td class="kolom_4" nowrap>';
						if( data.data[i].pembayaran_asuransi_id ){
							temp += '<button type="button" class="btn btn-warning btn-sm btn-block">Detail</button>';
						} else {
							temp += '<a class="btn btn-primary btn-sm btn-block" href="' + base + '/pendapatans/pembayaran/asuransi/' + data.data[i].id+ '">Confirm</a>';
						}
						temp += '</td>';
						temp += '</tr>';
					}
					$('#rek_container').html(temp);
					$('#rows').html(data.rows);
					pages = data.pages;
					$('#paging').twbsPagination({
						startPage: parseInt(key) +1,
						totalPages: pages,
						{{-- totalPages: 3, --}}
						visiblePages: 7,
						onPageClick: function (event, page) {
							search(parseInt( page ) -1);
						}
					});
			 	}
			 );
		}
</script>
@stop

