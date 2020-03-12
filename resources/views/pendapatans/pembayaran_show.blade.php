@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Laporan Pembayaran

@stop
@section('page-title') 
	<h2>Laporan Pembayaran {!! $asuransi->nama !!}</h2>
 <ol class="breadcrumb">
      <li>
          <a href="{{ url('laporans')}}">Home</a>
      </li>
      <li class="active">
          <strong>Laporan Per Pembayaran</strong>
      </li>
</ol>
@stop

@section('head') 
	<style type="text/css" media="all">
		.barcode {
			position: fixed;
			bottom: 73px;
			left: 0px;
			z-index: 999;
		}
	</style>
@stop
@section('content') 
<div class="row">
    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <div class="panel-title">
					<div class="panelLeft">
						Pembayaran
					</div>
					<div class="panelRight">
						<button class="btn btn-primary" onclick="importPembayaranExcel(this);return false;" type="button">
							 Excel  <span class="glyphicon glyphicon-import" aria-hidden="true"></span>
						</button>
					</div>
				</div>
            </div>
            <div class="panel-body">
                {!! Form::open([
					'url'          => 'pendapatans/pembayaran/asuransi',
					'method'       => 'post',
					'autocomplete' => 'off'
				]) !!} 
                    {!! Form::textarea('temp', json_encode( $pembayarans ), ['class' => 'form-control hide', 'id' => 'pembayarans']) !!} 
                    {!! Form::text('mulai', $mulai, ['class' => 'form-control hide']) !!} 
                    {!! Form::text('akhir', $akhir, ['class' => 'form-control hide']) !!} 
                <div class="form-group hide">
                    {!! Form::label('asuransi_id', 'Staf') !!}
                    {!! Form::text('asuransi_id' , $asuransi_id, ['class' => 'form-control']) !!}
                </div>
				<div class="form-group @if($errors->has('staf_id'))has-error @endif">
				  {!! Form::label('staf_id', 'Petugas', ['class' => 'control-label']) !!}
                  {!! Form::select('staf_id', App\Classes\Yoga::stafList() , null, ['class' => 'form-control selectpick rq', 'data-live-search' => 'true', 'id'=>'staf_id']) !!}
				  @if($errors->has('staf_id'))<code>{{ $errors->first('staf_id') }}</code>@endif
				</div>
                @if (\Auth::id() == 28)
					<div class="form-group @if($errors->has('coa_id'))has-error @endif">
					  {!! Form::label('coa_id', 'Akun Kas Tujuan', ['class' => 'control-label']) !!}
                      {!! Form::select('coa_id', $kasList, null, ['class' => 'form-control rq', 'id'=>'kasList']) !!}
					  @if($errors->has('coa_id'))<code>{{ $errors->first('coa_id') }}</code>@endif
					</div>
                @else
                    <div class="form-group">
                      {!! Form::label('coa_id', 'Akun Kas Tujuan') !!}
                      {!! Form::select('coa_id', $kasList, 110000, ['class' => 'form-control rq', 'id'=>'kasList', 'readonly' => 'readonly']) !!}
					  @if($errors->has('coa_id'))<code>{{ $errors->first('coa_id') }}</code>@endif
					</div>
                @endif
				<div class="form-group @if($errors->has('tanggal_dibayar'))has-error @endif">
				  {!! Form::label('tanggal_dibayar', 'Tanggal Dibayar', ['class' => 'control-label']) !!}
                  {!! Form::text('tanggal_dibayar' , null, ['class' => 'form-control tanggal rq']) !!}
				  @if($errors->has('tanggal_dibayar'))<code>{{ $errors->first('tanggal_dibayar') }}</code>@endif
				</div>
				<div class="form-group @if($errors->has('dibayar'))has-error @endif">
				  {!! Form::label('dibayar', 'Dibayar Sebesar', ['class' => 'control-label']) !!}
                  {!! Form::text('dibayar' , null, ['class' => 'form-control rq uangInput', 'id'=>'piutang']) !!}
				  @if($errors->has('dibayar'))<code>{{ $errors->first('dibayar') }}</code>@endif
				</div>
				<div class="form-group @if($errors->has('invoice_id'))has-error @endif">
				  {!! Form::label('invoice_id', 'ID Invoice', ['class' => 'control-label']) !!}
				  <div class="table-responsive">
				  	<table class="table table-hover table-condensed table-bordered">
				  		<tbody>
							<tr>
								<td>
									<div class="form-group">
										{!! Form::text('invoice_id[]', null, array(
											'class'         => 'form-control phone',
											'placeholder'   => 'Nomor Invoice'
											))!!}
									</div>
								</td>
								<td class="column-fit">
									<button type="button" class="btn btn-primary" onclick="tambahInput(this); return false;">
										<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
									</button>
								</td>
							</tr>
						</tbody>
				  	</table>
				  </div>
                  {!! Form::text('invoice_id' , null, ['class' => 'form-control rq', 'id'=>'invoice_id']) !!}
				  @if($errors->has('invoice_id'))<code>{{ $errors->first('invoice_id') }}</code>@endif
				</div>
				<div class="form-group @if($errors->has('kata_kunci'))has-error @endif">
				  {!! Form::label('kata_kunci', 'Kata Kunci', ['class' => 'control-label']) !!}
				  {!! Form::text('kata_kunci' , $asuransi->kata_kunci, ['class' => 'form-control rq', 'id'=>'kata_kunci']) !!}
				  @if($errors->has('kata_kunci'))<code>{{ $errors->first('kata_kunci') }}</code>@endif
				</div>
				@if(isset($id))
					@include('pendapatans.pembayaran_show_form', ['id' => $id])
				@else
					@include('pendapatans.pembayaran_show_form', ['id' => null])
				@endif
				{!! Form::textarea('catatan_container', '[]', ['class' => 'form-control textareacustom hide', 'id' => 'catatan_container']) !!}
                <div class="form-group">
                    <button class="btn btn-success btn-lg btn-block" type="button" onclick="submitPage(this);return false;">Bayar</button>
                    {!! Form::submit('Bayar', ['class' => 'btn btn-success hide', 'id'=>'submit']) !!}
                </div>
                {!! Form::close() !!}
				<textarea name="" id="excel_pembayaran" class="textareacustom hide" rows="8" cols="40">{{ $excel_pembayaran }}</textarea>
            </div>
        </div>
    </div>
    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Informasi</div>
            </div>
            <div class="panel-body">
				<div class="table-responsive">
					<table class="table table-condensed">
						<tbody>
							<tr>
								<tr>
									<td>Nama Asuransi</td>
									<td class="text-right"> {{ $asuransi->nama }}</td>
								</tr>
								<tr>
									<td>Mulai</td>
									<td class="text-right"> {{ $mulai }}</td>
								</tr>
								<tr>
									<td>Akhir</td>
									<td class="text-right"> {{ $akhir }}</td>
								</tr>
								<tr>
									<td> Total Piutang </td>
									<td class="text-right">{{  App\Classes\Yoga::buatrp( $total_belum_dibayar + $total_sudah_dibayar) }}</td>
								</tr>
								<tr>
									<td>Sudah Dibayar Total</td>
									<td class="text-right">{{  App\Classes\Yoga::buatrp( $total_sudah_dibayar )}}</td>
								</tr>
								<tr>
									<td>Belum Dibayar Total</td>
									<td class="text-right">{{  App\Classes\Yoga::buatrp( $total_belum_dibayar )}}</td>
								</tr>
							</tr>
						</tbody>
					</table>
				</div>
            </div>
        </div>
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Invoice</h3>
			</div>
			<div class="panel-body">
			</div>
		</div>
		<div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title">Catatan</div>
            </div>
            <div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover table-condensed table-bordered">
						<thead>
							<tr>
								<th>Nama Peserta</th>
								<th>Tagihan</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="container_catatan">

						</tbody>
					</table>
				</div>
			</div>
        </div>
		<div class="barcode" id="panel_perbandingan">
			
		</div>
    </div>
</div>
<div>
  <!-- Nav tabs -->
  <div class="panel panel-default">
  	<div class="panel-body">
	  <ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#detail_pembayaran" aria-controls="detail_pembayaran" role="tab" data-toggle="tab">Detail Pembayaran</a></li>
		<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile Pembayaran</a></li>
		<li role="presentation"><a href="#excel_gak_cocok" aria-controls="excel_gak_cocok" role="tab" data-toggle="tab">Bandingkan Data</a></li>
	  </ul>

  <!-- Tab panes -->
	  <div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="detail_pembayaran">
			 <div class="panel panel-danger">
				<div class="panel-heading">
					<div class="panelLeft">
						<div class="panel-title">Detail Pembayaran  <span id="jumlah_pasien"></span> Pasien </div>
					</div>
					<div class="panelRight">
						<a class="btn btn-success" href="#" onclick="cekAll();return false;">Cek Semua</a>
						<a class="btn btn-danger" href="#" onclick="resetAll();return false;">Reset Semua</a>
					</div>
				</div>
				<div class="panel-body">
					<div class-"table-responsive">
						<table class="table table-hover table-condensed">
							<thead>
								<tr>
									<th>ID PERIKSA</th>
									<th>Nama Pasien</th>
									<th>Piutang</th>
									<th>Sudah Dibayar</th>
									<th>Pembayaran</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody id="table_temp2">

							</tbody>
						</table>
					</div>
				</div>
			</div>   
		</div>
		<div role="tabpanel" class="tab-pane" id="profile">
			@include('asuransis.templateHutangPembayaran', ['pembayarans' => $pembayarans_template])
		</div>
		<h3>Cocok = <span id="cocok"></span></h3>
		<h3>Tiak Cocok = <span id="tidak_cocok"></span></h3>
		<h3></h3>
		<div role="tabpanel" class="tab-pane" id="excel_gak_cocok">
			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<thead>
						<tr>
							<th>Nama Peserta</th>
							<th>Tagihan</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="bandingkan_data">

					</tbody>
				</table>
			</div>
			
		</div>
	  </div>
  	</div>
  </div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			<div class="panelLeft">
				Sudah Dibayar
				{{ count($sudah_dibayars)  }} pasien, TOTAL {{ App\Classes\Yoga::buatrp( $total_sudah_dibayar ) }}
			</div>
		</h3>
	</div>
	<div class="panel-body">
		<div class-"table-responsive">
			<table class="table table-hover table-condensed">
				<thead>
					<tr>
						<th>ID PERIKSA</th>
						<th>Nama Pasien</th>
						<th>Piutang</th>
						<th>Sudah Dibayar</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($sudah_dibayars) > 0)
						@foreach($sudah_dibayars as $dibayar)
							<tr>
								<td>{{ $dibayar->periksa_id }}</td>
								<td> {{ $dibayar->nama_pasien }} </td>
								<td>{{ App\Classes\Yoga::buatrp( $dibayar->piutang )}}</td>
								<td>{{ App\Classes\Yoga::buatrp( $dibayar->pembayaran )}}</td>
								<td nowrap class="autofit">
									<a class="btn btn-warning btn-sm" href="{{ url('piutang_dibayars/' . $dibayar->piutang_dibayar_id . '/edit') }}">Edit</a>
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="5">
								Tidak ada data untuk ditampilkan :p
							</td>
						</tr>
					@endif
				</tbody>
			</table>
		</div>
	</div>
</div>
@stop
@section('footer') 
<script type="text/javascript" charset="utf-8">
view(true);
@if(isset($id))
	cekRekening('#rekening_id');
@endif
function cek(control){
	var html_barcode = $('.barcode').html();
	var x = $('.barcode').find('.i').html();
    var sudah_dibayar       = $(control).closest('tr').find('td:nth-child(4)').html();
    var piutang             = $(control).closest('tr').find('td:nth-child(3)').html();
    sudah_dibayar           = cleanUang(sudah_dibayar.trim());
    piutang                 = cleanUang(piutang.trim());
    var akan_dibayar        = parseInt(piutang) - parseInt(sudah_dibayar);
    var key                 = $(control).val();
    var Array               = $('#pembayarans').val();
    Array                   = JSON.parse(Array);
    Array[key].akan_dibayar = akan_dibayar;
    $('#pembayarans').val(JSON.stringify(Array));
    view();
	if( html_barcode != '' ){
		deleteCekPembayaran(x);
	}
}
function reset(control){
    var key = $(control).val();
    var Array = $('#pembayarans').val();
    Array = $.parseJSON(Array);
    Array[key].akan_dibayar = 0;
    $('#pembayarans').val(JSON.stringify(Array));
    view();
}
function cekAll(){
    var Array = $('#pembayarans').val();
    Array = $.parseJSON(Array);
    for (var i                = 0; i < Array.length; i++) {
        var piutang           = Array[i].piutang;
        var sudah_dibayar     = Array[i].pembayaran;
        var akan_dibayar      = parseInt(piutang) - parseInt(sudah_dibayar);
        Array[i].akan_dibayar = akan_dibayar;
    };
	
    $('#pembayarans').val(JSON.stringify(Array));
    view();
}
function view(pertama_kali = false){
    let MyArray             = $('#pembayarans').val();
    MyArray                 = $.parseJSON(MyArray);
    var temp                = '';
    var temp2               = '';
    var temp_excel_pembayaran               = '';
    var akan_dibayar        = 0;
    var piutang_total       = 0;
    var sudah_dibayar_total = 0;
    var belum_dibayar_total = 0;
    var excel_pembayaran = $.parseJSON($('#excel_pembayaran').val());

	console.log('excel_pembayaran');
	console.log(excel_pembayaran);

	var cocok = 0;
	var total  = excel_pembayaran.length;
	console.log('excel_pembayaran');
	console.log(excel_pembayaran);
    for (var i = 0; i < MyArray.length; i++) {
		{{-- if( pertama_kali ){ --}}
		{{-- 	for (var r = 0; r < excel_pembayaran.length; r++) { --}}
		{{-- 		var excel_tagihan = excel_pembayaran[r].tagihan; --}}
		{{-- 		if ( --}}
		{{-- 			excel_pembayaran[r].peserta == null && --}}
		{{-- 			MyArray[i].piutang == excel_tagihan --}}
		{{-- 		) { --}}
		{{-- 			cocok = cocok + 1; --}}
		{{-- 			excel_pembayaran.splice(r, 1); --}}
		{{-- 			if(MyArray[i].piutang - MyArray[i].pembayaran > 0){ --}}
		{{-- 				MyArray[i].akan_dibayar = excel_tagihan; --}}
		{{-- 			} --}}
		{{-- 			break; --}}
		{{-- 		} --}}
		{{-- 	}; --}}
		{{-- } --}}

        if(MyArray[i].piutang - MyArray[i].pembayaran > 0){
            piutang_total += MyArray[i].piutang;
            sudah_dibayar_total += MyArray[i].pembayaran;
            belum_dibayar_total += MyArray[i].piutang - MyArray[i].pembayaran;
            temp2 += '<tr>';
            temp2 += '<td>' + MyArray[i].periksa_id + '</td>';
			temp2 += '<td>' + MyArray[i].nama_pasien + '</td>';
            temp2 += '<td class="uang">' + MyArray[i].piutang + '</td>';
            temp2 += '<td class="uang">' + MyArray[i].pembayaran + '</td>';
            temp2 += '<td><input class="form-control angka2 akan_dibayar" value="' + MyArray[i].akan_dibayar + '" onkeyup="akanDibayarKeyup(this);return false;" /></td>';
            if(MyArray[i].piutang - MyArray[i].pembayaran < 1){
            var status = '<div class="alert-success">';
            status += 'Sudah Lunas';
            status += '</div>';
            } else {
            var status = '<div class="alert-danger">';
            status += 'Belum Lunas';
            status += '</div>';
            }
            temp2 += '<td>' + status + '</td>';
            temp2 += '<td><button class="btn btn-sm btn-primary" onclick="cek(this);return false;" type="button" value="' + i + '">Cek</button> ';
            temp2 += '<button class="btn btn-sm btn-warning" onclick="reset(this);return false;" type="button" value="' + i + '">Reset</button></td>';
            temp2 += '</tr>';
            akan_dibayar += parseInt( MyArray[i].akan_dibayar );
        }
    };
	if( $.trim( temp2 ) == '' ){
		temp2 = '<tr><td colspan="7" class="text-center">Tidak Ada Piutang Yang Belum Dibayar</td></tr>';
	}

	refreshExcelPembayaran(excel_pembayaran);

	tidak_cocok = total - cocok;


    $('#jumlah_pasien').html(i);
    $('#table_temp').html(temp);
    $('#table_temp2').html(temp2);
	if(!pertama_kali){
		$('#piutang').val(uang2( akan_dibayar ));
	}
    $('#piutang_total').html(piutang_total);
    $('#belum_dibayar_total').html(belum_dibayar_total);
    $('#sudah_dibayar_total').html(sudah_dibayar_total);
    $('#dibayar_sebesar').html(akan_dibayar);
    $('#tidak_cocok').html(tidak_cocok);
    $('#cocok').html(cocok);
    $('#pembayarans').val(JSON.stringify(MyArray));
    formatUang();
}
function resetAll(){
    var Array = $('#pembayarans').val();
    Array = $.parseJSON(Array);
    for (var i = 0; i < Array.length; i++) {
        Array[i].akan_dibayar = 0;
    };
    $('#pembayarans').val(JSON.stringify(Array));
    view();
}

function submitPage(control){
	var val = $('#rekening_id').val();
	$.get(base + '/transaksi/avail',
		{ id: val },
		function (data, textStatus, jqXHR) { 
			console.log('dataaa');
			console.log(data);
			validate(control, $.trim(data));
		});

}
function akanDibayarKeyup(control){

	var before = $(control).val();
	$(control).val(parseInt(before) || '');
	if ( $(control).val() == '' ) {
		$(control).val('0')
	}

	var jumlahAkanDibayar =0;
	$('.akan_dibayar').each(function(){
		 jumlahAkanDibayar += parseInt( $(this).val() );
	});

	jumlahAkanDibayar = uang(jumlahAkanDibayar);


	$('#piutang').val(jumlahAkanDibayar); 

	var tempJson = $('#pembayarans').val();
	var tempArray = JSON.parse(tempJson);

	var i = $(control).closest('tr').find('.btn-primary').val();

	tempArray[i].akan_dibayar = $(control).val();

	$('#pembayarans').val( JSON.stringify(tempArray) );
}
function deleteCekPembayaran(i){
	var excel_pembayaran = $.parseJSON($('#excel_pembayaran').val());

	excel_pembayaran.splice(i, 1);

	refreshExcelPembayaran(excel_pembayaran);

	$('.nav-tabs a[href="#excel_gak_cocok"]').tab('show');

	$("#excel_gak_cocok")[0].scrollIntoView()
}

function cekExcelPembayaran(control){
	var nama_peserta = $(control).closest('tr').find('.nama_peserta').html();
	var tagihan = $(control).closest('tr').find('.tagihan').html();
	var i = $(control).closest('tr').find('.i').html();
	var temp = '<p class="bg-padding bg-success">';
	$('.nav-tabs a[href="#detail_pembayaran"]').tab('show');
	temp += '<span class="nama_peserta">';
	temp += nama_peserta
	temp += '</span> ';
	temp += '<span class="tagihan">';
	temp += tagihan
	temp += '</span> ';
	temp += '<span class="i">';
	temp += i
	temp += '</span> ';
	temp += '<br />';
	temp += '<button class="btn btn-info" type="button" onclick="jadikanCatatan(this); return false;">Jadikan Catatan</button>'
	temp += '<button class="btn btn-danger" type="button" onclick="deleteCek(' + i + ');">Selesai</button>'
	temp += '<button class="btn btn-success" type="button" onclick="bersihkan();">Clear</button>'
	temp += '<br />';
	temp += '</p>';
	$('#panel_perbandingan').html(temp);
}

function deleteCek(i){
	deleteCekPembayaran(i);
}

function jadikanCatatan(control){
	var nama_peserta = $(control).closest('.barcode').find('.nama_peserta').html();
	var tagihan      = $(control).closest('.barcode').find('.tagihan').html();
	var x            = $(control).closest('.barcode').find('.i').html();

	catatan(nama_peserta, tagihan, x);
}

function catatan(nama_peserta, tagihan, x){
	var array = {
		'nama_peserta': nama_peserta,
		'tagihan':      tagihan
	};
	if(confirm('Masukkan ke dalam catatan?')){
		var catatanExisting = parseCatatanExisting();
		catatanExisting.push(array);
		viewCatatanExisting(catatanExisting);
		deleteCekPembayaran(x);
	}
}



function refreshExcelPembayaran(excel_pembayaran){
	var temp_excel_pembayaran = '';
	for (var r = 0; r < excel_pembayaran.length; r++) {
		temp_excel_pembayaran += '<tr>';
		temp_excel_pembayaran += '<td class="i hide">' + r + '</td>';
		temp_excel_pembayaran += '<td class="nama_peserta">' + excel_pembayaran[r].peserta + '</td>';
		temp_excel_pembayaran += '<td class="tagihan">' + excel_pembayaran[r].tagihan + '</td>';
		temp_excel_pembayaran += '<td>';
		temp_excel_pembayaran += ' <button type="button" class="btn btn-warning btn-sm" onclick="cekExcelPembayaran(this); return false;">Bandingkan</button>';
		temp_excel_pembayaran += ' <button type="button" class="btn btn-info btn-sm" onclick="jadikanCatatanDisini(this); return false;">Catatan</button>';
		temp_excel_pembayaran += ' <button type="button" class="btn btn-danger btn-sm" onclick="deleteCekPembayaran(' +r+ '); return false;">Delete</button>';
		temp_excel_pembayaran += ' </td>';
	}
    $('#excel_pembayaran').val(JSON.stringify(excel_pembayaran));
    $('#bandingkan_data').html(temp_excel_pembayaran);
	$('#panel_perbandingan').html('');
}
function arr_diff (a1, a2) {

    var a = [], diff = [];

    for (var i = 0; i < a1.length; i++) {
        a[a1[i]] = true;
    }

    for (var i = 0; i < a2.length; i++) {
        if (a[a2[i]]) {
            delete a[a2[i]];
        } else {
            a[a2[i]] = true;
        }
    }

    for (var k in a) {
        diff.push(k);
    }
    return diff;
}
function delCatatan(control){
	if(confirm("Anda akan menghapus catatan ini")){
		var nama_peserta = $(control).closest('tr').find('.nama_peserta').html();
		var tagihan      = $(control).closest('tr').find('.tagihan').html();
		var i            = $(control).closest('tr').find('.i').html();

		var array = {
			'peserta' : nama_peserta,
			'tagihan' : tagihan
		};

		var excel_pembayaran = $.parseJSON($('#excel_pembayaran').val());

		excel_pembayaran.push(array);

		refreshExcelPembayaran(excel_pembayaran);

		var catatanExisting = parseCatatanExisting();
		catatanExisting.splice( i, 1 );
		viewCatatanExisting(catatanExisting);
	}

}
function parseCatatanExisting(){
	var catatanExisting = $('#catatan_container').html();
	catatanExisting = JSON.parse(catatanExisting);

	return catatanExisting;
}

function viewCatatanExisting(catatanExisting){
	var temp = '';
	for (var i = 0; i < catatanExisting.length; i++) {
		temp += '<tr>';
		temp += '<td class="i hide">';
		temp += i
		temp += '</td>';
		temp += '<td class="nama_peserta">';
		temp += catatanExisting[i].nama_peserta;
		temp += '</td>';
		temp += '<td class="tagihan">';
		temp += catatanExisting[i].tagihan;
		temp += '</td>';
		temp += '<td>';
		temp += '<button type="button" class="btn btn-danger btn-sm" onclick="delCatatan(this);return false;">del</button>';
		temp += '</td>';
		temp += '</tr>';
	}
	catatanExisting = JSON.stringify(catatanExisting);
	$('#catatan_container').html(catatanExisting);
	$('#container_catatan').html(temp);
}

function stripString(str){
	str = $.trim(str);
	str = str.toLowerCase();
	str = str.replace(/[^\w\s]|_/g, "").replace(/\s+/g, " ");
	str = str.replace(/\s/g, '');
	str = str.split(',')[0];
	str = str.replace(/\s/g, '');
	return str;
}
function bersihkan(){
	 $('.barcode').html('');
}
function jadikanCatatanDisini(control){
	var nama_peserta = $(control).closest('tr').find('.nama_peserta').html();
	var tagihan      = $(control).closest('tr').find('.tagihan').html();
	var x            = $(control).closest('tr').find('.i').html();
	catatan(nama_peserta, tagihan, x);
}
function validate(control, dt){
	var data = $('#pembayarans').val();
	data = JSON.parse(data);
	var akanDibayar = 0;

    for (var i = 0; i < data.length; i++) {
        if(data[i].piutang - data[i].pembayaran > 0){
			akanDibayar += parseInt( data[i].akan_dibayar );
			if( data[i].akan_dibayar > ( data[i].piutang - data[i].pembayaran ) ){
				var baris = parseInt(i) + 1;
				alert('Pembayaran ' + data[i].nama_pasien + ', baris ke ' + baris + ' lebih besar dari nilai piutangnya, harap diperbaiki');
				return false;
			}
		}
    };

	var found_tr_id = true;
	if(dt == '0'){
		found_tr_id = false;
	}

	console.log('found_tr_id');
	console.log(found_tr_id);

	console.log('dt == "0"');
	console.log(dt == '0');

	if(
		validatePass2(control) 
		&& cleanUang( $('#piutang').val() ) > 0 
		&& data.length > 0
		&& akanDibayar > 0
		&& found_tr_id
	){
		 $('#submit').click();
	} else if(cleanUang( $('#piutang').val() ) < 1 ){
		alert('Nilai yang dibayarkan harus lebih besar dari 0');
		validasi('#piutang', 'nilai harus lebih dari Rp. 0 ');
	} else if(akanDibayar < 1 ){
		alert('Harus ada pasien yang di ceklist');
	} 
	if(!found_tr_id ){
		validasi1( $('#rekening_id'), 'Transaksi tidak ditemukan')
	}
}

function cekRekening(control){

	var id = $(control).val();

	$.get(base + '/rekenings/cek_id',
		{ id: id },
		function (data, textStatus, jqXHR) {
			$(control).closest('.form-group').find('.alert').remove();
			if( data ){
				$(control).closest('.form-group').append('<div><div class="alert alert-info"><h2>' + uang(data.nilai) + '</h2><h4>'+ moment(data.tanggal, 'YYYY-MM-DD HH:II:SS').format('DD MMM YYYY')+'</h4>' + data.deskripsi + '</div></div>')
			}
		}
	);

}
</script>
@stop
