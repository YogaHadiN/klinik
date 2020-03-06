@extends('layout.master')

@section('title') 
	Klinik Jati Elok | Rekening Bank {{ $rekenings->first()->akun }}

@stop
@section('page-title') 
<h2>Rekening Bank {{ $rekenings->first()->akun }}</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Rekening Bank {{ $rekenings->first()->akun_bank->akun }}</strong>
	  </li>
</ol>
@stop
@section('content') 
	{!! Form::text('akun_bank_id', $rekenings->first()->akun_bank_id, ['class' => 'form-control hide', 'id' => 'akun_bank_id']) !!}
	<div class="table-responsive">
		{{ $rekenings->links() }}
		<table class="table table-hover table-condensed table-bordered">
			<thead>
				<tr>
					<th nowrap>
						Tanggal
                        {!! Form::text('tanggal', null, [
							'class' => 'form-control-inline tgl form-control ajaxsearchrekening',
							'onkeyup' => 'search(this);return false;',
							'id'    => 'tanggal'
						])!!}
					</th>
					<th>
						Deskripsi
                        {!! Form::text('deskripsi', null, [
							'class' => 'form-control-inline deskripsi form-control ajaxsearchrekening',
							'onkeyup' => 'search(this);return false;',
							'id' => 'deskripsi'
						])!!}
					</th>
					<th nowrap>
						Kredit
					</th>
					<th nowrap>
						Saldo
					</th>
				</tr>
			</thead>
			<tbody id="rek_container">
				@if($rekenings->count() > 0)
					@foreach($rekenings as $rekening)
						<tr>
							<td nowrap>{{ $rekening->tanggal->format('Y-m-d') }}</td>
							<td>{{ $rekening->deskripsi }}</td>
							@if( !$rekening->debet )
								<td nowrap class="text-right">{{ App\Classes\Yoga::buatrp( $rekening->nilai ) }}</td>
							@else
								<td nowrap class="text-right">{{ App\Classes\Yoga::buatrp('0') }}</td>
							@endif
							<td nowrap>{{ App\Classes\Yoga::buatrp( $rekening->saldo_akhir ) }}</td>
						</tr>
					@endforeach
				@else
					<tr>
						<td colspan="4" class="text-center">Tidak ada data untuk ditampilkan</td>
					</tr>
				@endif
			</tbody>
		</table>
		{{ $rekenings->links() }}
	</div>
@stop
@section('footer') 
	<script type="text/javascript" charset="utf-8">
		function search(control){
			 $.get(base + '/rekening_bank/search',
			 	{ 
					'tanggal':      $('#tanggal').val(),
					'akun_bank_id': $('#akun_bank_id').val(),
					'deskripsi':    $('#deskripsi').val()
				},
			 	function (data, textStatus, jqXHR) {
					var temp = '';
					for (var i = 0; i < data.length; i++) {
						temp += '<tr>';
						temp += '<td nowrap>';
						temp += data[i].tanggal;
						temp += '</td>';
						temp += '<td>';
						temp += data[i].deskripsi;
						temp += '</td>';
						temp += '<td class="text-right" nowrap>';
						temp += uang(data[i].nilai);
						temp += '</td>';
						temp += '<td class="text-right" nowrap>';
						temp += uang(data[i].saldo_akhir);
						temp += '</td>';
						temp += '</tr>';
					}
					 $('#rek_container').html(temp);
			 	}
			 );
		}
</script>
	
@stop

