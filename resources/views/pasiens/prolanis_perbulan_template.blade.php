<div class="table-responsive">
	<table class="table table-hover table-condensed table-bordered">
		<thead>
			<tr>
				<th>Tanggal</th>
				<th>Nama</th>
				<th>Nomor Asuransi</th>
				<th>Tanggal Lahir</th>
				<th>Usia</th>
				<th>Alamat</th>
				<th>Pembayaran</th>
				<th>Tekanan Darah</th>
				<th>Gula Darah</th>
			</tr>
		</thead>
		<tbody>
			@if(count($$prolanis) > 0)
				@foreach($$prolanis as $p)
					<tr
						@if( $prolanis == 'prolanis_ht' && App\Classes\Yoga::htTerkendali($p) && !empty($p['sistolik']) )
							class="success"
						@endif
						>
						<td>{{ $p['tanggal'] }}</td>
						<td>{{ ucwords($p['nama']) }}</td>
						<td>{{ $p['nomor_asuransi'] }}</td>
						<td>{{ $p['tanggal_lahir'] }}</td>
						<td>{{ App\Classes\Yoga::umurSaatPeriksa($p['tanggal_lahir'], $p['tanggal']) }}</td>
						<td>{{ $p['alamat'] }}</td>
						<td>{{ $p['nama_asuransi'] }}</td>
						<td nowrap>{{ $p['sistolik'] }} / {{ $p['diastolik'] }}</td>
						<td>
							@if( isset( $p['gula_darah'] ) )
							{{ $p['gula_darah'] }}
							@endif
						</td>
					</tr>
				@endforeach
			@else
				<tr>
					<td colspan="4" class="text-center">Tidak ada data </td>
				</tr>
			@endif
		</tbody>
	</table>
</div>
