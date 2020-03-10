	<div class="row">
		<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h2>Hutang {{ $asuransi->nama }} {{ count( $hutangs ) }} pasien</h2>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-condensed table-bordered DTs">
							<thead>
								<tr>
									<th>Bulan</th>
									<th>Hutang</th>
									<th>Sudah Dibayar</th>
									<th>Sisa hutang</th>
									{{-- <th>Jumlah Bayar</th> --}}
								</tr>
							</thead>
							<tbody>
								@if(count($hutangs) > 0)
									@foreach( $hutangs as $hutang )
										<tr
											@if(    $hutang->hutang  -  $hutang->sudah_dibayar  > 0  )
												class="bg-danger"
											@endif
											>
											<td>
												<a href="{{ url('pengeluarans/pembayaran_asuransi/show?asuransi_id=' . $asuransi->id. '&mulai=' . date('01-m-Y', strtotime($hutang->tanggal)). '&akhir=' . date('t-m-Y', strtotime( $hutang->tanggal ))) }}" data-toggle="tooltip" title="Formulir pelunasan piutang asuransi {{ $asuransi->nama }} bulan {{  date('M Y', strtotime($hutang->tanggal))  }}" data-placement="bottom">
													{{ date('Y-m', strtotime($hutang->tanggal)) }}
													{{-- {{ $hutang->id }} --}}
												</a>
												  
											</td>
											<td class="text-right">{{ App\Classes\Yoga::buatrp(  $hutang->hutang  )}}</td>
											<td class="text-right">
												<a href="{{ url('asuransis/' . $asuransi->id . '/piutangAsuransi/SudahDibayar/' . date('Y-m-01', strtotime( $hutang->tanggal )). '/' . date('Y-m-t', strtotime( $hutang->tanggal ))) }}">
													{{ App\Classes\Yoga::buatrp(  $hutang->sudah_dibayar  )}}
												</a>
											</td>
											<td class="text-right">
												<a href="{{ url('asuransis/' . $asuransi->id . '/piutangAsuransi/BelumDibayar/' . date('Y-m-01', strtotime( $hutang->tanggal )). '/' . date('Y-m-t', strtotime( $hutang->tanggal ))) }}">
													{{ App\Classes\Yoga::buatrp(   $hutang->hutang  -  $hutang->sudah_dibayar  )}}
												</a>
											</td>
										</tr>
									@endforeach
								@else
									<tr>
										<td class="text-center" colspan="2">Tidak ada data untuk ditampilkan</td>
									</tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
			<div class="panel panel-success">
				<div class="panel-heading">
					<h2>Riwayat Pembayaran {{ $asuransi->nama }}</h2>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-hover table-condensed table-bordered">
							<thead>
								<tr>
									<th>Tanggal</th>
									<th>Petugas</th>
									<th>Pembayaran</th>
								</tr>
							</thead>
							<tbody>
								@if($pembayarans->count() > 0)
									@foreach( $pembayarans as $pembayaran )
										<tr>
											<td>{{ $pembayaran->tanggal_dibayar->format('Y-m-d') }}</td>
											<td>{{ $pembayaran->staf->nama }}</td>
											<td class="text-right">
												<a href="{{ url('pembayaran_asuransis/' . $pembayaran->id) }}">
													{{ App\Classes\Yoga::buatrp(  $pembayaran->pembayaran ) }}
												</a>
											</td>
										</tr>
									@endforeach
								@else
									<tr>
										<td class="text-center" colspan="3">Tidak ada data untuk ditampilkan</td>
									</tr>
								@endif
							</tbody>
						</table>
					</div>
			
				</div>
			</div>
		</div>
	</div>
