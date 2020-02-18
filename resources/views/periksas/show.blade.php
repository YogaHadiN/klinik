@extends('layout.master')
@section('title') 
{{ env("NAMA_KLINIK") }} | Pasien

@stop
@section('page-title') 

 <h2>Semua Pemeriksaan</h2>
 <ol class="breadcrumb">
      <li>
          <a href="{{ url('laporans')}}">Home</a>
      </li>
      <li class="active">
          <strong>Semua Pemeriksaan</strong>
      </li>
</ol>
@stop
@section('content') 
	<input type="input" name="" class="hide" id="periksa_id" value="{{ $periksa->id }}" />

<div class="panel panel-primary">
      <div class="panel-heading">
            <div class="panel-title">
                <div class="panelLeft">
                    <h3>Nama Pasien : {!!$periksa->pasien->id!!} - {!!$periksa->pasien->nama!!}</h3>
                </div>
                <div class="panelRight">
					<a class="btn btn-lg btn-warning " href="{{ url('periksas/' .$periksa->id . '/edit/transaksiPeriksa') }}"
						@if( \Auth::id() != '28' )
							disabled
						@endif
					>
						Edit - Super Admin Only
					</a>
				</div>
            </div>
      </div>
      <div class="panel-body">
		  <div class="table-responsive">
				<table class="table table-bordered table-hover" id="tableAsuransi">
					  <thead>
						<tr>
							<th>Tanggal</th>
							<th>Status</th>
							<th>Terapi</th>
						</tr>
					</thead>
					<tbody>
						 <tr>
								<td rowspan="2">
									{!! $periksa->tanggal !!} <br><br>
									<strong>Pemeriksa :</strong><br> 
									{!! $periksa->staf->nama !!} <br><br>
									<strong>Pembayaran</strong> <br>
									{!! $periksa->asuransi->nama !!} <br><br>
									<strong>Jam Datang</strong> <br>
									{!! $periksa->jam !!} <br><br>
									<strong>Periksa id</strong> <br>
									{!! $periksa->id !!}
								</td>
								<td>
									<strong>Anamnesa :</strong> <br>
									{!! $periksa->anamnesa !!} <br>
									<strong>Pemeriksaan Fisik, Penunjang dan Tindakan :</strong> <br>
									{!! $periksa->pemeriksaan_fisik !!} <br>
									{!! $periksa->pemeriksaan_penunjang !!}<br>
									@if( !empty($periksa->sistolik) || !empty($periksa->sistolik))
										<strong>Tekanan Darah</strong> <br>
										{!! $periksa->sistolik !!}/{!! $periksa->diastolik !!} mmHg  <br>
									@endif
									<strong>Diagnosa :</strong> <br>
									{!! $periksa->diagnosa->diagnosa !!} - {!! $periksa->diagnosa->icd10->diagnosaICD !!}
									<br> <br>
									<div class="row">
										@if($periksa->usg)
											<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
												<a href="{{ url('usgs/' . $periksa->id) }}" class="btn btn-primary btn-block">Hasil USG</a>
											</div>
										@endif
										@if($periksa->registerAnc)
											<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
												<a href="{{ url('ancs/' . $periksa->id) }}" class="btn btn-info btn-block">Hasil ANC</a>
											</div>
										@endif
									</div>
								</td>
								<td>{!! $periksa->terapi_html !!}</td>
							</tr>
							<tr>
								<td>
									<div class="row">
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
											  <h2>Transaksi : </h2>
										</div>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										</div>
									</div>
								  <table class="table table-condensed">
									<tbody>
									  {!! $periksa->tindakan_html !!}
									</tbody>
									<tfoot>
									  <tr class="b-top-bold-big">
										<td>Total Biaya Transaksi </td>
										<td>:</td>
										<td  class="text-right">{!! $periksa->total_transaksi !!}</td>
									  </tr>
									</tfoot>
								  </table>
								</td>
								<td>
									<h2>Transaksi</h2>
									 <table class="table table-condensed">
									  <tbody>
										<tr>
										  <td>Pembayaran tunai</td>
										  <td class="uang">{!! $periksa->tunai !!}</td>
										</tr>
										<tr>
										  <td>Pembayaran Piutang</td>
										  <td class="uang">{!! $periksa->piutang !!}</td>
										</tr>
									  </tbody>
									</table>
								</td>
							</tr>
					</tbody>
				</table>
				<div class="row">
					<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
						  <a href="{{ url('pdfs/kuitansi/' . $periksa->id ) }}" class="btn btn-success btn-block" target="_blank">Cetak Kuitansi</a>
					</div>
					<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
						  <a href="{{ url('pdfs/status/' . $periksa->id ) }}" class="btn btn-primary btn-block" target="_blank">Cetak Resep</a>
					</div>
					<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
						  <a href="{{ url('pdfs/struk/' . $periksa->id ) }}" class="btn btn-warning btn-block" target="_blank">Cetak Struk</a>  
					</div>
					<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

					</div>
				</div>
		  </div>
      </div>
</div>

<div class="row">
	<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
		<div class="panel panel-danger">
			<div class="panel-heading">
				<div class="panel-title">
					<div class="panelLeft">
						Jurnal Umum
					</div>	
					<div class="panelRight">
					</div>
				</div>
			</div>
			<div class="panel-body">
				@include('periksas.jurnals')
			</div>
		</div>
	</div>
	<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="panel-title">
					Upload Berkas Pemeriksaan
				</div>
			</div>
			<div class="panel-body">
				<form enctype="multipart/form-data">
					<input name="file" type="file" />
				</form>
				<div class="progress">
				  <div class="progress-bar" id="progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
						0%
				  </div>
				</div>
				<div id="download_container">
					@if( file_exists("berkas/pemeriksaan/" . $periksa->id . ".pdf") )
						<a class="btn btn-success btn-block" href="{{ url('berkas/pemeriksaan/' . $periksa->id  . '.pdf') }}" target="_blank">Download Berkas</a>;
					@endif
					
				</div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-info">
  <div class="panel-heading">
    <h3 class="panel-title">Rincian obat</h3>
  </div>
  <div class="panel-body">
	  <div class="table-responsive">
			<table class="table table-striped table-bordered table-hover" id="tableAsuransi">
			  <thead>
				<tr>
				  <th>ID</th>
				  <th>Merek Obat</th>
				  <th>harga beli</th>
				  <th>harga jual</th>
				  <th>jumlah</th>
				  <th>Modal</th>
				  <th>Bruto</th>
				  <th>Untung</th>
				</tr>
			</thead>
			<tbody>
			   @foreach ($periksa->terapii as $terapi)
				 <tr>
					<td>
					  {!! $terapi->id !!}
					</td>
					<td>
					 {!! $terapi->merek_id !!} - {!! $terapi->merek->merek !!}
					</td>
					<td class="uang">
					  {!! $terapi->harga_beli_satuan !!}
					</td>
					<td class="uang">
					  {!! $terapi->harga_jual_satuan !!}
					</td>
					<td>
					  {!! $terapi->jumlah !!}
					</td>
					<td class="uang">
					  {!! $terapi->jumlah * $terapi->harga_beli_satuan !!}
					</td>
					<td class="uang">
					  {!! $terapi->jumlah * $terapi->harga_jual_satuan !!}
					</td>
					<td class="uang">
					  {!! $terapi->jumlah * $terapi->harga_jual_satuan - $terapi->jumlah * $terapi->harga_beli_satuan !!}
					</td>
				 </tr>
			   @endforeach
			</tbody>
			<tfoot>
			  <tr>
				<th colspan="5">Total :</th>
				<td class="uang">{!! $periksa->terapi_modal !!}</td>
				<td class="uang">{!! $periksa->terapi_bruto !!}</td>
				<td class="uang">{!! $periksa->terapi_untung !!}</td>
			  </tr>
			</tfoot>
		</table>
	  </div>
  </div>
</div>
<div class="row">
	<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="panel-title">
					<div class="panelLeft">
						Pembayaran
					</div>	
					<div class="panelRight">
					</div>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover table-condensed table-bordered">
						<thead>
							<tr>
								<th>Id</th>
								<th>Tanggal Dibayar</th>
								<th>Staf</th>
								<th>Tanggal Input</th>
								<th>Dibayar ke</th>
								<th>Jumlah Pembayaran</th>
							</tr>
						</thead>
						<tbody>
							@if($periksa->pembayarans->count() > 0)
								@foreach($periksa->pembayarans as $pa)
									<tr>
										<td>{{ $pa->pembayaran_asuransi_id }}</td>

										<td>{{ date('d M y', strtotime( $pa->pembayaranAsuransi->tanggal_dibayar )) }}</td>
										<td>{{ $pa->pembayaranAsuransi->staf->nama }}</td>
										<td>{{ date('d M y', strtotime( $pa->created_at )) }}</td>
										<td>{{ $pa->pembayaranAsuransi->coa->coa }}</td>
										<td class="text-right"> 
											<a class="" href="{{ url('pembayaran_asuransis/' . $pa->pembayaran_asuransi_id) }}">
												{{ App\Classes\Yoga::buatrp( $pa->pembayaran ) }}
											</a>
										</td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="6" class="text-center">Tidak ada data untuk ditampilkan</td>
								</tr>
							@endif
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>



@include('obat')
@stop
@section('footer') 
<script type="text/javascript" charset="utf-8">
	var base = '{{ url("/") }}';
	var periksa_id = $('#periksa_id').val();
	$(':file').on('change', function () {
		  var file = this.files[0];
		  if (file.size > 10485760) {
			alert('File paling besar untuk di upload adalah 10 MB');
			$(this).val('');
		  } else if(file.name.split('.').pop() != 'pdf'  ) {
			alert('Hanya file dalam bentuk PDF yang bisa diupload');
			$(this).val('');
		  } else {
			$.ajax({
				// Your server script to process the upload
				url: base + '/periksas/' + periksa_id + '/upload',
				type: 'POST',

				// Form data
				data: new FormData($('form')[0]),

				// Tell jQuery not to process data or worry about content-type
				// You *must* include these options!
				cache: false,
				contentType: false,
				processData: false,

				// Custom XMLHttpRequest
				xhr: function () {
				  var myXhr = $.ajaxSettings.xhr();
				  if (myXhr.upload) {
					// For handling the progress of the upload
					myXhr.upload.addEventListener('progress', function (e) {
					  if (e.lengthComputable) {
						  var persen= e.loaded / e.total *100;
						$('#progress').attr({
						  'aria-valuenow': persen,
						  'style': 'width:' + persen + '%'
						});
						$('#progress').html(persen + ' %');
						if( persen == 100 ){
							var html = '<a class="btn btn-success btn-block" href="' + base + '/berkas/pemeriksaan/' + periksa_id+ '.pdf" target="_blank">Download Berkas</a>';
							$('#download_container').html(html);
						} else {
							$('#download_container').html('');
						} 
					  }

					
					}, false);
				  }
				  return myXhr;
				}
			  });
		  }
	});
</script>
	{!! HTML::script('js/informasi_obat.js') !!}
@stop
