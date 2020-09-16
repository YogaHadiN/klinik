@if( $status == 'danger' || $status == 'warning' )
<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
	<div class="alert alert-warning" role="alert">
		<h1>PERHATIAN!!</h1>
		<div class="alert alert-info" role="alert">
			<h4>Jika tidak dipenuhi maka halaman ini tidak bisa disubmit</h4>
		</div>
		<div class="alert alert-success" role="alert">
			Label Hijau artinya dalam batas aman
		</div>
		<div class="alert alert-warning" role="alert">
			Label Kuning artinya peringatan mendekati batas minimal
		</div>
		<div class="alert alert-danger" role="alert">
			Label Merah artinya kurang dari batas minimal
		</div>
	</div>
</div>
@endif
<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
	<div class="panel panel-{{ $admedikaWarning }}">
		<div class="panel-heading">
			<h3 class="panel-title">Tagihan Admedika</h3>
		</div>
		<div class="panel-body">
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
		</div>
	</div>
</div>
<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
	<div class="panel panel-{{ $mootaWarning }}">
		<div class="panel-heading">
			<h3 class="panel-title">Moota</h3>
		</div>
		<div class="panel-body">
			<h3>Saldo Moota</h3>
				<h1>{{ App\Classes\Yoga::buatrp( $moota_balance ) }}</h1>
				<p>Saldo Moota harus diatas Rp. 10.000,- </p>
		</div>
	</div>
</div>
<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
	<div class="panel panel-{{ $vultrWarning }}">
		<div class="panel-heading">
			<h3 class="panel-title">Vultr</h3>
		</div>
		<div class="panel-body">
			<h3>Credit Vultr</h3>
			<h1>$ {{ abs( $vultr['balance'] + $vultr['pending_charges'] ) }} </h1>
				<p>Credit Vultr harus diatas $15</p>
		</div>
	</div>
</div>
<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
	<div class="panel panel-{{ $wablasWarning }}">
		<div class="panel-heading">
			<h3 class="panel-title">Wablas</h3>
		</div>
		<div class="panel-body">
			<h3>Credit Wablas</h3>
			<h1>Quota = {{ $quota }}</h1>
			<p>kuota harus diatas 500</p>
			<h1>Expired = {{ $expired }} ( {{ App\Classes\Yoga::dateDiffNow( $expired ) }} hari )</h1>
			<p>expired harus diatas 3 hari</p>
		</div>
	</div>
</div>
