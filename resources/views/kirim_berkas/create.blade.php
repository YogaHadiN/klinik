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
	<h1>Form Kirim Berkas</h1>
	{!! Form::open(['url' => 'kirim_berkas', 'method' => 'post']) !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group @if($errors->has('staf_id'))has-error @endif">
			  {!! Form::label('staf_id', 'Petugas', ['class' => 'control-label']) !!}
			<div class="row">
				<div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
					{!! Form::select('staf_id[]', App\Staf::list(), null, array(
						'class'         => 'form-control rq'
					))!!}
				</div>
				<div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
					{!! Form::select('role_pengiriman_id[]', App\RolePengiriman::list(), null, array(
						'class'         => 'form-control rq'
					))!!}
				</div>
				<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
					<button type="button" class="btn btn-success btn-block"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
				</div>
			</div>
			  @if($errors->has('staf_id'))<code>{{ $errors->first('staf_id') }}</code>@endif
			</div>
		</div>
	</div>
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
	<textarea name="piutang_asuransi" id="piutang_asuransi" rows="8" cols="40">[]</textarea>
	<textarea name="piutang_tercatat" id="piutang_tercatat" rows="8" cols="40">[]</textarea>
	{!! Form::close() !!}
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

	<div>
	
	  <!-- Nav tabs -->
	  <ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#pencarian_piutang" aria-controls="" role="tab" data-toggle="tab">Pencarian Piutang</a></li>
		<li role="presentation"><a href="#piutang_container" aria-controls="piutang_container" role="tab" data-toggle="tab">Piutang Tercatat</a></li>
	  </ul>
	
	  <!-- Tab panes -->
	  <div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="pencarian_piutang">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">
						<div class="panelLeft">
							
						</div>
						<div class="panelRight">
							<button type="button" onclick="cekSemua();return false;" class="btn btn-success">Cek Semua</button>
						</div>
					</h3>
				</div>
				<div class="panel-body">
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
	<script type="text/javascript" charset="utf-8">

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
		function cekPiutang(x){
			x = $.trim(x);
			var json =$('#piutang_asuransi').val(); 
			var data = $.parseJSON(json);
			var catat = data[x];
			var json_piutang_tercatat =$('#piutang_tercatat').val(); 
			var piutang_tercatat = $.parseJSON(json_piutang_tercatat);
			piutang_tercatat.push(catat);
			$('#piutang_tercatat').val(JSON.stringify(piutang_tercatat));
			var temp = viewTemp( piutang_tercatat );
			$('#body_piutang_tercatat').html(temp);


		}
		function viewTemp(data){
			var temp = ''; 
			for (var i = 0; i < data.length; i++) {
				temp += '<tr>';
				temp += '<td><a target="_blank" href="' + base + '/periksas/' +data[i].periksa_id+ '">';
				temp += data[i].periksa_id;
				temp += '</a></td>';
				temp += '<td>';
				temp += data[i].nama_pasien;
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
				temp += '<td>';
				temp += '<button type="button" onclick="cekPiutang(' + $.trim(i) + '); return false;" class="btn btn-info">Cek</button>'
				temp += '</td>';
				temp += '</tr>';
			}
			return temp;
		}
		
		
	</script>
@stop

