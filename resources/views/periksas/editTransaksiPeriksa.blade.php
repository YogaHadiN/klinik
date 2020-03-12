@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Edit Transaksi Periksa

@stop
@section('page-title') 
<h2>Edit Transaksi Periksa</h2>
<ol class="breadcrumb">
	  <li>
		  <a href="{{ url('laporans')}}">Home</a>
	  </li>
	  <li class="active">
		  <strong>Edit Transaksi Periksa</strong>
	  </li>
</ol>
@stop
@section('content') 
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="panel panel-info">
							<div class="panel-heading">
								<div class="panel-title">
									<div class="panelLeft">
										Edit Transaksi Periksa
									</div>
								</div>
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-hover table-condensed">
										<thead>
											<tr>
												<th class="hide id">id</th>
												<th>Jenis Tarif</th>
												<th>Biaya</th>
											</tr>
										</thead>
										<tbody>
											@if($periksa->transaksii->count() > 0)
												@foreach($periksa->transaksii as $k => $t)
													<tr>
														<td class='hide id'>{{ $t->id }}</td>
														<td class='jenis_tarif'>{{ $t->jenisTarif->jenis_tarif }}</td>
														<td>
														   {!! Form::text('nilai', $t->biaya, [
															   'class' => 'form-control uangInput text-right',
															   'title' => $k,
															   'onkeyup' => 'transaksiPeriksa(this);return false;'
														   ]) !!}
													   </td>
													</tr>
												@endforeach
											@else
												<tr>
													<td class="text-center" colspan="4">Tidak Ada Data Untuk Ditampilkan :p</td>
												</tr>
											@endif
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<div class="panelLeft">
									<div class="panel-title">Informasi</div>
								</div>
								<div class="panelRight">
								</div>
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-hover table-condensed table-bordered">
										<tbody>
											<tr>
												<td>Periksa Id</td>
												<td>{{ $periksa->id }}</td>
											</tr>
											<tr>
												<td>Nama Pasien</td>
												<td>{{ $periksa->pasien_id }}-{{ $periksa->pasien->nama }}</td>
											</tr>
											<tr>
												<td>Pembayaran</td>
												<td>{{ $periksa->asuransi->nama }}</td>
											</tr>
											<tr>
												<td>Nama Dokter</td>
												<td>{{ $periksa->staf->nama }}</td>
											</tr>
											<tr>
												<td>Tanggal</td>
												<td>{{ $periksa->created_at->format('d M Y') }}</td>
											</tr>
											<tr>
												<td>Jam</td>
												<td>{{ $periksa->created_at->format('H:i:s') }}</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<div class="panel-title">
							<div class="panelLeft">
								Edit Tunai / Piutang
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-hover table-condensed">
								<thead>
									<tr>
										<th>Pembayaran</th>
										<th>Nilai</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td> Tunai </td>
										<td id="tunai">
										   {!! Form::text('tunai', $periksa->tunai, [
											   'class' => 'form-control uangInput text-right tunai',
											   'onkeyup' => 'periksaKeyUp(this, "tunai");return false;'
										   ]) !!}
									   </td>
									</tr>
									<tr>
										<td>Piutang</td>
										<td id="piutang">
										   {!! Form::text('piutang', $periksa->piutang, [
											   'class' => 'form-control uangInput text-right piutang',
											   'onkeyup' => 'periksaKeyUp(this, "piutang");return false;'
										   ]) !!}
									   </td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="alert alert-danger">
						<h2>Peringatan</h2>
						<p>Halaman ini hanya untuk Super Admin atau Manager</p>
						<p>Jika Anda bukan termasuk diantara keduanya Harap segera logout</p>
					</div>
					<div class="alert alert-info">
						<h2>Petunjuk</h2>
						<ul>
							<li>Jumlah nilai pada kolom debit dan kolom kredit pada <strong>Jurnal Umum</strong>  harus sama (seimbang)</li>
							<li>Jumlah nilai seluruh baris pada <strong>Transaksi Periksa</strong> dan <strong>Edit Tunai Piutang</strong> harus sama</li>
							<li>Jumlah tersebut harus sama dengan <strong>jumlah harta</strong> yang diterima pada transaksi ini</li>
							<li>Harta yang dimaksud pada halaman ini adalah
								<ul>
									@foreach( $periksa->jurnals as $j )
										@if( substr( $j->coa_id, 0, 2 ) == '11' && $j->debit == 1  )
											<li>{{ $j->coa->coa }}</li>
										@endif
									@endforeach
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="panelLeft">
					Jurnal Umum
				</div>
			</div>
			<div class="panel-body">
				@include('jurnal_umums.templateJurnal', [
					'jurnals' => $periksa->jurnals,
					'delete' => true,
					'count' => $periksa->jurnals->count()
					])
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
		@include('jurnal_umums.formManualInput')
	</div>
</div>

{!! Form::open(['url' => 'periksas/' . $periksa->id . '/update/transaksiPeriksa', 'method' => 'post']) !!}

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group @if($errors->has('nomor_asuransi'))has-error @endif">
			  {!! Form::label('nomor_asuransi', 'Nomor Asuransi', ['class' => 'control-label']) !!}
			  {!! Form::text('nomor_asuransi', $periksa->nomor_asuransi, array(
					'class'         => 'form-control rq'
				))!!}
			  @if($errors->has('nomor_asuransi'))<code>{{ $errors->first('nomor_asuransi') }}</code>@endif
			</div>
		</div>
	</div>
	<div class="hide">
		<h2>Temp</h2>
		{!! Form::textarea('temp', '[]', ['class' => 'form-control textareacustom', 'id' => 'temp']) !!}
	</div>
	<div class="hide">
		<h2>Jurnals</h2>
		{!! Form::textarea('jurnals', $periksa->jurnals, ['class' => 'form-control textareacustom', 'id' => 'jurnals']) !!}
	</div>
	<div class="hide">
		<h2>Transaksis</h2>
		{!! Form::textarea('transaksis', $periksa->transaksii, ['class' => 'form-control textareacustom', 'id' => 'transaksis']) !!}
	</div>
	<div class="hide">
		<h2>Periksa</h2>
		{!! Form::textarea('periksa', $periksa, ['class' => 'form-control textareacustom', 'id' => 'periksa']) !!}
	</div>
	<div class="row">
		<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<button class="btn btn-lg btn-block btn-success" type="button" onclick='dummySubmit(this);return false;'>Submit</button>
					{!! Form::submit('Submit', ['class' => 'btn btn-block btn-success hide', 'id' => 'submit']) !!}
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<a class="btn btn-lg btn-danger btn-block" href="{{ url('laporans') }}">Cancel</a>
				</div>
			</div>
		</div>
	</div>
{!! Form::close() !!}
@stop

@section('footer') 
<script src="{!! asset('js/jurnalManual.js') !!}"></script>
<script type="text/javascript" charset="utf-8">

	temp = parseTemp();
	render(temp);
	function dummySubmit(control){

		var kredit            = hitung().kredit;
		var debit             = hitung().debit;
		var biaya             = hitung().biaya;
		var total_periksa     = hitung().total_periksa;
		var total_harta_masuk = hitung().total_harta_masuk;

		if( 
			kredit == debit &&
			biaya == total_periksa &&
			biaya == total_harta_masuk
		){
			if(validatePass2(control)){
				$('#submit').click();
			}
		} else {
			if( kredit != debit ){
				alert('Jumlah Debit = ' + uang( debit ) + ', kredit = ' + uang( kredit ) + ' , HARUS SAMA!');
			}
			if( biaya != total_periksa  || biaya != total_harta_masuk){
				alert('Jumlah Biaya = ' + uang( biaya ) + ', Total Periksa = ' + uang( total_periksa ) + ' Total Harta Yang Masuk = ' + uang( total_harta_masuk )+ ', HARUS SAMA!');
			}
		}
		
	}
	function nilaiTransaksi(control){
		var nilai = cleanUang( $(control).val() );
		var key = $(control).attr('title');
		var jurnals = $('#jurnals').val();
		jurnals = JSON.parse(jurnals);
		jurnals[key]['biaya'] = nilai;
		jurnals = JSON.stringify(jurnals);
		$('#jurnlas').val(jurnals);
		$('#debit_total').html(hitung().debit);
		$('#kredit_total').html(hitung().kredit);
	}
	
	function periksaKeyUp(control, tipe){
		var nilai = cleanUang( $(control).val() );
		var periksa = $('#periksa').val();
		periksa = JSON.parse(periksa);
		periksa[tipe] = nilai;
		periksa = JSON.stringify(periksa);
		$('#periksa').val(periksa);
		$('#periksa_total').html(hitung().total_periksa);
	}
	function coaChange(control){
		 var key   = parseInt( $(control).closest('tr').find('.key').html() );
		var coa_id = $(control).val();
		var data = $('#jurnals').val();
		data = JSON.parse(data);
		 data[key]['coa_id'] = coa_id;
		 data = JSON.stringify(data);
		 $('#jurnals').val(data);
	}

	function nilaiKeyUp(control){
		 var key   = parseInt( $(control).closest('tr').find('.key').html() );
		var nilai = cleanUang( $(control).val() );
		var data = $('#jurnals').val();
		data = JSON.parse(data);
		 data[key]['nilai'] = parseInt( nilai );
		 data = JSON.stringify(data);
		 $('#jurnals').val(data);
	}
	function transaksiPeriksa(control){

		var nilai = cleanUang( $(control).val() );
		var key = $(control).attr('title');

		var transaksis = $('#transaksis').val();
		transaksis = JSON.parse(transaksis);
		transaksis[key].biaya = nilai;
		transaksis = JSON.stringify( transaksis );
		$('#transaksis').val(transaksis);
		$('#biaya_total').html(hitung().biaya);
	}
	function hitung(){
			var jurnals = $('#jurnals').val();
			jurnals = JSON.parse(jurnals);
			var temp = $('#temp').val();
			temp = JSON.parse(temp);
			var transaksis = $('#transaksis').val();
			transaksis = JSON.parse(transaksis);
			var periksa = $('#periksa').val();
			periksa = JSON.parse(periksa);

			var debit = 0;
			var kredit = 0;
			var total_harta_masuk = 0;
			 for (var i = 0; i < jurnals.length; i++) {
				 if( jurnals[i].debit == '1' ){
					debit += parseInt( jurnals[i].nilai );
				 } else {
					kredit += parseInt( jurnals[i].nilai );
				 }
				 if( jurnals[i].coa_id.substring(0,2) == '11' && jurnals[i].debit == '1' ){
					 total_harta_masuk += parseInt( jurnals[i].nilai );
				 }
			 }
			 for (var i = 0; i < temp.length; i++) {
				 if( temp[i].debit == '1' ){
					debit += parseInt( temp[i].nilai );
				 } else {
					kredit += parseInt( temp[i].nilai );
				 }
				 if( temp[i].coa_id.substring(0,2) == '11' && temp[i].debit == '1' ){
					 total_harta_masuk += parseInt( temp[i].nilai );
				 }
			 }
			var biaya = 0;
			 for (var i = 0; i < transaksis.length; i++) {
				biaya += parseInt( transaksis[i].biaya );
			 }
			var total_periksa = parseInt( periksa.tunai ) + parseInt( periksa.piutang );

		return {
			'kredit' : kredit,
			'debit' : debit,
			'biaya' : biaya,
			'total_periksa' : total_periksa,
			'total_harta_masuk' : total_harta_masuk
		};
	}
	function stringifyJurnal(data){
		 data = JSON.stringify(data);
		 $('#jurnals').val(data);
	}
	function delJurnal(control){

		var jurnals = $('#jurnals').val();
		jurnals = JSON.parse(jurnals);

		var id = $(control).closest('tr').find('.id').html();

		 for (var i = 0; i < jurnals.length; i++) {
			if( jurnals[i].id == id ){
				break;
			}
		 }

		jurnals.splice(i, 1);
		jurnals = JSON.stringify(jurnals);
		$('#jurnals').val(jurnals);
		$(control).closest('tr').remove();

	}

</script>
@stop
	
