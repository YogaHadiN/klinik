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

			<h3>Credit Zenziva</h3>
				<h1>{{ $zenziva_credit }}</h1>
				<p>Credit Zenziva harus diatas 100</p>

			<h3>Expired Zenziva</h3>
			<h1>{{ $zenziva_expired->format('d M Y') }} ({{ $time_left }})</h1>
				<p>Expired Zenziva harus diatas 10 hari</p>

			<h3>Credit Vultr</h3>
			<h1>$ {{ abs( $vultr['balance'] + $vultr['pending_charges'] ) }} </h1>
				<p>Credit Vultr harus diatas $15</p>
			<div class="alert alert-info" role="alert">
				<h4>Jika tidak dipenuhi maka halaman ini tidak bisa disubmit</h4>
			</div>
		</div>
	</div>
</div>
