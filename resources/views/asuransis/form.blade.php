<div class="panel panel-default">
    <div class="panel-body">
        <div role="tabpanel">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist" id="tab2panel">
                <li role="presentation" class="active">
                    <a href="#Asuransi" aria-controls="Asuransi" role="tab" data-toggle="tab">Asuransi</a>
                </li>
                <li role="presentation">
                    <a href="#Tarif" aria-controls="Tarif" role="tab" data-toggle="tab">Tarif</a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="Asuransi">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<div class="form-group @if($errors->has('nama'))has-error @endif">
									  {!! Form::label('nama', 'Nama Asuransi', ['class' => 'control-label', 'style' => 'text-align:left']) !!}
                                        {!! Form::text('nama', null, array(
                                            'class'         => 'form-control',
                                            'placeholder'   => 'Nama Asuransi'
                                            ))!!}
									  @if($errors->has('nama'))<code>{{ $errors->first('nama') }}</code>@endif
									</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="form-group @if($errors->has('alamat'))has-error @endif">
										  {!! Form::label('alamat', 'Alamat', ['class' => 'control-label']) !!}
                                            {!! Form::textarea('alamat', null, array(
                                                'class'         => 'form-control textareacustom',
                                                'placeholder'   => 'Alamat'
                                                ))!!}
										  @if($errors->has('alamat'))<code>{{ $errors->first('alamat') }}</code>@endif
										</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<div class="form-group @if($errors->has('umum'))has-error @endif">
											  {!! Form::label('umum', 'Umum', ['class' => 'control-label']) !!}
                                                {!! Form::textarea('umum', $umumstring, array(
                                                    'class'         => 'form-control textareacustom',
                                                    'placeholder'   => 'Umum'
                                                    ))!!}
											  @if($errors->has('umum'))<code>{{ $errors->first('umum') }}</code>@endif
											</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
												<div class="form-group @if($errors->has('gigi'))has-error @endif">
												  {!! Form::label('gigi', 'Gigi', ['class' => 'control-label']) !!}
                                                    {!! Form::textarea('gigi', $gigistring, array(
                                                        'class'         => 'form-control textareacustom',
                                                        'placeholder'   => 'Gigi'
                                                        ))!!}
												  @if($errors->has('gigi'))<code>{{ $errors->first('gigi') }}</code>@endif
												</div>
                                                </div>
                                            </div>
										<div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
												<div class="form-group @if($errors->has('gigi'))has-error @endif">
												  {!! Form::label('kata_kunci', 'Kata Kunci Transfer', ['class' => 'control-label']) !!}
                                                    {!! Form::text('kata_kunci', null, array(
                                                        'class'         => 'form-control',
                                                        'placeholder'   => 'Kata Kunci Transfer Bank'
                                                        ))!!}
												  @if($errors->has('kata_kunci'))<code>{{ $errors->first('kata_kunci') }}</code>@endif
												</div>
											</div>
										</div>
									</div>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<div class="row">
											<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
												<div class="form-group @if($errors->has('kali_obat'))has-error @endif">
												  {!! Form::label('kali_obat', 'Pengali Obat', ['class' => 'control-label']) !!}
												  {!! Form::text('kali_obat' , '1.25', ['class' => 'form-control']) !!}
												  @if($errors->has('kali_obat'))<code>{{ $errors->first('kali_obat') }}</code>@endif
												</div>
											</div>
											<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
												<div class="form-group @if($errors->has('no_telp'))has-error @endif">
												  {!! Form::label('no_telp', 'Nomor Telepon', ['class' => 'control-label']) !!}
													{!! Form::text('no_telp', null, array(
														'class'         => 'form-control',
														'placeholder'   => 'No Telp'
														))!!}
												  @if($errors->has('no_telp'))<code>{{ $errors->first('no_telp') }}</code>@endif
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
												<div class="form-group @if($errors->has('tipe_asuransi'))has-error @endif">
												  {!! Form::label('tipe_asuransi', 'Tipe Asuransi', ['class' => 'control-label']) !!}
													{!! Form::select('tipe_asuransi',array(
														null => '- Tipe Asuransi -',
														'1' => 'Admedika',
														'2' => 'Kapitasi',
														'3' => 'Perusahaan',
														'4' => 'Flat',
														'5' => 'BPJS',
														), null, array(
														'class'         => 'form-control',
														'placeholder'   => 'tipe_asuransi'
														))!!}
												  @if($errors->has('tipe_asuransi'))<code>{{ $errors->first('tipe_asuransi') }}</code>@endif
												</div>
											</div>
											<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
												<div class="form-group @if($errors->has('tanggal_berakhir'))has-error @endif">
												  {!! Form::label('tanggal_berakhir', 'Tangggal Berakhir', ['class' => 'control-label']) !!}
													{!! Form::text('tanggal_berakhir', $tanggal, array(
														'class'         => 'form-control tanggal',
														'placeholder'   => 'tanggal_berakhir'
														))!!}
												  @if($errors->has('tanggal_berakhir'))<code>{{ $errors->first('tanggal_berakhir') }}</code>@endif
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
												<div class="form-group @if($errors->has('penagihan'))has-error @endif">
												  {!! Form::label('penagihan', 'Penagihan', ['class' => 'control-label']) !!}
													{!! Form::textarea('penagihan', $penagihanstring, array(
														'class'         => 'form-control textareacustom',
														'placeholder'   => 'penagihan'
														))!!}
												  @if($errors->has('penagihan'))<code>{{ $errors->first('penagihan') }}</code>@endif
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
												<div class="form-group @if($errors->has('rujukan'))has-error @endif">
												  {!! Form::label('rujukan', 'Rujukan', ['class' => 'control-label']) !!}
													{!! Form::textarea('rujukan', $rujukanstring, array(
														'class'         => 'form-control textareacustom',
														'placeholder'   => 'Rujukan'
														))!!}
												  @if($errors->has('rujukan'))<code>{{ $errors->first('rujukan') }}</code>@endif
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											  {!! Form::label('pics', 'PIC', ['class' => 'control-label']) !!}
												<div class="table-responsive">
													<table class="table table-hover table-condensed table-bordered" id="table_pic">
														<tbody>
															@if( isset($asuransi) && isset($asuransi->pic) )
																@foreach($asuransi->pic as $k => $pic)	
																	<tr>
																		<td>
																			<div class="form-group">
																				{!! Form::text('pic[]', $pic->nama, array(
																					'class'         => 'form-control pic',
																					'placeholder'   => 'nama'
																				))!!}
																			</div>
																		</td>
																		<td>
																			<div class="form-group">
																				{!! Form::text('hp_pic[]', $pic->nomor_telepon, array(
																					'class'         => 'form-control hp',
																					'placeholder'   => 'nomor handphone'
																				))!!}
																			</div>
																		</td>
																		<td class="column-fit">
																			@if( $k == $asuransi->pic->count() - 1 && $asuransi->pic->count() > 1 )
																				<button type="button" class="btn btn-primary" onclick="tambahInput(this); return false;">
																					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
																				</button>
																				&nbsp
																				<button type="button" class="btn btn-danger" onclick="kurangInput(this); return false;">
																					<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
																				</button>
																			@elseif( $k == $asuransi->pic->count() - 1 && $asuransi->pic->count() == 1   )
																				<button type="button" class="btn btn-primary" onclick="tambahInput(this); return false;">
																					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
																				</button>
																			@else
																				<button type="button" class="btn btn-danger" onclick="kurangInput(this); return false;">
																					<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
																				</button>
																			@endif
																		</td>
																	</tr>
																@endforeach
																@else
																	<tr>
																		<td>
																			<div class="form-group">
																				{!! Form::text('pic[]',null, array(
																					'class'         => 'form-control pic',
																					'placeholder'   => 'nama'
																				))!!}
																			</div>
																		</td>
																		<td>
																			<div class="form-group">
																				{!! Form::text('hp_pic[]', null, array(
																					'class'         => 'form-control hp',
																					'placeholder'   => 'nomor handphone'
																				))!!}
																			</div>
																		</td>
																		<td class="column-fit">
																			<button type="button" class="btn btn-primary" onclick="tambahInput(this); return false;">
																				<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
																			</button>
																		</td>
																	</tr>
																@endif
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											  {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
												<div class="table-responsive">
													<table class="table table-hover table-condensed table-bordered" id="table_email">
														<tbody>
															@if( isset($asuransi) && isset($asuransi->email) )
																@foreach($asuransi->email as $k => $email)	
																	<tr>
																		<td>
																			<div class="form-group">
																				{!! Form::text('email[]', null, array(
																					'class'         => 'form-control hp',
																					'placeholder'   => 'email'
																				))!!}
																			</div>
																		</td>
																		<td class="column-fit">
																			<button type="button" class="btn btn-primary" onclick="tambahInput(this); return false;">
																				<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
																			</button>
																		</td>
																	</tr>
																	<tr>
																		<td>
																			<div class="form-group">
																				{!! Form::text('email[]', $email->email, array(
																					'class'       => 'form-control hp',
																					'placeholder' => 'email'
																				))!!}
																			</div>
																		</td>
																		<td class="column-fit">
																			@if( $k == $asuransi->email->count() - 1 && $asuransi->email->count() > 1 )
																				<button type="button" class="btn btn-primary" onclick="tambahInput(this); return false;">
																					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
																				</button>
																				&nbsp
																				<button type="button" class="btn btn-danger" onclick="kurangInput(this); return false;">
																					<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
																				</button>
																			@elseif( $k == $asuransi->email->count() - 1 && $asuransi->email->count() == 1   )
																				<button type="button" class="btn btn-primary" onclick="tambahInput(this); return false;">
																					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
																				</button>
																			@else
																				<button type="button" class="btn btn-danger" onclick="kurangInput(this); return false;">
																					<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
																				</button>
																			@endif
																		</td>
																	</tr>
																@endforeach
																@else
																	<tr>
																		<td>
																			<div class="form-group">
																				{!! Form::text('email[]', null, array(
																					'id'         => 'email',
																					'class'         => 'form-control hp',
																					'placeholder'   => 'email'
																				))!!}
																			</div>
																		</td>
																		<td class="column-fit">
																			<button type="button" class="btn btn-primary" onclick="tambahInput(this); return false;">
																				<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
																			</button>
																		</td>
																	</tr>
																@endif
														</tbody>
													</table>
												</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<div role="tabpanel" class="tab-pane" id="Tarif">
								<div class="panel panel-info">
									<div class="panel-heading">
										<h3>TARIF</h3>
									</div>
									<div class="panel-body">
										<!-- Table -->
										<div class="table-responsive">
											<table class="table table-condensed table-bordered DT">
												<thead>
													<tr>
														<th>Jenis Tarif</th>
														<th>Biaya</th>
														<th>Jasa Dokter</th>
														<th>Tipe Tindakan</th>
														<th>Action</th>
														<th class="hide">id</th>
													</tr>
												</thead>
												<tbody id="tblTarif">
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				{!! Form::submit($submit, array(
					'class' => 'btn btn-primary block full-width m-b'
					))!!}
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				{!! HTML::link('asuransis', 'Cancel', ['class' => 'btn btn-danger btn-block'])!!}
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				{!! Form::textarea('tarifs', $tarifs, ['class' => 'form-control hide', 'id' => 'tarifs'])!!}
			</div>
		</div>
<script type="text/javascript" charset="utf-8">
</script>
