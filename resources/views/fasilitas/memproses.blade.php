<div class="row">
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<div class="widget style1 yellow-bg">
			<div class="row">
				<div class="col-xs-4">
					<i class="fa fa-cogs fa-spin fa-5x"></i>
				</div>
				<div class="col-xs-8 text-right">
					<span>Memproses antrian</span>
					<h2 class="font-bold">{{ $antrian->nomor_antrian }}</h2>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
			<div class="table-responsive">
				<table class="table table-hover table-condensed table-bordered">
					<thead>
						<tr>
							<th>Nama</th>
							<th>Tanggal Lahir</th>
							<th>No Telp</th>
							<th>Pembayaran</th>
							<th>Nama Asuransi</th>
							<th>Nomor Asuransi</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{{ $antrian->whatsapp_registration->nama }}</td>
							<td>{{ $antrian->whatsapp_registration->tanggal_lahir }}</td>
							<td>{{ $antrian->whatsapp_registration->no_telp }}</td>
							<td>{{ $antrian->whatsapp_registration->pembayaran }}</td>
							<td>{{ $antrian->whatsapp_registration->nama_asuransi }}</td>
							<td>{{ $antrian->whatsapp_registration->nomor_bpjs }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			

	</div>
</div>
