@extends('layout.master')

@section('title') 
Klinik Jati Elok | Detail Pembayaran Asuransi

@stop
@section('page-title') 
<h2>Detail Pembayaran Asuransi</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Detail Pembayaran Asuransi</strong>
	  </li>
</ol>

@stop
@section('content') 
	<h1>Detail Pembayaran Asuransi</h1>
	<div>
	
	  <!-- Nav tabs -->
	  <ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#pembayaran" aria-controls="pembayaran" role="tab" data-toggle="tab">Pembayaran</a></li>
		<li role="presentation"><a href="#detail" aria-controls="detail" role="tab" data-toggle="tab">Detail Pembayaran</a></li>
	  </ul>
	
	  <!-- Tab panes -->
	  <div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="pembayaran">
			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<tbody>
						<tr>
							<td>Asuransi</td>
							<td>{{ $pembayaran->asuransi->nama }}</td>
						</tr>
						<tr>
							<td>Periode</td>
							<td>{{ date('d F y', strtotime( $pembayaran->mulai ))  }} - {{ date('d F y', strtotime( $pembayaran->akhir )) }}</td>
						</tr>
						<tr>
							<td>Tanggal input</td>
							<td>{{ date('d F y', strtotime( $pembayaran->created_at )) }}</td>
						</tr>
						<tr>
							<td> pembayaran </td>
							<td>{{ App\Classes\Yoga::buatrp( $pembayaran->pembayaran ) }}</td>
						</tr>
						<tr>
							<td> tanggal dibayar </td>
							<td>{{ date('d F y', strtotime( $pembayaran->tanggal_dibayar )) }}</td>
						</tr>
						<tr>
							<td>Kas ke</td>
							<td>{{ $pembayaran->coa->coa }}</td>
						</tr>
						<tr>
							<td>Staf Penginput</td>
							<td>{{ $pembayaran->staf->nama }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			

		</div>
		<div role="tabpanel" class="tab-pane" id="detail">
			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<thead>
						<tr>
							<th>Tanggal</th>
							<th>Nama</th>
							<th>Pembayaran</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@if(count($pembayaran_asuransi) > 0)
							@foreach($pembayaran_asuransi as $pa)
								<tr>
									<td>{{ date('d M y', strtotime( $pa->tanggal )) }}</td>
									<td>	
										<a href="{{ url('periksas/' . $pa->periksa_id) }}">{{ $pa->nama_pasien }}</a>
									</td>
									<td class="text-right">{{ App\Classes\Yoga::buatrp( $pa->pembayaran )}}</td>
									<td>
										<a class="btn btn-warning btn-sm" href="{{ url('piutang_dibayars/' . $pa->piutang_dibayar_id . '/edit') }}">Edit</a>
									</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td colspan="5" class="text-center">Tidak ada data untuk ditampilkan</td>
							</tr>
						@endif
					</tbody>
					<tfoot>
						<tr>
							<td colspan="2"></td>
							<td>
									<h3 class="text-right">{{ App\Classes\Yoga::buatrp( $total_pembayaran )}}</h3>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	  </div>
	
	</div>

	
@stop
@section('footer') 
	
@stop

