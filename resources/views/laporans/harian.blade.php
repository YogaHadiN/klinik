@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Laporan Harian

@stop
@section('head')
    <link href="{!! asset('css/print.css') !!}" rel="stylesheet" media="print">
@stop
@section('page-title') 

 <h2>Laporan Harian</h2>
 <ol class="breadcrumb">
      <li>
          <a href="{{ url('laporans')}}">Home</a>
      </li>
      <li class="active">
          <strong>Laporan Harian</strong>
      </li>
</ol>
@stop
@section('content') 
	<div class="row">
		<div class="col-md-4 col-lg-4">
			<div class="alert alert-info">
				<h2>Jumlah Pasien Lama</h2>
				<h1>{{ $pasien_lama }} pasien, {{ $persen_lama }} %</h1>
			</div>
		</div>
		<div class="col-md-4 col-lg-4">
			<div class="alert alert-success">
				<h2>Jumlah Pasien Baru</h2>
				<h1>{{ $pasien_baru }} pasien, {{ $persen_baru }} %</h1>
			</div>
		</div>
	</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-info">
              <div class="panel-heading">
                    <h2 class="panel-title">
						<div class="panelLeft">
							Ringkasan Laporan
						</div>
						<div class="panelRight">
							<a class="btn btn-success btn-lg" target="_blank" href="{{ url('pdfs/struk/pertanggal/' . date('Y', strtotime($tanggal)) . '/'.  date('m', strtotime($tanggal)) . '/' . date('d', strtotime($tanggal)  ))}}">Cetak Struk Hari Ini</a>
						</div>
					
					</h2>
              </div>
              <div class="panel-body">
				  @include('laporans.harianForm')
              </div>
        </div>
    </div>
</div>
@if( 
	\App\User::find(\Auth::id())->role == '2' ||
	\App\User::find(\Auth::id())->role == '3' ||
	\App\User::find(\Auth::id())->role == '4' ||
	\App\User::find(\Auth::id())->role == '6' 
	)

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="panel panel-primary">
			  <div class="panel-heading">
					<div class="panel-title">
						<div class="panelLeft">
							<h3>Laporan Tanggal : {!! App\Classes\Yoga::updateDatePrep($tanggal) !!}</h3>
						</div>
						<div class="panelRight">
							<h3>Total : {!! count($periksas) !!}</h3>
						</div>
					</div>
			  </div>
			  <div class="panel-body">
				  <div class="table-responsive">
					  <table class="table table-bordered table-hover" id="tableAsuransi">
						  <thead>
							  <tr>
								  <th class="hide">ID PERIKSA</th>
								  <th>Periksa Id</th>
								  <th class="hide old_asuransi_id">old_asuransi_id</th>
								  <th class="hide tanggal">tanggal</th>
								  <th>Nama Pasien</th>
								  <th>Pembayaran</th>
								  <th>Poli</th>
								  <th>Tunai</th>
								  <th>Piutang</th>
								  <th>Action</th>
							  </tr>
						  </thead>
						  <tbody>
							  @if (count($periksas) > 0)
								  @foreach ($periksas as $key => $periksa)
									  <tr>
										  <td class="hide periksa_id">{!! $periksa->periksa_id !!}</td>
										  <td class="periksa_id">{!! $periksa->periksa_id !!}</td>
										  <td class="hide old_asuransi_id">{!! $periksa->asuransi_id !!}</td>
										  <td class="hide tanggal">{!! $periksa->tanggal !!}</td>
										  <td class="nama_pasien">{!! $periksa->nama_pasien !!}</td>
										  {{-- <td>{!! $periksa->nama_asuransi !!}</td> --}}
										  <td>
											  {!! Form::select('asuransi_id', $list_asuransi, $periksa->asuransi_id, [
												  'class' => 'form-control selectpick',
												  'onchange' => 'updateAsuransiPeriksa(this);return false;',
												  'data-live-search' => 'true'
											  ]) !!}
										  </td>
										  <td>{!! $periksa->poli !!}</td>
										  <td class='uang'>{!! $periksa->tunai !!}</td>
										  <td class='uang'>{!! $periksa->piutang !!}</td>
										  <td>
											  
											  <a class="btn btn-block btn-info" href="{{ url('periksas/' . $periksa->periksa_id ) }}" target="_blank"> <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Detail</a>
										  </td>
									  </tr>
								  @endforeach
							  @else
								  <tr>
									  <td colspan="6" class="text-center">Tidak / Belum ada transaksi tanggal {!! App\Classes\Yoga::updateDatePrep($tanggal) !!}</td>
								  </tr>
							  @endif
						  </tbody>
						  <tfoot>
							  <tr>
								  <th colspan="4">Total</th>
								  <td class="uang">{!! App\Classes\Yoga::totalTunaiHarian($periksas)!!}</td>
								  <td class="uang">{!! App\Classes\Yoga::totalPiutangHarian($periksas)!!}</td>
							  </tr>
						  </tfoot>
					  </table>
				  </div>
			  </div>
		</div>
	</div>
	</div>
	@endif
@stop
@section('footer') 
{!! HTML::script('js/laporan_harian.js')!!}
	
@stop
