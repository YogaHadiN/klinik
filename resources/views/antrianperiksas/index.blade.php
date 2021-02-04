@extends('layout.master')

@section('title') 
{{ env("NAMA_KLINIK") }} | Poli {!! ucfirst($poli) !!}

@stop
@section('page-title') 
<h2>Antrian Periksa</h2>
    <ol class="breadcrumb">
      <li>
          <a href="{{ url('laporans')}}">Home</a>
      </li>
      <li class="active">
          <strong>Poli {!! ucfirst($poli) !!} </strong>
      </li>
     </ol>
@stop
@section('content') 

<input type="hidden" name="_token" id="token" value="{{ Session::token() }}">
<div class="panel panel-primary">
      <div class="panel-heading">
            <div class="panel-title">
                <div class="panelLeft">
                    <h3>Belum Diperiksa</h3>
                </div>
                <div class="panelRight">
                    <h3>Total : {!! $antrianperiksa->count() !!}</h3>
                </div>
            </div>
      </div>
      <div class="panel-body">             
		  <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tableAsuransi">
                  <thead>
                    <tr>
						<th>Antrian</th>
                        <th>Tanggal</th>
                        <th>Poli</th>
                        <th>Pembayaran</th>
                        <th>Nama Pasien</th>
                        <th>Pemeriksa</th>
                        <th class="hide">pasien_id</th>
                        <th class="hide">id</th>
                        <th style="width:5px" nowrap>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($antrianperiksa->count() > 0)
                        @foreach ($antrianperiksa as $periksa)
                            <tr>
								@if($periksa->poli == 'estetika' && $periksa->periksaEx != null)
								<td> <a class="btn btn-xs btn-info" href="{{ url('periksa/'.$periksa->periksaEx->id . '/images') }}">Gambar</a> </td>
								@else
									<td class="nomor_antrian">
										@if(isset($periksa->antrian))
											{!! $periksa->antrian->nomor_antrian !!}
										@endif
									</td>
								@endif
								<td>{!! App\Classes\Yoga::updateDatePrep($periksa->tanggal) !!} </br>
									{!! $periksa->jam !!}
								</td>
                                <td>
                                    {!! Form::open(['url' => 'antrianperiksas/' . $periksa->id .'/editPoli', 'method' => 'put'])!!}
										{!! Form::select('poli', $poli_list, $periksa->poli, ['class' => 'form-control', 'onchange' => 'changePoli(this);return false;']) !!}
                                    {!! Form::close() !!}
								</td>
                                <td>{!! $periksa->asuransi->nama !!}</td>
                                <td class="nama_pasien">{!! $periksa->pasien->nama !!}</td>
								<td>
								{!! Form::select('staf_id', $staf_list, $periksa->staf_id, ['class' => 'form-control selectpick', 'data-live-search' => 'true', 'onchange' => 'changeStaf();return false;']) !!}
								
								</td>
                                {{--<td>{!! $periksa->staf->nama !!}</td>--}}
                                <td class="hide pasien_id">{!! $periksa->pasien_id !!}</td>
                                <td class="hide id">{!! $periksa->id !!}</td>

                                <td nowrap>
                                    {!! Form::open(['url' => 'antrianperiksas/' . $periksa->id, 'method' => 'delete'])!!}
										<a href="{!! URL::to('poli/' . $periksa->id)!!}" class="btn btn-success btn-xs">
											<span class="glyphicon glyphicon-log-in " aria-hidden="true"></span>
										</a>
                                        {!! Form::hidden('pasien_id', $periksa->pasien_id, ['class' => 'pasien_id'])!!}
                                        {!! Form::hidden('alasan', null, ['class' => 'alasan', 'id' => 'alasan' . $periksa->id])!!}
                                        <button type="button" class="btn btn-danger btn-xs" onclick="alasas_hapus(this);return false;">
											<span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
										</button>
                                        <button type="button" class="btn btn-info btn-xs" onclick="panggil(this);return false;">
											<span class="glyphicon glyphicon-volume-up" aria-hidden="true"></span>
										</button>
										@if( $periksa->asuransi_id == '32' && $periksa->antars->count() > 0 )		
											<a class="btn btn-primary btn-xs" href="{{ url('antrianperiksas/pengantar/' . $periksa->id . '/edit') }}">{!! $periksa->antars->count() !!} pengantar</a>
										@elseif( $periksa->asuransi_id == '32' && $periksa->antars->count() < 1 )
											<a class="btn btn-warning btn-xs" href="{{ url('antrianperiksas/pengantar/' . $periksa->id . '/edit') }}">Edit Pengantar</a>
										@endif
                                      {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">TIdak ada data untuk ditampilkan :p</td>
                        </tr>
                        {{-- false expr --}}
                    @endif
                    
                </tbody>
            </table>
		  	
		  </div>
      </div>
</div>
@if ($postperiksa->count() > 0)
    <div class="panel panel-success">
          <div class="panel-heading">
			<div class="panel-title">
				<div class="panelLeft">
					<h3>Sudah Diperiksa Pasien Belum Pulang</h3>
				</div>
				<div class="panelRight">
					<h3>Total : {!! $postperiksa->count() !!}</h3>
				</div>
			</div>
          </div>
          <div class="panel-body">             
			  <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tableAsuransi">
                      <thead>
                        <tr>
                            <th class="hide">periksa_id</th>
                            <th>Antrian</th>
                            <th>Nama Pasien</th>
                            <th>Pemeriksa</th>
                            <th>Pembayaran</th>
                            <th>Surat Sakit</th>
                            <th>Rujukan</th>
                            <th style="width:5px" nowrap>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach ($postperiksa as $periksa)
                                <tr>
                                    <td class="hide">{!! $periksa->id !!}</td>
									<td>
										@if( !is_null($periksa->antrian ) )
											{!! $periksa->antrian->nomor_antrian !!}
										@endif
									</td> 
                                    <td>{!! $periksa->pasien->nama !!}</td>
                                    <td>{!! $periksa->staf->nama !!}</td> 
                                    <td>{!! $periksa->asuransi->nama !!}</td>
                                    <td>
                                        @if(!$periksa->suratSakit)
                                        --
                                        @else 
                                            {!!App\Classes\Yoga::updateDatePrep($periksa->suratSakit->tanggal_mulai)!!} selama {!!$periksa->suratSakit->hari!!} hari
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$periksa->rujukan)
                                            --
                                        @else 
                                            {!! $periksa->rujukan->tujuanRujuk->tujuan_rujuk!!} <br>
                                            {!! $periksa->rujukan->complication!!}
                                        @endif
                                    </td>
                                    <td nowrap>
                                      {!! Form::open(['url' => 'update/kembali2/' . $periksa->id, 'method' => 'post'])!!}
                                        @if(!$periksa->suratSakit)
                                            <span>
                                                <button type="button" onclick="cekMasihAda(this);return false;" class="btn btn-success btn-sm">Buat Surat Sakit</button>
                                                <a href="{{ url('suratsakits/create/' . $periksa->id) }}" class="btn btn-success btn-sm rujukan hide">Buat Surat Sakit2</a>
                                            </span>
                                        @else
                                            <span>
                                                <button type="button" onclick="cekMasihAda(this);return false;" class="btn btn-warning btn-sm">Edit Surat Sakit</button>
                                                <a href="{{ url('suratsakits/' . $periksa->suratSakit->id . '/edit') }}" class="btn btn-warning btn-sm rujukan hide">Edit Surat Sakit2</a>
                                            </span>
                                        @endif
                                        @if(!$periksa->rujukan)
                                            <span>
                                                <button type="button" onclick="cekMasihAda(this);return false;" class="btn btn-success btn-sm">Buat Rujukan</button>
                                                <a href="{{ url('rujukans/create/' . $periksa->id) }}" class="btn btn-success btn-sm rujukan hide">Buat Rujukan2</a>
                                            </span>
                                        @else
                                            <span>
                                                <button type="button" onclick="cekMasihAda(this);return false;" class="btn btn-warning btn-sm">Edit Rujukan</button>
                                                <a href="{{ url('rujukans/' . $periksa->rujukan->id . '/edit') }}" class="btn btn-warning btn-sm rujukan hide">Edit Rujukan2</a>
                                            </span>
                                        @endif
                                        @if(!$periksa->kontrol)
                                            <span>
                                                <button type="button" onclick="cekMasihAda(this);return false;" class="btn btn-success btn-sm">Buat Janji Kontrol</button>
                                                <a href="{{ url('kontrols/create/' . $periksa->id) }}" class="btn btn-success btn-sm rujukan hide">Buat Rujukan2</a>
                                            </span>
                                        @else
                                            <span>
                                                <button type="button" onclick="cekMasihAda(this);return false;" class="btn btn-warning btn-sm">Edit Janji Kontrol</button>
                                                <a href="{{ url('kontrols/' . $periksa->kontrol->id . '/edit') }}" class="btn btn-warning btn-sm rujukan hide">Edit Rujukan2</a>
                                            </span>
                                        @endif
                                            <span>
                                                <button type="button" onclick="cekMasihAda(this);return false;" class="btn btn-danger btn-sm">Periksa Lagi</button>
                                            {!! Form::submit('Periksa Lagi2', ['class' => 'btn btn-danger btn-sm periksa hide', 'onclick' => 'return confirm("Anda Yakin mau mengembalikan ' . $periksa->pasien_id . ' - ' . $periksa->pasien->nama. ' ke ruang periksa?")']) !!}
                                            </span>
                                      {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
			  	
			  </div>
          </div>
    </div>
@endif
@include('antrianpolis.modalalasan', ['antrianperiksa' => 'fasilitas/antrianperiksa/destroy'])
@include('panggil')

@stop
@section('footer') 
    <script>
		function panggil(control){
			var nomor_antrian = $(control).closest('tr').find('.nomor_antrian').html().trim();
			$.get(base + '/poli/ajax/panggil_pasien',
				{ nomor_antrian: nomor_antrian },
				function (data, textStatus, jqXHR) {
					pglPasien(data);
				}
			);
		}
		function alasas_hapus(control){
			var id = $(control).closest('tr').find('.id').html()
			var pasien_id = $(control).closest('tr').find('.pasien_id').html()
			var nama_pasien = $(control).closest('tr').find('.nama_pasien').html()
			var onclick = 'modalAlasan(this' + ', "' + pasien_id + '", "' + nama_pasien + '"); return false;';

			$('#modal-alasan .id').val(id);
			$('#modal-alasan .pasien_id').val(pasien_id);
			$('#modal-alasan').modal('show');
			$('#modal-alasan .dummySubmit').attr('onclick', onclick);
		}

        function hapusSajalah(){
            var id = $('#alasan_id').val();
            var submit_id = $('#submit_id').val();
            console.log('id = ' + id);
            $('#' + id).val($('#alasan_textarea').val());
            $('#' + submit_id).click();
        }

        function cekMasihAda(control){
            var periksa_id = $(control).closest('tr').find('td:first-child').html();

            $.post('{{ url("antrianperiksas/ajax/cekada") }}', {'periksa_id': periksa_id }, function(data) {
                data = $.trim(data);
                if (data == '1') {
                    console.log('diterima');
                    var text = $(control).closest('span').find('.hide').html();
                    console.log('html = ' + text);
                    $(control).closest('span').find('.hide').get(0).click();
                } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text:'pasien sudah pulang'
                    });
                    location.reload();
                }
            });
        }
		function changePoli(control){
			if( $(control).val() != '' ){
				$(control).closest('form').submit();
			}
		}
    </script>
@stop
