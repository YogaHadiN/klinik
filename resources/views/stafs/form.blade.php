<div class="row">
     <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="form-group @if($errors->has('nama'))has-error @endif">
					  {!! Form::label('nama', 'Nama', ['class' => 'control-label']) !!}
                        {!! Form::text('nama', null, array(
                            'class'         => 'form-control',
                            'placeholder'   => 'Ketik nama tanpa gelar'
                        ))!!}
					  @if($errors->has('nama'))<code>{{ $errors->first('nama') }}</code>@endif
					</div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="form-group @if($errors->has('alamat_domisili'))has-error @endif">
					  {!! Form::label('alamat_domisili', 'Alamat Domisili', ['class' => 'control-label']) !!}
                        {!! Form::textarea('alamat_domisili', null, array(
                            'class'         => 'textareacustom form-control',
                            'placeholder'   => 'Alamat'
                        ))!!}
					  @if($errors->has('alamat_domisili'))<code>{{ $errors->first('alamat_domisili') }}</code>@endif
					</div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="form-group @if($errors->has('tanggal_lahir'))has-error @endif">
					  {!! Form::label('tanggal_lahir', 'Tanggal Lahir', ['class' => 'control-label']) !!}
                        {!! Form::text('tanggal_lahir', $tanggal_lahir, array(
                            'class'         => 'form-control tanggal',
                            'placeholder'   => 'Tanggal Lahir'
                            ))!!}
					  @if($errors->has('tanggal_lahir'))<code>{{ $errors->first('tanggal_lahir') }}</code>@endif
					</div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="form-group @if($errors->has('ktp'))has-error @endif">
					  {!! Form::label('ktp', 'KTP', ['class' => 'control-label']) !!}
                        {!! Form::text('ktp', null, array(
                            'class'         => 'form-control',
                            'placeholder'   => 'No KTP'
						))!!}
					  @if($errors->has('ktp'))<code>{{ $errors->first('ktp') }}</code>@endif
					</div>
                </div>
            </div>
            <div class="row">
                 <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					 <div class="form-group @if($errors->has('email'))has-error @endif">
					   {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                        {!! Form::email('email', null, array(
                            'class'         => 'form-control',
                            'placeholder'   => 'email'
                        ))!!}
					   @if($errors->has('email'))<code>{{ $errors->first('email') }}</code>@endif
					 </div>
                </div>
                 <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					 <div class="form-group @if($errors->has('no_telp'))has-error @endif">
					   {!! Form::label('no_telp', 'No Telp', ['class' => 'control-label']) !!}
                        {!! Form::text('no_telp', null, array(
                            'class'         => 'form-control',
                            'placeholder'   => 'Nomor Telepon'
                        ))!!}
					   @if($errors->has('no_telp'))<code>{{ $errors->first('no_telp') }}</code>@endif
					 </div>
                </div>
            </div>
     <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			 <div class="form-group @if($errors->has('alamat_ktp'))has-error @endif">
			   {!! Form::label('alamat_ktp', 'Alamat KTP', ['class' => 'control-label']) !!}
				{!! Form::textarea('alamat_ktp', null, array(
					'class'         => 'textareacustom form-control',
					'placeholder'   => 'Alamat KTP'
				))!!}
			   @if($errors->has('alamat_ktp'))<code>{{ $errors->first('alamat_ktp') }}</code>@endif
			 </div>
        </div>
    </div>
  <div class="row">
     <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		 <div class="form-group @if($errors->has('str'))has-error @endif">
		   {!! Form::label('str', 'STR', ['class' => 'control-label']) !!}
            {!! Form::text('str', null, array(
                'class'         => 'form-control',
                'placeholder'   => 'STR'
            ))!!}
		   @if($errors->has('str'))<code>{{ $errors->first('str') }}</code>@endif
		 </div>
    </div>
     <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		 <div class="form-group @if($errors->has('universitas_asal'))has-error @endif">
		   {!! Form::label('universitas_asal', 'Universitas Asal', ['class' => 'control-label']) !!}
            {!! Form::text('universitas_asal', null, array(
                'class'         => 'form-control',
                'placeholder'   => 'Universitas Asal'
            ))!!}
		   @if($errors->has('universitas_asal'))<code>{{ $errors->first('universitas_asal') }}</code>@endif
		 </div>
    </div>
</div>
  <div class="row">
     <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		 <div class="form-group @if($errors->has('titel'))has-error @endif">
		   {!! Form::label('titel', 'Titel', ['class' => 'control-label']) !!}
            {!! Form::select('titel', array(
                ''      =>   '(tidak ada titel)',
                'dr'    => 'Dokter',
                'drg'   => 'Dokter Gigi',
                'bd'    => 'Bidan',
                'ns'    => 'Perawat'
                ), null, array(
                'class'         => 'form-control',
                'placeholder'   => 'Titel'
            ))!!}
		   @if($errors->has('titel'))<code>{{ $errors->first('titel') }}</code>@endif
		 </div>
    </div>
     <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		 <div class="form-group @if($errors->has('no_hp'))has-error @endif">
		   {!! Form::label('no_hp', 'No HP', ['class' => 'control-label']) !!}
            {!! Form::text('no_hp', null, array(
                'class'         => 'form-control',
                'placeholder'   => 'Nomor HP'
            ))!!}
		   @if($errors->has('no_hp'))<code>{{ $errors->first('no_hp') }}</code>@endif
		 </div>
    </div>
</div>
  <div class="row">
     <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		 <div class="form-group @if($errors->has('tanggal_lulus'))has-error @endif">
		   {!! Form::label('tanggal_lulus', 'Tanggal Lulus', ['class' => 'control-label']) !!}
            {!! Form::text('tanggal_lulus', $tanggal_lulus, array(
                'class'         => 'form-control tanggal',
                'placeholder'   => 'Tanggal Lulus'
            ))!!}
		   @if($errors->has('tanggal_lulus'))<code>{{ $errors->first('tanggal_lulus') }}</code>@endif
		 </div>
    </div>
     <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		 <div class="form-group @if($errors->has('tanggal_mulai'))has-error @endif">
		   {!! Form::label('tanggal_mulai', 'Tanggal Mulai', ['class' => 'control-label']) !!}
            {!! Form::text('tanggal_mulai', $tanggal_mulai, array(
                'class'         => 'form-control tanggal',
                'placeholder'   => 'Tanggal Mulai'
            ))!!}
		   @if($errors->has('tanggal_mulai'))<code>{{ $errors->first('tanggal_mulai') }}</code>@endif
		 </div>
    </div>
</div>
<div class="row">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div class="form-group @if($errors->has('menikah'))has-error @endif">
			{!! Form::label('menikah', 'Status Pernikahan', ['class' => 'control-label']) !!}
			{!! Form::select('menikah', App\Classes\Yoga::statusMenikahList(), null, array(
				'class'         => 'form-control rq'
			))!!}
		  @if($errors->has('menikah'))<code>{{ $errors->first('menikah') }}</code>@endif
		</div>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div class="form-group @if($errors->has('jumlah_anak'))has-error @endif">
			{!! Form::label('jumlah_anak', 'Jumlah Anak', ['class' => 'control-label']) !!}
			{!! Form::text('jumlah_anak', null, array(
				'class'         => 'form-control rq'
			))!!}
		  @if($errors->has('jumlah_anak'))<code>{{ $errors->first('jumlah_anak') }}</code>@endif
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div class="form-group @if($errors->has('no_npwp'))has-error @endif">
			{!! Form::label('npwp', 'Nomor NPWP', ['class' => 'control-label']) !!}
			{!! Form::text('npwp', null, array(
				'class'         => 'form-control rq'
			))!!}
		  @if($errors->has('no_npwp'))<code>{{ $errors->first('no_npwp') }}</code>@endif
		</div>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div class="form-group @if($errors->has('jenis_kelamin'))has-error @endif">
			{!! Form::label('jenis_kelamin', 'Jenis Kelamin', ['class' => 'control-label']) !!}
			{!! Form::select('jenis_kelamin', App\Classes\Yoga::jenisKelaminList(), null, array(
				'class'         => 'form-control rq'
			))!!}
		  @if($errors->has('jenis_kelamin'))<code>{{ $errors->first('jenis_kelamin') }}</code>@endif
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="form-group @if($errors->has('ada_penghasilan_lain'))has-error @endif">
			{!! Form::label('ada_penghasilan_lain', 'Ada PenghasilanLain', ['class' => 'control-label']) !!}
			{!! Form::select('ada_penghasilan_lain', App\Classes\Yoga::pilihan('Ada Penghasilan Lain'), null, array(
				'class'         => 'form-control rq'
			))!!}
		  @if($errors->has('ada_penghasilan_lain'))<code>{{ $errors->first('ada_penghasilan_lain') }}</code>@endif
		</div>
	</div>
</div>
{{-- @if( isset($staf) ) --}}
{{-- 	@include('asuransis.upload', ['asuransi' => $staf, 'models' => 'stafs', 'folder' => 'staf']) --}}
{{-- @endif --}}
@if( \Auth::user()->id == '28' && isset( $staf ) )
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="panel-title">Daftar Gaji</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-hover table-condensed">
							<thead>
								<tr>
									<th>Tanggal</th>
									<th>Periode</th>
									<th>Gaji Pokok</th>
									<th>Bonus</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								@if($staf->gaji->count() > 0)
									@foreach($staf->gaji as $gaji)
										<tr>
											<td>{{ $gaji->tanggal_dibayar->format('d-m-Y') }}</td>
											<td class="text-right">{{ $gaji->mulai->format('M-Y') }}</td>
											<td class="text-right">{{ App\Classes\Yoga::buatrp($gaji->gaji_pokok )}}</td>
											<td class="text-right">{{ App\Classes\Yoga::buatrp($gaji->bonus) }}</td>
											<td class="text-right strong">{{ App\Classes\Yoga::buatrp($gaji->bonus + $gaji->gaji_pokok ) }}</td>
										</tr>
									@endforeach
								@else
									<tr>
										<td class="text-center" colspan="5">Tidak Ada Data Untuk Ditampilkan :p</td>
									</tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@endif
</div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
					{!! Form::label('image', 'Upload Pas Foto') !!}
					{!! Form::file('image') !!}
						@if (isset($staf) && $staf->image)
							<p> {!! HTML::image(asset($staf->image), null, ['class'=>'img-rounded upload']) !!} </p>
						@else
							<p> {!! HTML::image(asset('img/photo_not_available.png'), null, ['class'=>'img-rounded upload']) !!} </p>
						@endif
					{!! $errors->first('image', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group{{ $errors->has('ktp_image') ? ' has-error' : '' }}">
					{!! Form::label('ktp_image', 'Upload Gambar KTP') !!}
					{!! Form::file('ktp_image') !!}
						@if (isset($staf) && $staf->ktp_image)
							<p> {!! HTML::image(asset($staf->ktp_image), null, ['class'=>'img-rounded upload']) !!} </p>
						@else
							<p> {!! HTML::image(asset('img/photo_not_available.png'), null, ['class'=>'img-rounded upload']) !!} </p>
						@endif
					{!! $errors->first('ktp_image', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group{{ $errors->has('str_image') ? ' has-error' : '' }}">
					{!! Form::label('str_image', 'Upload Gambar STR') !!}
					{!! Form::file('str_image') !!}
						@if (isset($staf) && $staf->str_image)
							<p> {!! HTML::image(asset($staf->str_image), null, ['class'=>'img-rounded upload']) !!} </p>
						@else
							<p> {!! HTML::image(asset('img/photo_not_available.png'), null, ['class'=>'img-rounded upload']) !!} </p>
						@endif
					{!! $errors->first('str_image', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group{{ $errors->has('sip_image') ? ' has-error' : '' }}">
					{!! Form::label('sip_image', 'Upload Gambar SIP') !!}
					{!! Form::file('sip_image') !!}
						@if (isset($staf) && $staf->sip_image)
							<p> {!! HTML::image(asset($staf->sip_image), null, ['class'=>'img-rounded upload']) !!} </p>
						@else
							<p> {!! HTML::image(asset('img/photo_not_available.png'), null, ['class'=>'img-rounded upload']) !!} </p>
						@endif
					{!! $errors->first('sip_image', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group{{ $errors->has('gambar_npwp') ? ' has-error' : '' }}">
					{!! Form::label('gambar_npwp', 'Upload Gambar Kartu NPWP') !!}
					{!! Form::file('gambar_npwp') !!}
						@if (isset($staf) && $staf->gambar_npwp)
							<p> {!! HTML::image(asset($staf->gambar_npwp), null, ['class'=>'img-rounded upload']) !!} </p>
						@else
							<p> {!! HTML::image(asset('img/photo_not_available.png'), null, ['class'=>'img-rounded upload']) !!} </p>
						@endif
					{!! $errors->first('gambar_npwp', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group{{ $errors->has('surat_nikah') ? ' has-error' : '' }}">
					{!! Form::label('surat_nikah', 'Upload Gambar Surat Nikah') !!}
					{!! Form::file('surat_nikah') !!}
						@if (isset($staf) && $staf->surat_nikah)
							<p> {!! HTML::image(asset($staf->surat_nikah), null, ['class'=>'img-rounded upload']) !!} </p>
						@else
							<p> {!! HTML::image(asset('img/photo_not_available.png'), null, ['class'=>'img-rounded upload']) !!} </p>
						@endif
					{!! $errors->first('surat_nikah', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group{{ $errors->has('kartu_keluarga') ? ' has-error' : '' }}">
					{!! Form::label('kartu_keluarga', 'Upload Gambar Kartu Keluarga') !!}
					{!! Form::file('kartu_keluarga') !!}
						@if (isset($staf) && $staf->kartu_keluarga)
							<p> {!! HTML::image(asset($staf->kartu_keluarga), null, ['class'=>'img-rounded upload']) !!} </p>
						@else
							<p> {!! HTML::image(asset('img/photo_not_available.png'), null, ['class'=>'img-rounded upload']) !!} </p>
						@endif
					{!! $errors->first('kartu_keluarga', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
		</div>
    </div>
</div>
<div class="row">
     <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <div class="form-group">
            {!! Form::submit('Submit', array(
                'class' => 'btn btn-primary block full-width m-b'
            )) !!}
        </div>
    </div>
     <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <div class="form-group">
            {!! HTML::link('stafs', 'Cancel', ['class' => 'btn btn-warning btn-block'])!!}
        </div>
    </div>
</div>
 </div>


