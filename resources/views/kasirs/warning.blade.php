<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
	<div class="panel panel-{{ $status }}">
		<div class="panel-heading">
			<h3 class="panel-title">CEKLIST HARIAN</h3>
		</div>
		<div class="panel-body">
			@if( $status == 'danger' || $status == 'warning' )
				<div class="alert alert-warning" role="alert">
					<h1>PERHATIAN!!</h1>
				</div>
			@endif
			<h3>Saldo Moota</h3>
				<h1>{{ App\Classes\Yoga::buatrp( $moota_balance ) }}</h1>
				<p>Saldo Moota harus diatas Rp. 10.000,- </p>

			<h3>Credit Vultr</h3>
			<h1>$ {{ abs( $vultr['balance'] + $vultr['pending_charges'] ) }} </h1>
				<p>Credit Vultr harus diatas $15</p>
			<h3>Piutang Admedika Belum Dikirim</h3>
			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<tbody>
						<tr>
							<td>{{ $pasien_pertama_belum_dikirim->nama_pasien }}</td>
							<td>{{ $pasien_pertama_belum_dikirim->tanggal }}</td>
							<td>{{ $pasien_pertama_belum_dikirim->nama_asuransi }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<h1>{{ $jarak_hari }} hari</h1>
			<p>Maksmial 24 hari sudah harus dikirim</p>
			<div class="alert alert-info" role="alert">
				<h4>Jika tidak dipenuhi maka halaman ini tidak bisa disubmit</h4>
			</div>
		</div>
	</div>
</div>
