@extends('layout.master')

@section('title') 
	Klinik Jati Elok | Rekening Bank {{ $rekening->akun }}

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
		<table class="table table-hover table-condensed table-bordered">
			<thead>
				<tr>
					<th nowrap>
						Tanggal
                        {!! Form::text('tanggal', null, [
							'class' => 'form-control-inline tgl form-control ajaxsearchrekening',
							'onkeyup' => 'clearAndSearch();return false;',
							'id'    => 'tanggal'
						])!!}
					</th>
					<th>
						Deskripsi
                        {!! Form::text('deskripsi', null, [
							'class' => 'form-control-inline deskripsi form-control ajaxsearchrekening',
							'onkeyup' => 'clearAndSearch();return false;',
							'id' => 'deskripsi'
						])!!}
					</th>
					<th nowrap>
						Kredit
					</th>
					@if(\Auth::user()->role == '1')
					<th nowrap>
						Saldo
					</th>
					@endif
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
					'tanggal':        $('#tanggal').val(),
					'akun_bank_id':   $('#akun_bank_id').val(),
					'deskripsi':      $('#deskripsi').val(),
					'displayed_rows': $('#displayed_rows').val(),
					'key':            key
				},
			 	function (data, textStatus, jqXHR) {
					{{-- $('#paging').html(''); --}}
					var temp = '';
					for (var i = 0; i < data.data.length; i++) {
						temp += '<tr>';
						temp += '<td nowrap>';
						temp += data.data[i].tanggal;
						temp += '</td>';
						temp += '<td>';
						temp += data.data[i].deskripsi;
						temp += '</td>';
						temp += '<td class="text-right" nowrap>';
						temp += uang(data.data[i].nilai);
						temp += '</td>';
					@if(\Auth::user()->role == '1')
						temp += '<td class="text-right" nowrap>';
						temp += uang(data.data[i].saldo_akhir);
						temp += '</td>';
					@endif
						temp += '</tr>';
					}
					$('#rek_container').html(temp);
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

