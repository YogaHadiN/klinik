@extends('layout.master')

@section('title') 
Klinik Jati Elok | Kirim Berkas

@stop
@section('page-title') 
<h2>Kirim Berkas</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Kirim Berkas</strong>
	  </li>
</ol>

@stop
@section('content') 

	<div id="staf_container" class="hide">
		@include('kirim_berkas.staf_form')
	</div>
	<h1>Form Kirim Berkas</h1>
	{!! Form::open([
		'url' => 'kirim_berkas', 
		'method' => 'post',
		'id' => 'postKirimBerkas'
	]) !!}
	@include('kirim_berkas.staf_form')
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group @if($errors->has('tanggal'))has-error @endif">
			  {!! Form::label('tanggal', 'Tanggal Pengiriman', ['class' => 'control-label']) !!}
				{!! Form::text('tanggal', date('d-m-Y'), array(
					'class'         => 'form-control rq tanggal'
				))!!}
			  @if($errors->has('tanggal'))<code>{{ $errors->first('tanggal') }}</code>@endif
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-body">
			<h3>Hasil : </h3>
			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<thead>
						<tr>
							<th>Nama Asuransi</th>
							<th>Jumlah Tagihan</th>
							<th>Total Tagihan</th>
						</tr>
					</thead>
					<tbody id="rekap_pengecekan">

					</tbody>
				</table>
			</div>
		</div>
	</div>


	<textarea name="piutang_asuransi" class="hide" id="piutang_asuransi" rows="8" cols="40">[]</textarea>
	<textarea name="piutang_tercatat" class="hide" id="piutang_tercatat" rows="8" cols="40">[]</textarea>
	{!! Form::close() !!}
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<div class="panelLeft">
						
					</div>
					<div class="panelRight">
						<button type="button" onclick="uncekSemua();return false;" class="btn btn-warning">Uncek Semua</button>
						<button type="button" onclick="cekSemua();return false;" class="btn btn-success">Cek Semua</button>
					</div>
				</h3>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover table-condensed table-bordered">
						<tbody>
							<tr>
								<td>
									{!! Form::select('asuransi_id', App\Asuransi::list(), null, [
										'class' => 'form-control selectpick',
										'id' => 'asuransi_id',
										'data-live-search' => 'true'
									]) !!}
								</td>
								<td>
									{!! Form::text('date_from', null, ['class' => 'form-control tanggal', 'id' => 'date_from', 'placeholder' => 'Tanggal Mulai']) !!}
								</td>
								<td>
									{!! Form::text('date_frrm', null, ['class' => 'form-control tanggal', 'id' => 'date_to', 'placeholder' => 'Tanggal Akhir']) !!}
								</td>
								<td>
									<button type="button" class="btn btn-success btn-block" onclick="cariPiutangAsuransi();return false;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				
			</div>
		</div>

	<div>
	
	  <!-- Nav tabs -->
	  <ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#pencarian_piutang" aria-controls="" role="tab" data-toggle="tab">Pencarian Piutang</a></li>
		<li role="presentation"><a href="#piutang_container" aria-controls="piutang_container" role="tab" data-toggle="tab">Piutang Tercatat</a></li>
	  </ul>
	
	  <!-- Tab panes -->

	  <div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="pencarian_piutang">
					<div class="table-responsive">
						<table class="table table-hover table-condensed table-bordered">
							<thead>
								<tr>
									<th>ID PERIKSA</th>
									<th>Nama Pasien</th>
									<th>Asuransi</th>
									<th>Piutang</th>
									<th>Sudah Dibayar</th>
									<th>Sisa Piutang</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody id="body_pencarian_piutang">

							</tbody>
						</table>
					</div>
				</div>
		<div role="tabpanel" class="tab-pane" id="piutang_container">
				<table class="table table-hover table-condensed table-bordered">
					<thead>
						<tr>
							<th>ID PERIKSA</th>
							<th>Nama Pasien</th>
							<th>Asuransi</th>
							<th>Piutang</th>
							<th>Sudah Dibayar</th>
							<th>Sisa Piutang</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="body_piutang_tercatat">

					</tbody>
				</table>

		</div>
	  </div>
	
	</div>
@stop
@section('footer') 
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/0.10.0/lodash.min.js"></script>
	<script type="text/javascript" charset="utf-8">

		$('#postKirimBerkas').find('.staf_id')
		.selectpicker({
			style: 'btn-default',
			size: 10,
			selectOnTab : true,
			style : 'btn-white'
		});
		function cariPiutangAsuransi(){
			$.get(base + '/kirim_berkas/cari/piutang',
				{ 
					asuransi_id: $('#asuransi_id').val(),
					date_from:   $('#date_from').val(),
					date_to:     $('#date_to').val()
				},
				function (data, textStatus, jqXHR) {
					if( data.length > 0 ){
						var temp = viewTemp(data);
						$('#body_pencarian_piutang').html(temp);
						var string =  JSON.stringify( data ) ;
						$('#piutang_asuransi').val(string);
					} else {
						$('#piutang_asuransi').val('[]');
					}
				}
			);
		}
		function cekPiutang(x, control){
			x = $.trim(x);
			var data = parsePiutangAsuransi();
			var catat = data[x];
			piutang_tercatat = parsePiutangTercatat();
			piutang_tercatat.push(catat);
			$('#piutang_tercatat').val(JSON.stringify(piutang_tercatat));
			var temp = viewTemp( piutang_tercatat, true );
			$('#body_piutang_tercatat').html(temp);
			var n = piutang_tercatat.length - 1;
			$(control).closest('td').html( buttonCek(n, false) );
		}
		function viewTemp(data, catat = false){
			var temp = ''; 
			var data_piutang_tercatat = parsePiutangTercatat();
			for (var i = 0; i < data.length; i++) {
				temp += '<tr>';
				temp += '<td><a target="_blank" href="' + base + '/periksas/' +data[i].periksa_id+ '">';
				temp += data[i].periksa_id;
				temp += '</a></td>';
				temp += '<td>';
				temp += data[i].nama_pasien;
				temp += '</td>';
				temp += '<td class="hide">';
				temp += data[i].piutang_id;
				temp += '</td>';
				temp += '<td>';
				temp += data[i].nama_asuransi;
				temp += '</td>';
				temp += '<td class="text-right">';
				temp += uang(data[i].piutang);
				temp += '</td>';
				temp += '<td class="text-right">';
				temp += uang(data[i].sudah_dibayar);
				temp += '</td>';
				temp += '<td class="text-right">';
				temp += uang(data[i].piutang - data[i].sudah_dibayar);
				temp += '</td>';
				if(catat){
					temp += '<td>';
					temp += buttonCek(i, false);
					temp += '</td>';
				} else {
					var tercatat = cekTercatat( data_piutang_tercatat, data[i].piutang_id );
					if (typeof tercatat !== "boolean"){
						temp += '<td>';
						temp += buttonCek(tercatat, false);
						temp += '</td>';
					} else {
						temp += '<td>';
						temp += buttonCek(i);
						temp += '</td>';
					}
				}
				temp += '</tr>';
			}
			if( catat ){
				var grouped = _.groupBy(data, 'nama_asuransi');
				var temp = '';
				var keseluruhan_tagihan = 0;
				var keseluruhan_total_tagihan = 0;
				for (var key in grouped) {
					var jumlah_tagihan = grouped[key].length;
					var total_tagihan = 0;

					for (var i = 0; i < grouped[key].length; i++) {
						total_tagihan += parseInt(grouped[key][i].piutang) - parseInt(grouped[key][i].sudah_dibayar);
					}
					temp += '<tr>';
					temp += '<td>';
					temp += key;
					temp += '</td>';
					temp += '<td class="text-right">';
					temp +=  jumlah_tagihan + ' tagihan';
					temp += '</td>';
					temp += '<td class="text-right">';
					temp +=  uang(total_tagihan);
					temp += '</td>';
					temp += '</tr>';

					keseluruhan_tagihan += jumlah_tagihan;
					keseluruhan_total_tagihan += total_tagihan;
				}
				temp += '<tr>';
				temp += '<td>';
				temp += '</td>';
				temp += '<td class="text-right"><strong>';
				temp +=  keseluruhan_tagihan + ' tagihan';
				temp += '</strong></td>';
				temp += '<td class="text-right"><strong>';
				temp +=  uang(keseluruhan_total_tagihan);
				temp += '</strong></td>';
				temp += '</tr>';

				$('#rekap_pengecekan').html(temp);
			}
			return temp;
		}
		function uncekPiutang(n, control){
			var piutang_tercatat = parsePiutangTercatat();
			piutang_tercatat.splice(n, 1);
			var temp = viewTemp(piutang_tercatat, true);
			$('#piutang_tercatat').val( JSON.stringify( piutang_tercatat ) );
			$('#body_piutang_tercatat').html(temp);

			var data_piutang_pencarian = parsePiutangAsuransi();
			var temp = viewTemp(data_piutang_pencarian);
			$('#piutang_asuransi').val( JSON.stringify( data_piutang_pencarian ) );
			$('#body_pencarian_piutang').html(temp);

		}
		
		function parsePiutangTercatat(){
			var json_piutang_tercatat =$('#piutang_tercatat').val(); 
			var piutang_tercatat = $.parseJSON(json_piutang_tercatat);
			return piutang_tercatat;
		}
		function buttonCek(i, cek = true){
			if( cek ){
				 return '<button type="button" onclick="cekPiutang(' + $.trim(i) + ', this); return false;" class="btn btn-info">Cek</button>'
			} else {
				 return '<button type="button" onclick="uncekPiutang(' + $.trim(i) + ', this); return false;" class="btn btn-danger">Uncek</button>'
			}
		}
		function cekTercatat( data, piutang_id ){
			var ada = false;
			for (var i = 0; i < data.length; i++) {
				if( data[i].piutang_id == piutang_id ){
					ada = i;
					break;
				}
			}
			return ada
		}

		function cekSemua(){
			var piutang_asuransi = parsePiutangAsuransi();
			var piutang_tercatat = parsePiutangTercatat();

			for (var i = 0; i < piutang_asuransi.length; i++) {
				if( typeof cekTercatat( piutang_tercatat, piutang_asuransi[i].piutang_id ) === 'boolean' ){
					piutang_tercatat.push( piutang_asuransi[i]  );
				}
			}

			var temp = viewTemp( piutang_tercatat, true );
			$('#body_piutang_tercatat').html(temp);
			$('#piutang_tercatat').val( JSON.stringify(piutang_tercatat) );

			var temp = viewTemp(piutang_asuransi);
			$('#body_pencarian_piutang').html(temp);
		}

		function parsePiutangAsuransi(){
			var json =$('#piutang_asuransi').val(); 
			var data = $.parseJSON(json);
			return data;
		}
		function uncekSemua(){
			var piutang_tercatat = parsePiutangTercatat();
			var data = parsePiutangAsuransi();
			for (var i = 0; i < piutang_tercatat.length; i++) {
				if( typeof cekTercatat( data, piutang_tercatat[i].piutang_id ) !== 'boolean' ){
					piutang_tercatat.splice(i,1);
					i = i-1;
				}
			}
			$('#piutang_tercatat').val( JSON.stringify(piutang_tercatat) );
			var temp = viewTemp(data);
			$('#body_pencarian_piutang').html(temp);
			temp = viewTemp(piutang_tercatat, true);
			$('#body_piutang_tercatat').html( temp );
		}
		function tambahStaf(control){
			var staf_id         = $(control).closest('.row').find('.staf_id').val();
			var role_pengiriman = $(control).closest('.row').find('.role_pengiriman').val();
			var staf_tervalidasi = false;
			var role_pengiriman_tervalidasi = false;
			if( staf_id ){
				staf_tervalidasi = true;
			} else {
				validasiin(control, '.staf_id', 'Nama Staf harus diisi');
			}
			if(role_pengiriman){
				role_pengiriman_tervalidasi = true;
			} else {
				validasiin(control, '.role_pengiriman', 'Role Pengiriman harus diisi');
			}

			if( staf_tervalidasi && role_pengiriman_tervalidasi) {
				tambah(control)
			}
		}

		function kurangStaf(control){

			$(control).closest('.row').remove();
			 
		}
		function tambah(control){
			var temp = $('#staf_container').html();
			$(control).closest('.row').after(temp);
			$(control).closest('.row').next().find('.staf_id').selectpicker({
				style: 'btn-default',
				size: 10,
				selectOnTab : true,
				style : 'btn-white'
			});
			$(control).closest('.row').next().find('.btn-white').focus();
			$(control)
				.removeClass('btn-success')
				.addClass('btn-danger')
				.html('<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>')
				.attr('onclick','kurangStaf(this);return false;')
		}
		function validasiin(control, classSelector, pesan){
			$(control).closest('.row').find(classSelector) 
			.parent()
			.find('code')
			.remove();

			$(control).closest('.row').find(classSelector) 
			.parent()
			.addClass('has-error')
			.append('<code>' + pesan + '</code>');

			$(control).closest('.row').find(classSelector) 
			.parent()
			.find('code')
			.hide()
			.fadeIn(1000);

			$(control).closest('.row').find(classSelector) 
		   .on('keyup change', function(){
			  $(this).parent()
			  .removeClass('has-error')
			  .find('code')
			  .fadeOut('1000', function() {
				  $(this).remove();
			  });
		   })   
		}
	</script>
@stop

